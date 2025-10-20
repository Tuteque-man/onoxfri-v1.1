<?php
require_once __DIR__ . '/../config.php';

function issue_token(): string {
    return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response(['success' => false, 'message' => 'Method not allowed'], 405);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    $email = trim($input['email'] ?? '');
    $password = (string)($input['password'] ?? '');
    $roleId = (int)($input['roleId'] ?? 0);
    $nombre = trim($input['nombre'] ?? '');
    $apellido = trim($input['apellido'] ?? '');
    $nombre_usuario = trim($input['nombre_usuario'] ?? '');
    $nombre_empresa = trim($input['nombre_empresa'] ?? '');

    if (!$email || !$password || !$roleId || !$nombre || !$apellido || !$nombre_usuario || !$nombre_empresa) {
        json_response(['success' => false, 'message' => 'Faltan campos requeridos'], 400);
    }

    $pdo = db();
    $pdo->beginTransaction();

    // 1) Crear o recuperar empresa por nombre (UNIQUE)
    $stmt = $pdo->prepare('SELECT id FROM empresas WHERE nombre_empresa = ?');
    $stmt->execute([$nombre_empresa]);
    $empresa = $stmt->fetch();

    if (!$empresa) {
        $stmt = $pdo->prepare('INSERT INTO empresas (nombre_empresa) VALUES (?)');
        $stmt->execute([$nombre_empresa]);
        $empresa_id = (int)$pdo->lastInsertId();
    } else {
        $empresa_id = (int)$empresa['id'];
    }

    // 2) Asegurar que la empresa tenga el roleId en empresa_roles (máx 3 via trigger)
    $stmt = $pdo->prepare('SELECT 1 FROM empresa_roles WHERE empresa_id = ? AND role_id = ?');
    $stmt->execute([$empresa_id, $roleId]);
    if (!$stmt->fetch()) {
        // Esto puede fallar si ya hay 3 roles (trigger lo controla)
        $stmtIns = $pdo->prepare('INSERT INTO empresa_roles (empresa_id, role_id) VALUES (?, ?)');
        $stmtIns->execute([$empresa_id, $roleId]);
    }

    // 3) Validaciones de unicidad
    // email
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $pdo->rollBack();
        json_response(['success' => false, 'message' => 'El email ya está registrado'], 409);
    }
    // nombre_usuario
    $stmt = $pdo->prepare('SELECT id FROM users WHERE nombre_usuario = ?');
    $stmt->execute([$nombre_usuario]);
    if ($stmt->fetch()) {
        $pdo->rollBack();
        json_response(['success' => false, 'message' => 'El nombre de usuario ya está registrado'], 409);
    }
    // 1 usuario por (empresa, rol)
    $stmt = $pdo->prepare('SELECT id FROM users WHERE empresa_id = ? AND role_id = ?');
    $stmt->execute([$empresa_id, $roleId]);
    if ($stmt->fetch()) {
        $pdo->rollBack();
        json_response(['success' => false, 'message' => 'Ese rol ya tiene un usuario en esta empresa'], 409);
    }

    // 4) Insert usuario
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO users (email, nombre_empresa, nombre, apellido, nombre_usuario, empresa_id, role_id, password_hash, is_active)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)');
    $stmt->execute([$email, $nombre_empresa, $nombre, $apellido, $nombre_usuario, $empresa_id, $roleId, $hash]);
    $user_id = (int)$pdo->lastInsertId();

    // 5) Obtener nombre del rol
    $stmt = $pdo->prepare('SELECT name FROM roles WHERE id = ?');
    $stmt->execute([$roleId]);
    $roleRow = $stmt->fetch();
    $roleName = $roleRow ? $roleRow['name'] : null;

    $pdo->commit();

    $token = issue_token();
    $user = [
        'id' => $user_id,
        'email' => $email,
        'role' => $roleName,
        'role_id' => $roleId,
        'empresa_id' => $empresa_id,
        'nombre' => $nombre,
        'apellido' => $apellido,
        'nombre_usuario' => $nombre_usuario,
    ];

    json_response(['success' => true, 'token' => $token, 'user' => $user]);
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) { $pdo->rollBack(); }
    json_response(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()], 500);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) { $pdo->rollBack(); }
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
