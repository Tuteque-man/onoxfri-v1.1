<?php
/**
 * Modelo de Cliente
 * Sistema de inventario onoxfri
 */

require_once __DIR__ . '/BaseModel.php';

class Customer extends BaseModel {
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'email', 'phone', 'address'];

    public function __construct() {
        parent::__construct();
    }

    /**
     * Buscar clientes por nombre o email
     */
    public function search($term) {
        $sql = "SELECT * FROM {$this->table}
                WHERE name LIKE ? OR email LIKE ?
                ORDER BY name";

        $searchTerm = "%{$term}%";
        return $this->db->select($sql, [$searchTerm, $searchTerm]);
    }

    /**
     * Obtener clientes recientes
     */
    public function getRecent($limit = 10) {
        $sql = "SELECT * FROM {$this->table}
                ORDER BY created_at DESC LIMIT ?";

        return $this->db->select($sql, [$limit]);
    }

    /**
     * Verificar si el email ya existe
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = ?";
        $params = [$email];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $result = $this->db->selectOne($sql, $params);
        return $result['count'] > 0;
    }
}
?>