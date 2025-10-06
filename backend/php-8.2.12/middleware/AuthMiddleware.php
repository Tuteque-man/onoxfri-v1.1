<?php
/**
 * Middleware básico de autenticación y autorización
 * Sistema de inventario onoxfri
 */

require_once __DIR__ . '/../config/database.php';

class AuthMiddleware {
    private $db;
    private $user = null;

    public function __construct() {
        $this->db = getDB();
        $this->checkSession();
    }

    /**
     * Verificar si hay una sesión activa
     */
    private function checkSession() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $userId = $_SESSION['user_id'];
        $user = $this->getUserById($userId);

        if ($user && $user['is_active']) {
            $this->user = $user;
            return true;
        }

        return false;
    }

    /**
     * Obtener usuario por ID con roles
     */
    private function getUserById($userId) {
        $sql = "SELECT u.*, GROUP_CONCAT(r.name) as role_names
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.id = ? AND u.is_active = 1
                GROUP BY u.id";

        return $this->db->selectOne($sql, [$userId]);
    }

    /**
     * Iniciar sesión de usuario
     */
    public function login($email, $password) {
        $sql = "SELECT u.*, r.name as role_name
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.email = ? AND u.is_active = 1";

        $user = $this->db->selectOne($sql, [$email]);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role_name'];

            // Registrar sesión en la base de datos
            $this->logSession($user['id']);

            $this->user = $user;
            return true;
        }

        return false;
    }

    /**
     * Cerrar sesión
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->updateSessionLogout($_SESSION['user_id']);
        }

        session_unset();
        session_destroy();
        $this->user = null;
    }

    /**
     * Verificar si el usuario está autenticado
     */
    public function isAuthenticated() {
        return $this->user !== null;
    }

    /**
     * Obtener usuario actual
     */
    public function getCurrentUser() {
        return $this->user;
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole($role) {
        if (!$this->user) {
            return false;
        }

        // Si role_names es una cadena separada por comas (GROUP_CONCAT)
        if (isset($this->user['role_names'])) {
            $roles = explode(',', $this->user['role_names']);
            return in_array($role, $roles);
        }

        // Fallback para compatibilidad
        return strtolower($this->user['role_name'] ?? '') === strtolower($role);
    }

    /**
     * Verificar si el usuario tiene permiso específico
     */
    public function hasPermission($permission) {
        if (!$this->user) {
            return false;
        }

        $sql = "SELECT COUNT(*) as count
                FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN role_permissions rp ON ur.role_id = rp.role_id
                JOIN permissions p ON rp.permission_id = p.id
                WHERE u.id = ? AND p.name = ?";

        $result = $this->db->selectOne($sql, [$this->user['id'], $permission]);
        return $result['count'] > 0;
    }

    /**
     * Middleware para proteger rutas
     */
    public function requireAuth() {
        if (!$this->isAuthenticated()) {
            $this->returnUnauthorized();
        }
    }

    /**
     * Middleware para verificar roles
     */
    public function requireRole($role) {
        $this->requireAuth();

        if (!$this->hasRole($role)) {
            $this->returnForbidden();
        }
    }

    /**
     * Middleware para verificar permisos
     */
    public function requirePermission($permission) {
        $this->requireAuth();

        if (!$this->hasPermission($permission)) {
            $this->returnForbidden();
        }
    }

    /**
     * Registrar inicio de sesión
     */
    private function logSession($userId) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $sql = "INSERT INTO user_sessions (user_id, login_at, ip, user_agent)
                VALUES (?, NOW(), ?, ?)";

        $this->db->insert($sql, [$userId, $ip, $userAgent]);
    }

    /**
     * Actualizar cierre de sesión
     */
    private function updateSessionLogout($userId) {
        $sql = "UPDATE user_sessions
                SET logout_at = NOW()
                WHERE user_id = ? AND logout_at IS NULL
                ORDER BY login_at DESC LIMIT 1";

        $this->db->update($sql, [$userId]);
    }

    /**
     * Retornar respuesta de no autorizado
     */
    private function returnUnauthorized() {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Acceso no autorizado'
        ]);
        exit;
    }

    /**
     * Retornar respuesta de acceso prohibido
     */
    private function returnForbidden() {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Acceso prohibido'
        ]);
        exit;
    }
}

// Función helper para obtener instancia de auth
function getAuth() {
    static $auth = null;
    if ($auth === null) {
        $auth = new AuthMiddleware();
    }
    return $auth;
}
?>