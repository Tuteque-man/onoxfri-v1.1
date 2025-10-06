<?php
/**
 * Controlador base para todas las operaciones CRUD
 * Sistema de inventario onoxfri
 */

require_once __DIR__ . '/../middleware/AuthMiddleware.php';

abstract class BaseController {
    protected $auth;
    protected $model;
    protected $modelName;

    public function __construct() {
        $this->auth = getAuth();

        // Inicializar modelo basado en el nombre de la clase hija
        $this->initializeModel();
    }

    /**
     * Inicializar modelo basado en el nombre del controlador
     */
    private function initializeModel() {
        $className = get_class($this);
        $modelName = str_replace('Controller', '', $className);

        $modelFile = __DIR__ . '/../models/' . $modelName . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            $this->modelName = $modelName;
            $this->model = new $modelName();
        }
    }

    /**
     * Manejar respuestas JSON
     */
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Respuesta de éxito
     */
    protected function successResponse($message, $data = null) {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        $this->jsonResponse($response);
    }

    /**
     * Respuesta de error
     */
    protected function errorResponse($message, $statusCode = 400, $errors = null) {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        $this->jsonResponse($response, $statusCode);
    }

    /**
     * Obtener datos del request
     */
    protected function getRequestData() {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? $_POST ?? [];
    }

    /**
     * Validar datos requeridos
     */
    protected function validateRequired($data, $fields) {
        $errors = [];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = "El campo {$field} es requerido";
            }
        }

        return $errors;
    }

    /**
     * Sanitizar datos de entrada
     */
    protected function sanitizeInput($data) {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Verificar autenticación
     */
    protected function requireAuth() {
        $this->auth->requireAuth();
    }

    /**
     * Verificar permisos
     */
    protected function requirePermission($permission) {
        $this->auth->requirePermission($permission);
    }

    /**
     * Obtener parámetro de URL
     */
    protected function getUrlParam($index) {
        $url = $_SERVER['REQUEST_URI'];
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));

        return $segments[$index] ?? null;
    }

    /**
     * Obtener método HTTP
     */
    protected function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Registrar acción en el log de auditoría
     */
    protected function logAction($action, $payload = null) {
        if (!$this->auth->isAuthenticated()) {
            return;
        }

        $user = $this->auth->getCurrentUser();
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        $sql = "INSERT INTO audit_logs (user_id, action, payload, ip, created_at)
                VALUES (?, ?, ?, ?, NOW())";

        $this->model->query($sql, [$user['id'], $action, $payload, $ip]);
    }
}
?>