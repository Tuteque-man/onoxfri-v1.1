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

    // 2) Crear empresa primero (para obtener empresa_id)
    // Detectar si la columna 'fecha_creacion' existe en empresas
    $colStmtFecha = $pdo->prepare('SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $colStmtFecha->execute(['empresas', 'fecha_creacion']);
    $hasFechaCreacion = (bool)$colStmtFecha->fetchColumn();
    
    if ($hasFechaCreacion) {
        $stmt = $pdo->prepare('INSERT INTO empresas (nombre_empresa, descripcion, fecha_creacion) VALUES (?, NULL, NOW())');
        $stmt->execute([$nombre_empresa]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO empresas (nombre_empresa, descripcion) VALUES (?, NULL)');
        $stmt->execute([$nombre_empresa]);
    }
    $empresa_id = (int)$pdo->lastInsertId();

    // 3) Crear registro en empresa_roles (requerido por foreign key constraint)
    // Verificar si la tabla empresa_roles existe
    $tableStmt = $pdo->prepare('SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?');
    $tableStmt->execute(['empresa_roles']);
    $hasEmpresaRolesTable = (bool)$tableStmt->fetchColumn();
    
    if ($hasEmpresaRolesTable) {
        // Insertar o ignorar si ya existe (por si acaso)
        $stmt = $pdo->prepare('INSERT IGNORE INTO empresa_roles (empresa_id, role_id) VALUES (?, ?)');
        $stmt->execute([$empresa_id, $roleId]);
    }

    // 4) Crear usuario con todos los campos requeridos según new_scheme.sql
    $hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Insertar usuario con todos los campos requeridos
    $stmt = $pdo->prepare('
        INSERT INTO users (
            email, nombre_empresa, nombre, apellido, nombre_usuario,
            empresa_id, role_id, password_hash, is_active
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
    ');
    $stmt->execute([
        $email,
        $nombre_empresa,
        $nombre,
        $apellido,
        $nombre_usuario,
        $empresa_id,
        $roleId,
        $hash
    ]);
    $user_id = (int)$pdo->lastInsertId();
    
    // 5) La tabla user_roles no existe en new_scheme.sql
    // El role_id ya está asignado directamente en la tabla users

    $pdo->commit();

    $token = issue_token();
    $user = [
        'id' => $user_id,
        'email' => $email,
        'role' => $roleRow['name'],
        'role_id' => $roleId,
        'empresa_id' => $empresa_id,
        'empresa_nombre' => $nombre_empresa,
        'nombre' => $nombre,
        'apellido' => $apellido,
        'nombre_usuario' => $nombre_usuario,
        'name' => trim($nombre . ' ' . $apellido)
    ];

    json_response(['success' => true, 'token' => $token, 'user' => $user]);
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) { $pdo->rollBack(); }
    json_response(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()], 500);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) { $pdo->rollBack(); }
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
