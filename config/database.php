<?php
require '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__. '/../');
$dotenv->load();



// Intentar acceder a la base de datos

try {
    $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
    $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo 'Conectado a la base de datos';
} catch(PDOException $e) {
    var_dump($e->getCode());
    if($e->getCode() == 1049) {

        header('Location:../install/install.php');
        exit;
        
    }
    else {
        echo 'Error al conectar a la base de datos: '. $e->getMessage();
        die('Error al conectar a la base de datos: '. $e->getMessage());
    }
}

// Si el error es porque la base de datos no existe, crearla
if($pdo->getAttribute(PDO::ATTR_ERRMODE) == PDO::ERRMODE_EXCEPTION) {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    $pdo->exec('CREATE DATABASE '. $_ENV['DB_NAME']);  
}

?>