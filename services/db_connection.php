<?php

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__. '/../');
$dotenv->load();

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

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>