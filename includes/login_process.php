<?php

require_once '../utils/auth.php';
require_once '../utils/validation.php';
require_once '../utils/logger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/login.php');
    exit;
}

session_start();

$email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$recordar = isset($_POST['remember']) && $_POST['remember'] === 'on';

logDebug('Intento de inicio de sesión para: ' . $email);

if (empty($email) || empty($password)) {
    logWarning('Intento de inicio de sesión con campos vacíos');
    redireccionarConMensaje('../pages/login.php', 'Todos los campos son obligatorios', 'error');
}

if (!validarEmail($email)) {
    logWarning('Intento de inicio de sesión con formato de email inválido: ' . $email);
    redireccionarConMensaje('../pages/login.php', 'El formato del email no es válido', 'error');
}

logDebug('Validando credenciales para: ' . $email);
$usuario = validarCredenciales($email, $password);

if (!$usuario) {
    logWarning('Credenciales inválidas para: ' . $email . "//" . $password . "//" . $usuario);
    redireccionarConMensaje('../pages/login.php', 'Email o contraseña incorrectos', 'error');
}

iniciarSesion($usuario, $recordar);

switch ($usuario['rol']) {
    case 'cliente':
        header('Location: ../index.php');
        break;
    default:
        header('Location: ../pages/dashboard');
        break;
}
exit;
