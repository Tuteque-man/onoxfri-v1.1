<?php
/**
 * Modelo de Producto
 * Sistema de inventario onoxfri
 */

require_once __DIR__ . '/BaseModel.php';

class Product extends BaseModel {
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = ['sku', 'name', 'category_id', 'price', 'cost', 'min_stock', 'is_active'];

    public function __construct() {
        parent::__construct();
    }

    /**
     * Obtener productos activos con información de stock
     */
    public function getActiveWithStock() {
        $sql = "SELECT p.*, c.name as category_name, s.quantity_on_hand, s.reserved
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN stock_levels s ON p.id = s.product_id
                WHERE p.is_active = 1
                ORDER BY p.name";

        return $this->db->select($sql);
    }

    /**
     * Obtener producto por SKU
     */
    public function findBySku($sku) {
        $sql = "SELECT p.*, c.name as category_name, s.quantity_on_hand, s.reserved
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN stock_levels s ON p.id = s.product_id
                WHERE p.sku = ?";

        return $this->db->selectOne($sql, [$sku]);
    }

    /**
     * Buscar productos por nombre o SKU
     */
    public function search($term) {
        $sql = "SELECT p.*, c.name as category_name, s.quantity_on_hand, s.reserved
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN stock_levels s ON p.id = s.product_id
                WHERE p.is_active = 1
                AND (p.name LIKE ? OR p.sku LIKE ?)
                ORDER BY p.name";

        $searchTerm = "%{$term}%";
        return $this->db->select($sql, [$searchTerm, $searchTerm]);
    }

    /**
     * Obtener productos por categoría
     */
    public function getByCategory($categoryId) {
        $sql = "SELECT p.*, c.name as category_name, s.quantity_on_hand, s.reserved
                FROM {$this->table} p
                JOIN categories c ON p.category_id = c.id
                LEFT JOIN stock_levels s ON p.id = s.product_id
                WHERE p.category_id = ? AND p.is_active = 1
                ORDER BY p.name";

        return $this->db->select($sql, [$categoryId]);
    }

    /**
     * Obtener productos con stock bajo
     */
    public function getLowStock() {
        $sql = "SELECT p.*, c.name as category_name, s.quantity_on_hand, s.reserved, p.min_stock
                FROM {$this->table} p
                JOIN categories c ON p.category_id = c.id
                JOIN stock_levels s ON p.id = s.product_id
                WHERE p.is_active = 1
                AND s.quantity_on_hand < p.min_stock
                ORDER BY (s.quantity_on_hand - p.min_stock)";

        return $this->db->select($sql);
    }

    /**
     * Actualizar stock del producto
     */
    public function updateStock($productId, $quantity, $type = 'adjust', $reference = null, $userId = null) {
        try {
            $this->db->beginTransaction();

            // Insertar movimiento de inventario
            $sql = "INSERT INTO inventory_movements (product_id, type, qty, reference, created_by, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())";

            $this->db->insert($sql, [$productId, $type, $quantity, $reference, $userId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Obtener historial de precios
     */
    public function getPriceHistory($productId) {
        $sql = "SELECT ph.*, u.name as user_name
                FROM price_history ph
                LEFT JOIN users u ON ph.user_id = u.id
                WHERE ph.product_id = ?
                ORDER BY ph.created_at DESC";

        return $this->db->select($sql, [$productId]);
    }

    /**
     * Actualizar precio del producto
     */
    public function updatePrice($productId, $newPrice, $reason = null, $userId = null) {
        try {
            $this->db->beginTransaction();

            // Obtener precio actual
            $currentProduct = $this->find($productId);
            $oldPrice = $currentProduct['price'];

            // Actualizar precio
            $this->update($productId, ['price' => $newPrice]);

            // Registrar en historial
            $sql = "INSERT INTO price_history (product_id, old_price, new_price, reason, user_id, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())";

            $this->db->insert($sql, [$productId, $oldPrice, $newPrice, $reason, $userId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Obtener valor total del inventario
     */
    public function getInventoryValue() {
        $sql = "SELECT
                    SUM(s.quantity_on_hand * p.price) as total_price_value,
                    SUM(s.quantity_on_hand * p.cost) as total_cost_value,
                    COUNT(p.id) as total_products
                FROM {$this->table} p
                JOIN stock_levels s ON p.id = s.product_id
                WHERE p.is_active = 1";

        return $this->db->selectOne($sql);
    }

    /**
     * Obtener estadísticas por categoría
     */
    public function getStatsByCategory() {
        $sql = "SELECT
                    c.id, c.name,
                    COUNT(p.id) as product_count,
                    SUM(s.quantity_on_hand) as total_stock,
                    SUM(s.quantity_on_hand * p.price) as total_value
                FROM categories c
                LEFT JOIN {$this->table} p ON c.id = p.category_id AND p.is_active = 1
                LEFT JOIN stock_levels s ON p.id = s.product_id
                GROUP BY c.id, c.name
                ORDER BY total_value DESC";

        return $this->db->select($sql);
    }
}
?>