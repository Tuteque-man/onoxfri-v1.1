<?php
/**
 * Controlador de Autenticación
 * Sistema de inventario onoxfri
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Manejar rutas del controlador
     */
    public function handleRequest() {
        $method = $this->getMethod();
        $action = $this->getUrlParam(3) ?? 'login'; // /api/auth/{action}

        switch ($method) {
            case 'POST':
                $this->handlePost($action);
                break;
            case 'DELETE':
                $this->handleDelete($action);
                break;
            default:
                $this->errorResponse('Método no permitido', 405);
        }
    }

    /**
     * Manejar peticiones POST
     */
    private function handlePost($action) {
        switch ($action) {
            case 'login':
                $this->login();
                break;
            case 'register':
                $this->register();
                break;
            default:
                $this->errorResponse('Acción no encontrada', 404);
        }
    }

    /**
     * Manejar peticiones DELETE
     */
    private function handleDelete($action) {
        if ($action === 'logout') {
            $this->logout();
        } else {
            $this->errorResponse('Acción no encontrada', 404);
        }
    }

    /**
     * Iniciar sesión
     */
    private function login() {
        try {
            $data = $this->getRequestData();

            // Validar campos requeridos
            $requiredFields = ['email', 'password'];
            $errors = $this->validateRequired($data, $requiredFields);

            if (!empty($errors)) {
                $this->errorResponse('Datos de validación fallaron', 422, $errors);
                return;
            }

            // Intentar login
            if ($this->auth->login($data['email'], $data['password'])) {
                $user = $this->auth->getCurrentUser();

                $responseData = [
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role_name']
                    ],
                    'message' => 'Inicio de sesión exitoso'
                ];

                $this->logAction('USER_LOGIN', json_encode(['email' => $data['email']]));
                $this->successResponse('Inicio de sesión exitoso', $responseData);
            } else {
                $this->errorResponse('Credenciales inválidas', 401);
            }
        } catch (Exception $e) {
            $this->errorResponse('Error en el inicio de sesión: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cerrar sesión
     */
    private function logout() {
        try {
            $user = $this->auth->getCurrentUser();

            if ($user) {
                $this->logAction('USER_LOGOUT', json_encode(['user_id' => $user['id']]));
            }

            $this->auth->logout();
            $this->successResponse('Sesión cerrada exitosamente');
        } catch (Exception $e) {
            $this->errorResponse('Error al cerrar sesión: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Registrar nuevo usuario
     */
    private function register() {
        try {
            $data = $this->getRequestData();
            $data = $this->sanitizeInput($data);

            // Validar campos requeridos
            $requiredFields = ['name', 'email', 'password'];
            $errors = $this->validateRequired($data, $requiredFields);

            if (!empty($errors)) {
                $this->errorResponse('Datos de validación fallaron', 422, $errors);
                return;
            }

            // Validar formato de email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->errorResponse('Formato de email inválido', 422);
                return;
            }

            // Verificar si el email ya existe
            if ($this->userModel->findByEmail($data['email'])) {
                $this->errorResponse('El email ya está registrado', 422);
                return;
            }

            // Crear usuario
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'is_active' => 1
            ];

            $user = $this->userModel->createUser($userData);

            // Asignar rol por defecto (Asistente)
            $this->userModel->assignRole($user['id'], 3);

            $this->logAction('USER_REGISTER', json_encode(['email' => $data['email']]));
            $this->successResponse('Usuario registrado exitosamente', [
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ]
            ]);
        } catch (Exception $e) {
            $this->errorResponse('Error al registrar usuario: ' . $e->getMessage(), 500);
        }
    }
}
?>