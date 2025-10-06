<?php
/**
 * Modelo de Usuario - Versión Corregida
 * Sistema de inventario onoxfri
 */

require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'email', 'password_hash', 'is_active'];
    protected $hidden = ['password_hash'];
    private $currentUser = null;

    public function __construct($userId = null) {
        parent::__construct();
        if ($userId) {
            $this->loadUser($userId);
        }
    }

    /**
     * Cargar usuario por ID
     */
    public function loadUser($userId) {
        try {
            $this->currentUser = $this->find($userId);
            return $this->currentUser;
        } catch (Exception $e) {
            throw new Exception("Error al cargar usuario: " . $e->getMessage());
        }
    }

    /**
     * Establecer usuario actual
     */
    public function setCurrentUser($user) {
        $this->currentUser = $user;
    }

    /**
     * Obtener usuario actual
     */
    public function getCurrentUser() {
        return $this->currentUser;
    }

    /**
     * Obtener usuario por email con roles
     */
    public function findByEmail($email) {
        try {
            $sql = "SELECT u.*, GROUP_CONCAT(r.name) as role_names
                    FROM {$this->table} u
                    LEFT JOIN user_roles ur ON u.id = ur.user_id
                    LEFT JOIN roles r ON ur.role_id = r.id
                    WHERE u.email = ?
                    GROUP BY u.id";

            return $this->db->selectOne($sql, [$email]);
        } catch (Exception $e) {
            throw new Exception("Error al buscar usuario por email: " . $e->getMessage());
        }
    }

    /**
     * Obtener usuarios activos con roles
     */
    public function getActiveUsers() {
        try {
            $sql = "SELECT u.*, GROUP_CONCAT(r.name) as role_names
                    FROM {$this->table} u
                    LEFT JOIN user_roles ur ON u.id = ur.user_id
                    LEFT JOIN roles r ON ur.role_id = r.id
                    WHERE u.is_active = 1
                    GROUP BY u.id
                    ORDER BY u.name";

            return $this->db->select($sql);
        } catch (Exception $e) {
            throw new Exception("Error al obtener usuarios activos: " . $e->getMessage());
        }
    }

    /**
     * Crear usuario con validaciones completas
     */
    public function createUser($data) {
        try {
            // Validar datos requeridos
            $requiredFields = ['name', 'email', 'password'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    throw new Exception("El campo {$field} es requerido");
                }
            }

            // Validar formato de email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Formato de email inválido");
            }

            // Verificar si el email ya existe
            if ($this->emailExists($data['email'])) {
                throw new Exception("El email ya está registrado");
            }

            // Hash de contraseña
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);

            // Establecer valores por defecto
            $data['is_active'] = $data['is_active'] ?? 1;

            return $this->create($data);
        } catch (Exception $e) {
            throw new Exception("Error al crear usuario: " . $e->getMessage());
        }
    }

    /**
     * Actualizar contraseña con validación
     */
    public function updatePassword($userId, $newPassword) {
        try {
            if (empty($newPassword)) {
                throw new Exception("La contraseña no puede estar vacía");
            }

            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            return $this->update($userId, ['password_hash' => $passwordHash]);
        } catch (Exception $e) {
            throw new Exception("Error al actualizar contraseña: " . $e->getMessage());
        }
    }

    /**
     * Verificar contraseña del usuario actual
     */
    public function verifyPassword($password) {
        if (!$this->currentUser) {
            throw new Exception("No hay usuario cargado");
        }

        if (!isset($this->currentUser['password_hash'])) {
            return false;
        }

        return password_verify($password, $this->currentUser['password_hash']);
    }

    /**
     * Obtener roles del usuario actual
     */
    public function getUserRoles() {
        if (!$this->currentUser) {
            throw new Exception("No hay usuario cargado");
        }

        try {
            $sql = "SELECT r.name, r.id
                    FROM roles r
                    JOIN user_roles ur ON r.id = ur.role_id
                    WHERE ur.user_id = ?
                    ORDER BY r.name";

            return $this->db->select($sql, [$this->currentUser['id']]);
        } catch (Exception $e) {
            throw new Exception("Error al obtener roles del usuario: " . $e->getMessage());
        }
    }

    /**
     * Verificar si usuario tiene un rol específico
     */
    public function hasRole($roleName) {
        if (!$this->currentUser) {
            return false;
        }

        try {
            $sql = "SELECT COUNT(*) as count
                    FROM user_roles ur
                    JOIN roles r ON ur.role_id = r.id
                    WHERE ur.user_id = ? AND r.name = ?";

            $result = $this->db->selectOne($sql, [$this->currentUser['id'], $roleName]);
            return $result['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Asignar rol a usuario (agrega sin eliminar roles existentes)
     */
    public function assignRole($userId, $roleId) {
        try {
            // Verificar si ya tiene este rol
            $sql = "SELECT COUNT(*) as count FROM user_roles WHERE user_id = ? AND role_id = ?";
            $result = $this->db->selectOne($sql, [$userId, $roleId]);

            if ($result['count'] > 0) {
                return true; // Ya tiene este rol
            }

            // Asignar nuevo rol
            $sql = "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)";
            return $this->db->insert($sql, [$userId, $roleId]);
        } catch (Exception $e) {
            throw new Exception("Error al asignar rol: " . $e->getMessage());
        }
    }

    /**
     * Remover rol de usuario
     */
    public function removeRole($userId, $roleId) {
        try {
            $sql = "DELETE FROM user_roles WHERE user_id = ? AND role_id = ?";
            return $this->db->delete($sql, [$userId, $roleId]);
        } catch (Exception $e) {
            throw new Exception("Error al remover rol: " . $e->getMessage());
        }
    }

    /**
     * Reemplazar todos los roles del usuario
     */
    public function setRoles($userId, $roleIds) {
        try {
            $this->db->beginTransaction();

            // Eliminar roles existentes
            $sql = "DELETE FROM user_roles WHERE user_id = ?";
            $this->db->delete($sql, [$userId]);

            // Asignar nuevos roles
            if (!empty($roleIds)) {
                foreach ($roleIds as $roleId) {
                    $sql = "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)";
                    $this->db->insert($sql, [$userId, $roleId]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Error al establecer roles: " . $e->getMessage());
        }
    }

    /**
     * Obtener sesiones del usuario
     */
    public function getSessions($userId) {
        try {
            $sql = "SELECT * FROM user_sessions
                    WHERE user_id = ?
                    ORDER BY login_at DESC";

            return $this->db->select($sql, [$userId]);
        } catch (Exception $e) {
            throw new Exception("Error al obtener sesiones: " . $e->getMessage());
        }
    }

    /**
     * Activar/Desactivar usuario
     */
    public function toggleActive($userId, $active = true) {
        try {
            return $this->update($userId, ['is_active' => $active ? 1 : 0]);
        } catch (Exception $e) {
            throw new Exception("Error al cambiar estado del usuario: " . $e->getMessage());
        }
    }

    /**
     * Verificar si email existe (método auxiliar privado)
     */
    private function emailExists($email, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = ?";
            $params = [$email];

            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }

            $result = $this->db->selectOne($sql, $params);
            return $result['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>