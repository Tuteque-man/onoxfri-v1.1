<?php
/**
 * Archivo de prueba de conexión a base de datos
 * Sistema de inventario onoxfri
 */

// Habilitar errores para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Prueba de Conexión a Base de Datos</h1>";
echo "<p>Probando conexión a la base de datos <strong>onoxfri</strong>...</p>";

try {
    // Incluir configuración de base de datos
    require_once 'config/database.php';

    echo "<p>✅ Archivo de configuración cargado correctamente</p>";

    // Crear instancia de conexión
    $db = getDB();
    echo "<p>✅ Conexión a base de datos establecida</p>";

    // Probar consulta simple
    $result = $db->selectOne("SELECT 1 as test, NOW() as current_time");
    echo "<p>✅ Consulta de prueba ejecutada correctamente</p>";

    if ($result) {
        echo "<p>Resultado de la prueba:</p>";
        echo "<ul>";
        echo "<li><strong>Test:</strong> " . $result['test'] . "</li>";
        echo "<li><strong>Hora actual:</strong> " . $result['current_time'] . "</li>";
        echo "</ul>";

        // Verificar si la base de datos existe
        $databases = $db->select("SHOW DATABASES LIKE 'onoxfri'");
        if (count($databases) > 0) {
            echo "<p>✅ Base de datos 'onoxfri' encontrada</p>";
        } else {
            echo "<p>⚠️ Base de datos 'onoxfri' no encontrada</p>";
        }

        // Verificar tablas principales
        $tables = ['users', 'products', 'categories', 'roles'];
        echo "<p>Verificando tablas principales:</p>";
        echo "<ul>";

        foreach ($tables as $table) {
            try {
                $count = $db->selectOne("SELECT COUNT(*) as count FROM {$table}");
                echo "<li><strong>{$table}:</strong> ✅ Existe ({$count['count']} registros)</li>";
            } catch (Exception $e) {
                echo "<li><strong>{$table}:</strong> ❌ No existe o error: " . $e->getMessage() . "</li>";
            }
        }
        echo "</ul>";

    } else {
        echo "<p>❌ Error: No se recibió resultado de la consulta de prueba</p>";
    }

    echo "<hr>";
    echo "<h3>✅ CONEXIÓN A BASE DE DATOS EXITOSA</h3>";
    echo "<p>El servidor PHP puede conectarse correctamente a la base de datos MySQL/MariaDB.</p>";
    echo "<p><strong>Base de datos:</strong> " . getConfig('database.database') . "</p>";
    echo "<p><strong>Servidor:</strong> " . getConfig('database.host') . "</p>";
    echo "<p><strong>Puerto:</strong> " . (getConfig('database.port') ?? '3306 (por defecto)') . "</p>";

} catch (PDOException $e) {
    echo "<h3>❌ ERROR DE CONEXIÓN PDO</h3>";
    echo "<p><strong>Código de error:</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Sugerencias:</strong></p>";
    echo "<ul>";
    echo "<li>Verifica que XAMPP esté ejecutándose</li>";
    echo "<li>Verifica que la base de datos 'onoxfri' exista en phpMyAdmin</li>";
    echo "<li>Verifica las credenciales en config/database.php</li>";
    echo "<li>Verifica que el puerto 3306 esté disponible</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "<h3>❌ ERROR GENERAL</h3>";
    echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
}

echo "<hr>";
echo "<h3>Información de Debug</h3>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<strong>Configuración de base de datos:</strong>";
echo "<pre>";
print_r(getConfig('database'));
echo "</pre>";

echo "<p><a href='index.php'>← Volver al inicio</a></p>";
?>