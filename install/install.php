<?php
// Verificar si ya está instalado
if(file_exists('../config/.installed')) {
    header('Location: ../index.php');
    exit;
}

require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// conectar a mysql sin especificar la base de datos y entonces lazar la query de schema.sql
// si la query falla, mostrar el mensaje de error y terminar el script

try {
    $dsn ='mysql:host='. $_ENV['DB_HOST'];
    $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    $sql = file_get_contents(__DIR__. '/../db/schema.sql');
    $pdo->exec($sql);
    // redirigira la pagina principal
    header('Location:../index.php');
    exit;
}   catch(PDOException $e) {
    var_dump($e->getMessage());
    exit; 
}

?>