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

    if (!$email || !$password || !$roleId || !$nombre || !$apellido || !$nombre_empresa) {
        json_response(['success' => false, 'message' => 'Faltan campos requeridos'], 400);
    }

    $pdo = db();
    $pdo->beginTransaction();

    // 1) Validaciones
    // email único
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $pdo->rollBack();
        json_response(['success' => false, 'message' => 'El email ya está registrado'], 409);
    }
    // rol válido
    $stmt = $pdo->prepare('SELECT name FROM roles WHERE id = ?');
    $stmt->execute([$roleId]);
    $roleRow = $stmt->fetch();
    if (!$roleRow) {
        $pdo->rollBack();
        json_response(['success' => false, 'message' => 'Rol inválido'], 400);
    }

    // 2) Crear usuario
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $fullName = trim($nombre . ' ' . $apellido);
    if ($fullName === '') { $fullName = $nombre ?: $apellido; }
    // Detectar si la columna 'name' existe en la tabla users
    $colStmt = $pdo->prepare('SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $colStmt->execute([DB_NAME, 'users', 'name']);
    $hasNameColumn = (bool)$colStmt->fetchColumn();

    if ($hasNameColumn) {
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, is_active) VALUES (?, ?, ?, 1)');
        $stmt->execute([$fullName, $email, $hash]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, is_active) VALUES (?, ?, 1)');
        $stmt->execute([$email, $hash]);
    }
    $user_id = (int)$pdo->lastInsertId();

    // 3) Crear empresa y asociarla al usuario como propietario (usuario_id)
    // Detectar si la columna empresas.usuario_id existe
    $colStmt2 = $pdo->prepare('SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $colStmt2->execute([DB_NAME, 'empresas', 'usuario_id']);
    $hasUsuarioIdInEmpresas = (bool)$colStmt2->fetchColumn();

    if ($hasUsuarioIdInEmpresas) {
        $stmt = $pdo->prepare('INSERT INTO empresas (nombre_empresa, descripcion, usuario_id, fecha_creacion) VALUES (?, NULL, ?, NOW())');
        $stmt->execute([$nombre_empresa, $user_id]);
    } else {
        // Insert sin relacion directa; se puede enlazar luego por otra vía
        $stmt = $pdo->prepare('INSERT INTO empresas (nombre_empresa, descripcion) VALUES (?, NULL)');
        $stmt->execute([$nombre_empresa]);
    }
    $empresa_id = (int)$pdo->lastInsertId();

    // 4) Asignar rol al usuario
    $stmt = $pdo->prepare('INSERT INTO user_roles (user_id, role_id, assigned_at) VALUES (?, ?, NOW())');
    $stmt->execute([$user_id, $roleId]);

    $pdo->commit();

    $token = issue_token();
    $user = [
        'id' => $user_id,
        'email' => $email,
        'role' => $roleRow['name'],
        'role_id' => $roleId,
        'empresa_id' => $empresa_id,
        'empresa_nombre' => $nombre_empresa,
        'name' => $hasNameColumn ? $fullName : (strpos($email, '@') !== false ? substr($email, 0, strpos($email, '@')) : $email),
        // Campos del formulario devueltos por compatibilidad
        'nombre' => $nombre,
        'apellido' => $apellido,
        'nombre_usuario' => $nombre_usuario ?: null,
    ];

    json_response(['success' => true, 'token' => $token, 'user' => $user]);
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) { $pdo->rollBack(); }
    json_response(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()], 500);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) { $pdo->rollBack(); }
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
