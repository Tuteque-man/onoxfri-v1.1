<?php
/**
 * Controlador de Productos
 * Sistema de inventario onoxfri
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Product.php';

class ProductController extends BaseController {
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
    }

    /**
     * Manejar rutas del controlador
     */
    public function handleRequest() {
        $method = $this->getMethod();
        $action = $this->getUrlParam(3) ?? 'index'; // /api/products/{action}

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
                $this->getAllProducts();
                break;
            case 'active':
                $this->getActiveProducts();
                break;
            case 'low-stock':
                $this->getLowStockProducts();
                break;
            case 'search':
                $this->searchProducts();
                break;
            case 'stats':
                $this->getProductStats();
                break;
            default:
                if (is_numeric($action)) {
                    $this->getProduct($action);
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

        switch ($action) {
            case 'create':
                $this->createProduct();
                break;
            case 'update-stock':
                $this->updateStock();
                break;
            case 'update-price':
                $this->updatePrice();
                break;
            default:
                $this->errorResponse('Acción no encontrada', 404);
        }
    }

    /**
     * Manejar peticiones PUT
     */
    private function handlePut($action) {
        $this->requireAuth();

        if (is_numeric($action)) {
            $this->updateProduct($action);
        } else {
            $this->errorResponse('ID de producto requerido', 400);
        }
    }

    /**
     * Manejar peticiones DELETE
     */
    private function handleDelete($action) {
        $this->requireAuth();

        if (is_numeric($action)) {
            $this->deleteProduct($action);
        } else {
            $this->errorResponse('ID de producto requerido', 400);
        }
    }

    /**
     * Obtener todos los productos
     */
    private function getAllProducts() {
        try {
            $products = $this->productModel->all();
            $this->successResponse('Productos obtenidos exitosamente', $products);
        } catch (Exception $e) {
            $this->errorResponse('Error al obtener productos: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener productos activos
     */
    private function getActiveProducts() {
        try {
            $products = $this->productModel->getActiveWithStock();
            $this->successResponse('Productos activos obtenidos exitosamente', $products);
        } catch (Exception $e) {
            $this->errorResponse('Error al obtener productos activos: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener producto específico
     */
    private function getProduct($id) {
        try {
            $product = $this->productModel->find($id);

            if (!$product) {
                $this->errorResponse('Producto no encontrado', 404);
                return;
            }

            $this->successResponse('Producto obtenido exitosamente', $product);
        } catch (Exception $e) {
            $this->errorResponse('Error al obtener producto: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Buscar productos
     */
    private function searchProducts() {
        try {
            $term = $_GET['q'] ?? '';

            if (empty($term)) {
                $this->errorResponse('Término de búsqueda requerido', 400);
                return;
            }

            $products = $this->productModel->search($term);
            $this->successResponse('Búsqueda completada', $products);
        } catch (Exception $e) {
            $this->errorResponse('Error en la búsqueda: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener productos con stock bajo
     */
    private function getLowStockProducts() {
        try {
            $products = $this->productModel->getLowStock();
            $this->successResponse('Productos con stock bajo obtenidos', $products);
        } catch (Exception $e) {
            $this->errorResponse('Error al obtener productos con stock bajo: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Crear nuevo producto
     */
    private function createProduct() {
        try {
            $data = $this->getRequestData();
            $data = $this->sanitizeInput($data);

            // Validar campos requeridos
            $requiredFields = ['sku', 'name', 'price', 'cost'];
            $errors = $this->validateRequired($data, $requiredFields);

            if (!empty($errors)) {
                $this->errorResponse('Datos de validación fallaron', 422, $errors);
                return;
            }

            // Verificar SKU único
            $existingProduct = $this->productModel->findBySku($data['sku']);
            if ($existingProduct) {
                $this->errorResponse('El SKU ya existe', 422);
                return;
            }

            // Establecer valores por defecto
            $data['is_active'] = $data['is_active'] ?? 1;
            $data['min_stock'] = $data['min_stock'] ?? 0;

            $product = $this->productModel->create($data);

            // Inicializar stock
            if (isset($data['initial_stock'])) {
                $this->productModel->updateStock(
                    $product['id'],
                    $data['initial_stock'],
                    'in',
                    'Stock inicial',
                    $this->auth->getCurrentUser()['id']
                );
            }

            $this->logAction('CREATE_PRODUCT', json_encode($product));
            $this->successResponse('Producto creado exitosamente', $product);
        } catch (Exception $e) {
            $this->errorResponse('Error al crear producto: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Actualizar producto
     */
    private function updateProduct($id) {
        try {
            $data = $this->getRequestData();
            $data = $this->sanitizeInput($data);

            $product = $this->productModel->find($id);
            if (!$product) {
                $this->errorResponse('Producto no encontrado', 404);
                return;
            }

            // Verificar SKU único si se está actualizando
            if (isset($data['sku'])) {
                $existingProduct = $this->productModel->findBySku($data['sku']);
                if ($existingProduct && $existingProduct['id'] != $id) {
                    $this->errorResponse('El SKU ya existe', 422);
                    return;
                }
            }

            $updatedProduct = $this->productModel->update($id, $data);

            $this->logAction('UPDATE_PRODUCT', json_encode(['id' => $id, 'changes' => $data]));
            $this->successResponse('Producto actualizado exitosamente', $updatedProduct);
        } catch (Exception $e) {
            $this->errorResponse('Error al actualizar producto: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Actualizar stock
     */
    private function updateStock() {
        try {
            $data = $this->getRequestData();

            $requiredFields = ['product_id', 'quantity', 'type'];
            $errors = $this->validateRequired($data, $requiredFields);

            if (!empty($errors)) {
                $this->errorResponse('Datos de validación fallaron', 422, $errors);
                return;
            }

            $product = $this->productModel->find($data['product_id']);
            if (!$product) {
                $this->errorResponse('Producto no encontrado', 404);
                return;
            }

            $this->productModel->updateStock(
                $data['product_id'],
                $data['quantity'],
                $data['type'],
                $data['reference'] ?? null,
                $this->auth->getCurrentUser()['id']
            );

            $this->logAction('UPDATE_STOCK', json_encode($data));
            $this->successResponse('Stock actualizado exitosamente');
        } catch (Exception $e) {
            $this->errorResponse('Error al actualizar stock: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Actualizar precio
     */
    private function updatePrice() {
        try {
            $data = $this->getRequestData();

            $requiredFields = ['product_id', 'new_price'];
            $errors = $this->validateRequired($data, $requiredFields);

            if (!empty($errors)) {
                $this->errorResponse('Datos de validación fallaron', 422, $errors);
                return;
            }

            $product = $this->productModel->find($data['product_id']);
            if (!$product) {
                $this->errorResponse('Producto no encontrado', 404);
                return;
            }

            $this->productModel->updatePrice(
                $data['product_id'],
                $data['new_price'],
                $data['reason'] ?? null,
                $this->auth->getCurrentUser()['id']
            );

            $this->logAction('UPDATE_PRICE', json_encode($data));
            $this->successResponse('Precio actualizado exitosamente');
        } catch (Exception $e) {
            $this->errorResponse('Error al actualizar precio: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Eliminar producto
     */
    private function deleteProduct($id) {
        try {
            $product = $this->productModel->find($id);
            if (!$product) {
                $this->errorResponse('Producto no encontrado', 404);
                return;
            }

            $this->productModel->delete($id);

            $this->logAction('DELETE_PRODUCT', json_encode($product));
            $this->successResponse('Producto eliminado exitosamente');
        } catch (Exception $e) {
            $this->errorResponse('Error al eliminar producto: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener estadísticas de productos
     */
    private function getProductStats() {
        try {
            $stats = [
                'inventory_value' => $this->productModel->getInventoryValue(),
                'stats_by_category' => $this->productModel->getStatsByCategory(),
                'low_stock_count' => count($this->productModel->getLowStock())
            ];

            $this->successResponse('Estadísticas obtenidas exitosamente', $stats);
        } catch (Exception $e) {
            $this->errorResponse('Error al obtener estadísticas: ' . $e->getMessage(), 500);
        }
    }
}
?>