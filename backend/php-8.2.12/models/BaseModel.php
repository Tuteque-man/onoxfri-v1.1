<?php
/**
 * Modelo base para todas las entidades
 * Sistema de inventario onoxfri
 */

require_once __DIR__ . '/../config/database.php';

abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener todos los registros
     */
    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->select($sql);
    }

    /**
     * Obtener registro por ID
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->selectOne($sql, [$id]);
    }

    /**
     * Obtener registros con condiciones
     */
    public function where($column, $operator, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} ?";
        return $this->db->select($sql, [$value]);
    }

    /**
     * Crear nuevo registro
     */
    public function create($data) {
        try {
            // Filtrar solo campos fillable si están definidos
            if (!empty($this->fillable)) {
                $data = array_intersect_key($data, array_flip($this->fillable));
            }

            $columns = array_keys($data);
            $placeholders = array_fill(0, count($columns), '?');
            $values = array_values($data);

            $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ")
                    VALUES (" . implode(', ', $placeholders) . ")";

            $id = $this->db->insert($sql, $values);

            if ($id) {
                return $this->find($id);
            }

            return null;
        } catch (Exception $e) {
            throw new Exception("Error al crear registro: " . $e->getMessage());
        }
    }

    /**
     * Actualizar registro
     */
    public function update($id, $data) {
        try {
            // Filtrar solo campos fillable si están definidos
            if (!empty($this->fillable)) {
                $data = array_intersect_key($data, array_flip($this->fillable));
            }

            if (empty($data)) {
                throw new Exception("No hay datos para actualizar");
            }

            $columns = array_keys($data);
            $setParts = array_map(function($col) { return "{$col} = ?"; }, $columns);
            $setClause = implode(', ', $setParts);
            $values = array_values($data);
            $values[] = $id;

            $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
            $affected = $this->db->update($sql, $values);

            return $affected > 0 ? $this->find($id) : null;
        } catch (Exception $e) {
            throw new Exception("Error al actualizar registro: " . $e->getMessage());
        }
    }

    /**
     * Eliminar registro
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->delete($sql, [$id]);
    }

    /**
     * Contar registros
     */
    public function count() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->db->selectOne($sql);
        return $result['count'];
    }

    /**
     * Ejecutar consulta personalizada
     */
    public function query($sql, $params = []) {
        return $this->db->select($sql, $params);
    }

    /**
     * Ejecutar consulta personalizada (único resultado)
     */
    public function queryOne($sql, $params = []) {
        return $this->db->selectOne($sql, $params);
    }

    /**
     * Iniciar transacción
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    /**
     * Confirmar transacción
     */
    public function commit() {
        return $this->db->commit();
    }

    /**
     * Revertir transacción
     */
    public function rollBack() {
        return $this->db->rollBack();
    }
}
?>