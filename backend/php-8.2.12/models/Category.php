<?php
/**
 * Modelo de Categoría
 * Sistema de inventario onoxfri
 */

require_once __DIR__ . '/BaseModel.php';

class Category extends BaseModel {
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = ['name'];

    public function __construct() {
        parent::__construct();
    }

    /**
     * Obtener categorías con conteo de productos
     */
    public function getWithProductCount() {
        $sql = "SELECT c.*,
                       COUNT(p.id) as product_count,
                       COUNT(CASE WHEN p.is_active = 1 THEN 1 END) as active_products
                FROM {$this->table} c
                LEFT JOIN products p ON c.id = p.category_id
                GROUP BY c.id, c.name
                ORDER BY c.name";

        return $this->db->select($sql);
    }

    /**
     * Verificar si la categoría tiene productos activos
     */
    public function hasActiveProducts($categoryId) {
        $sql = "SELECT COUNT(*) as count
                FROM products
                WHERE category_id = ? AND is_active = 1";

        $result = $this->db->selectOne($sql, [$categoryId]);
        return $result['count'] > 0;
    }

    /**
     * Obtener productos de la categoría
     */
    public function getProducts($categoryId, $onlyActive = true) {
        $whereClause = $onlyActive ? "AND p.is_active = 1" : "";

        $sql = "SELECT p.*, s.quantity_on_hand, s.reserved
                FROM products p
                LEFT JOIN stock_levels s ON p.id = s.product_id
                WHERE p.category_id = ? {$whereClause}
                ORDER BY p.name";

        return $this->db->select($sql, [$categoryId]);
    }
}
?>