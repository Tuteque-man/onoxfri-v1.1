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

    if (!$email || !$password) {
        json_response(['success' => false, 'message' => 'Email y contraseña son requeridos'], 400);
    }

    $pdo = db();

    $stmt = $pdo->prepare('SELECT u.id, u.email, u.password_hash, u.is_active, u.role_id, u.empresa_id, u.nombre, u.apellido, u.nombre_usuario, r.name AS role_name
                           FROM users u
                           JOIN roles r ON r.id = u.role_id
                           WHERE u.email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        json_response(['success' => false, 'message' => 'Credenciales inválidas'], 401);
    }

    if (!$user['is_active']) {
        json_response(['success' => false, 'message' => 'Usuario inactivo'], 403);
    }

    if (!password_verify($password, $user['password_hash'])) {
        json_response(['success' => false, 'message' => 'Credenciales inválidas'], 401);
    }

    $token = issue_token();

    $respUser = [
        'id' => (int)$user['id'],
        'email' => $user['email'],
        'role' => $user['role_name'],
        'role_id' => (int)$user['role_id'],
        'empresa_id' => (int)$user['empresa_id'],
        'nombre' => $user['nombre'],
        'apellido' => $user['apellido'],
        'nombre_usuario' => $user['nombre_usuario'],
    ];

    json_response(['success' => true, 'token' => $token, 'user' => $respUser]);
} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()], 500);
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
