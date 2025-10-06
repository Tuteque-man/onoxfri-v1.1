<?php
/**
 * Configuración de conexión a la base de datos
 * Archivo básico y funcional para el sistema de inventario onoxfri
 */

class Database {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'onoxfri';
    private $charset = 'utf8mb4';
    private $connection = null;

    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function select($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function selectOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    public function insert($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $this->connection->lastInsertId();
    }

    public function update($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function delete($sql, $params = []) {
        return $this->update($sql, $params);
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollBack() {
        return $this->connection->rollBack();
    }

    public function getLastError() {
        $error = $this->connection->errorInfo();
        return $error[2] ?? null;
    }
}

// Función helper para obtener instancia de conexión
function getDB() {
    static $db = null;
    if ($db === null) {
        $db = new Database();
    }
    return $db;
}

// Configuración adicional de la aplicación
$config = [
    'app_name' => 'Sistema de Inventario Onoxfri',
    'app_version' => '1.0.0',
    'base_url' => 'http://localhost/onoxfri/backend/php-8.2.12',
    'timezone' => 'America/Caracas',
    'debug' => true,
    'database' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'onoxfri',
        'charset' => 'utf8mb4'
    ]
];

// Función para obtener configuración
function getConfig($key = null) {
    global $config;
    if ($key === null) {
        return $config;
    }

    $keys = explode('.', $key);
    $value = $config;

    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return null;
        }
    }

    return $value;
}
?>