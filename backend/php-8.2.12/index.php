<?php
/**
 * Punto de entrada principal del servidor PHP
 * Sistema de inventario onoxfri
 */

// Habilitar errores para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
session_start();

// Configurar headers para API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Función para obtener la ruta de la URL
function getRoute() {
    $url = $_SERVER['REQUEST_URI'];
    $path = parse_url($url, PHP_URL_PATH);
    $path = str_replace('/onoxfri/backend/php-8.2.12', '', $path);
    $path = trim($path, '/');

    if (empty($path)) {
        return ['index'];
    }

    return explode('/', $path);
}

// Función para enviar respuesta JSON
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Función para manejar errores
function handleError($message, $statusCode = 500) {
    jsonResponse([
        'success' => false,
        'message' => $message,
        'error' => true
    ], $statusCode);
}

// Obtener ruta
$route = getRoute();

// Si no hay ruta específica, mostrar información del servidor
if (empty($route) || $route[0] === 'index') {
    echo json_encode([
        'success' => true,
        'message' => 'Servidor PHP del sistema de inventario onoxfri',
        'version' => '1.0.0',
        'status' => 'running',
        'routes' => [
            'GET /' => 'Información del servidor',
            'GET /test-db' => 'Probar conexión a base de datos',
            'POST /api/auth/login' => 'Iniciar sesión',
            'GET /api/products' => 'Listar productos',
            'GET /api/categories' => 'Listar categorías'
        ],
        'documentation' => 'Ver README.md para más información'
    ]);
    exit;
}

// Probar conexión a base de datos
if ($route[0] === 'test-db') {
    try {
        require_once 'config/database.php';
        $db = getDB();

        // Probar consulta simple
        $result = $db->selectOne("SELECT 1 as test");

        if ($result && $result['test'] == 1) {
            jsonResponse([
                'success' => true,
                'message' => 'Conexión a base de datos exitosa',
                'database' => getConfig('database.database'),
                'host' => getConfig('database.host')
            ]);
        } else {
            handleError('Error en consulta de prueba');
        }
    } catch (Exception $e) {
        handleError('Error de conexión a base de datos: ' . $e->getMessage());
    }
}

// Rutas de la API
if (isset($route[0]) && $route[0] === 'api') {
    try {
        // Incluir archivos necesarios
        require_once 'config/database.php';

        // Rutas de autenticación
        if (isset($route[1]) && $route[1] === 'auth') {
            require_once 'controller/AuthController.php';
            $authController = new AuthController();
            $authController->handleRequest();
        }

        // Rutas de productos
        elseif (isset($route[1]) && $route[1] === 'products') {
            require_once 'controller/ProductController.php';
            $productController = new ProductController();
            $productController->handleRequest();
        }

        // Rutas de categorías
        elseif (isset($route[1]) && $route[1] === 'categories') {
            require_once 'controller/CategoryController.php';
            $categoryController = new CategoryController();
            $categoryController->handleRequest();
        }

        // Ruta no encontrada
        else {
            handleError('Ruta de API no encontrada', 404);
        }

    } catch (Exception $e) {
        handleError('Error interno del servidor: ' . $e->getMessage());
    }
}

// Ruta no encontrada
handleError('Ruta no encontrada', 404);
?>