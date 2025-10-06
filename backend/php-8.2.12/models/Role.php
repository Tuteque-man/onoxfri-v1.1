<?php
/**
 * Modelo de Roles y Permisos
 * Sistema de inventario onoxfri
 */

require_once __DIR__ . '/BaseModel.php';

class Role extends BaseModel {
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $fillable = ['name'];

    public function __construct() {
        parent::__construct();
    }

    /**
     * Obtener roles con conteo de usuarios
     */
    public function getWithUserCount() {
        $sql = "SELECT r.*, COUNT(ur.user_id) as user_count
                FROM {$this->table} r
                LEFT JOIN user_roles ur ON r.id = ur.role_id
                GROUP BY r.id, r.name
                ORDER BY r.name";

        return $this->db->select($sql);
    }

    /**
     * Obtener permisos de un rol
     */
    public function getRolePermissions($roleId) {
        $sql = "SELECT p.name, p.description
                FROM permissions p
                JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role_id = ?
                ORDER BY p.name";

        return $this->db->select($sql, [$roleId]);
    }

    /**
     * Asignar permiso a rol
     */
    public function assignPermission($roleId, $permissionId) {
        try {
            // Verificar si ya tiene este permiso
            $sql = "SELECT COUNT(*) as count FROM role_permissions WHERE role_id = ? AND permission_id = ?";
            $result = $this->db->selectOne($sql, [$roleId, $permissionId]);

            if ($result['count'] > 0) {
                return true; // Ya tiene este permiso
            }

            // Asignar nuevo permiso
            $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)";
            return $this->db->insert($sql, [$roleId, $permissionId]);
        } catch (Exception $e) {
            throw new Exception("Error al asignar permiso: " . $e->getMessage());
        }
    }

    /**
     * Remover permiso de rol
     */
    public function removePermission($roleId, $permissionId) {
        try {
            $sql = "DELETE FROM role_permissions WHERE role_id = ? AND permission_id = ?";
            return $this->db->delete($sql, [$roleId, $permissionId]);
        } catch (Exception $e) {
            throw new Exception("Error al remover permiso: " . $e->getMessage());
        }
    }

    /**
     * Obtener usuarios de un rol
     */
    public function getRoleUsers($roleId) {
        $sql = "SELECT u.id, u.name, u.email, u.is_active
                FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                WHERE ur.role_id = ? AND u.is_active = 1
                ORDER BY u.name";

        return $this->db->select($sql, [$roleId]);
    }
}
?>