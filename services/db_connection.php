<?php
/**
 * Archivo de conexión a la base de datos para los servicios CRUD
 * Este archivo será incluido en todos los servicios para establecer la conexión
 */

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__. '/../');
$dotenv->load();

/**
 * Función para obtener la conexión a la base de datos
 * @return PDO Objeto de conexión PDO
 */
function getConnection() {
    if (!function_exists('logError')) {
        require_once __DIR__ . '/../utils/logger.php';
    }
    
    try {
        $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
        $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        if (function_exists('logDebug')) {
            logDebug('Conexión a la base de datos establecida correctamente');
        }
        
        return $pdo;
    } catch(PDOException $e) {
        if (function_exists('logError')) {
            logError('Error de conexión a la base de datos: ' . $e->getMessage(), [
                'host' => $_ENV['DB_HOST'],
                'dbname' => $_ENV['DB_NAME']
            ]);
        } else {
            error_log('Error de conexión a la base de datos: ' . $e->getMessage());
        }
        return null;
    }
}

/**
 * Función para sanitizar entradas de usuario
 * @param string $data Datos a sanitizar
 * @return string Datos sanitizados
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>