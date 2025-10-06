<?php
/**
 * Controlador de Categorías
 * Sistema de inventario onoxfri
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Category.php';

class CategoryController extends BaseController {
    private $categoryModel;

    public function __construct() {
        parent::__construct();
        $this->categoryModel = new Category();
    }

    /**
     * Manejar rutas del controlador
     */
    public function handleRequest() {
        $method = $this->getMethod();
        $action = $this->getUrlParam(3) ?? 'index'; // /api/categories/{action}

        switch ($method) {
            case 'GET':
                $this->handleGet($action);
                break;
            case 'POST':
                $this->handlePost($action);
                break;
            case 'PUT':
                $this->handlePut($action);
                break;
            case 'DELETE':
                $this->handleDelete($action);
                break;
            default:
                $this->errorResponse('Método no permitido', 405);
        }
    }

    /**
     * Manejar peticiones GET
     */
    private function handleGet($action) {
        $this->requireAuth();

        switch ($action) {
            case 'index':
                $this->getAllCategories();
                break;
            case 'with-count':
                $this->getCategoriesWithCount();
                break;
            default:
                if (is_numeric($action)) {
                    $this->getCategory($action);
                } else {
                    $this->errorResponse('Acción no encontrada', 404);
                }
        }
    }

    /**
     * Manejar peticiones POST
     */
    private function handlePost($action) {
        $this->requireAuth();

        if ($action === 'create') {
            $this->createCategory();
        } else {
            $this->errorResponse('Acción no encontrada', 404);
        }
    }

    /**
     * Manejar peticiones PUT
     */
    private function handlePut($action) {
        $this->requireAuth();

        if (is_numeric($action)) {
            $this->updateCategory($action);
        } else {
            $this->errorResponse('ID de categoría requerido', 400);
        }
    }

    /**
     * Manejar peticiones DELETE
     */
    private function handleDelete($action) {
        $this->requireAuth();

        if (is_numeric($action)) {
            $this->deleteCategory($action);
        } else {
            $this->errorResponse('ID de categoría requerido', 400);
        }
    }

    /**
     * Obtener todas las categorías
     */
    private function getAllCategories() {
        try {
            $categories = $this->categoryModel->all();
            $this->successResponse('Categorías obtenidas exitosamente', $categories);
        } catch (Exception $e) {
            $this->errorResponse('Error al obtener categorías: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener categorías con conteo de productos
     */
    private function getCategoriesWithCount() {
        try {
            $categories = $this->categoryModel->getWithProductCount();
            $this->successResponse('Categorías obtenidas exitosamente', $categories);
        } catch (Exception $e) {
            $this->errorResponse('Error al obtener categorías: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener categoría específica
     */
    private function getCategory($id) {
        try {
            $category = $this->categoryModel->find($id);

            if (!$category) {
                $this->errorResponse('Categoría no encontrada', 404);
                return;
            }

            $this->successResponse('Categoría obtenida exitosamente', $category);
        } catch (Exception $e) {
            $this->errorResponse('Error al obtener categoría: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Crear nueva categoría
     */
    private function createCategory() {
        try {
            $data = $this->getRequestData();
            $data = $this->sanitizeInput($data);

            // Validar campos requeridos
            $requiredFields = ['name'];
            $errors = $this->validateRequired($data, $requiredFields);

            if (!empty($errors)) {
                $this->errorResponse('Datos de validación fallaron', 422, $errors);
                return;
            }

            $category = $this->categoryModel->create($data);

            $this->logAction('CREATE_CATEGORY', json_encode($category));
            $this->successResponse('Categoría creada exitosamente', $category);
        } catch (Exception $e) {
            $this->errorResponse('Error al crear categoría: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Actualizar categoría
     */
    private function updateCategory($id) {
        try {
            $data = $this->getRequestData();
            $data = $this->sanitizeInput($data);

            $category = $this->categoryModel->find($id);
            if (!$category) {
                $this->errorResponse('Categoría no encontrada', 404);
                return;
            }

            $updatedCategory = $this->categoryModel->update($id, $data);

            $this->logAction('UPDATE_CATEGORY', json_encode(['id' => $id, 'changes' => $data]));
            $this->successResponse('Categoría actualizada exitosamente', $updatedCategory);
        } catch (Exception $e) {
            $this->errorResponse('Error al actualizar categoría: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Eliminar categoría
     */
    private function deleteCategory($id) {
        try {
            $category = $this->categoryModel->find($id);
            if (!$category) {
                $this->errorResponse('Categoría no encontrada', 404);
                return;
            }

            // Verificar si tiene productos activos
            if ($this->categoryModel->hasActiveProducts($id)) {
                $this->errorResponse('No se puede eliminar categoría con productos activos', 422);
                return;
            }

            $this->categoryModel->delete($id);

            $this->logAction('DELETE_CATEGORY', json_encode($category));
            $this->successResponse('Categoría eliminada exitosamente');
        } catch (Exception $e) {
            $this->errorResponse('Error al eliminar categoría: ' . $e->getMessage(), 500);
        }
    }
}
?>