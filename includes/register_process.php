<?php
/**
 * Procesamiento del formulario de registro
 */

require_once '../utils/auth.php';
require_once '../utils/validation.php';
require_once '../services/usuarios.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/register.php');
    exit;
}

session_start();

$nombre = isset($_POST['nombre']) ? sanitizeInput($_POST['nombre']) : '';
$email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : ''; 
$confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
$terms = isset($_POST['terms']) ? $_POST['terms'] : '';

$datos = [
    'nombre' => $nombre,
    'email' => $email,
    'password' => $password,
    'confirm_password' => $confirmPassword,
    'terms' => $terms
];

$errores = validarDatosRegistro($datos);

if (!empty($errores)) {
    $_SESSION['errores_registro'] = $errores;
    $_SESSION['datos_registro'] = $datos; 
    header('Location: ../pages/register.php');
    exit;
}

if (emailExiste($email)) {
    redireccionarConMensaje('../pages/login.php', 'Este email ya está registrado. Por favor, inicia sesión.', 'info');
}

require_once '../utils/logger.php';

$datosUsuario = [
    'nombre' => $nombre,
    'email' => $email,
    'password' => $password,
    'rol' => 'cliente', 
];

logDebug('Intentando crear usuario con email: ' . $email);

$usuarioId = crearUsuario($datosUsuario);

if (!$usuarioId) {
    logError('Error al crear la cuenta para el email: ' . $email);
    redireccionarConMensaje('../pages/register.php', 'Error al crear la cuenta. Por favor, inténtalo de nuevo.', 'error');
}

$usuario = obtenerUsuarioPorId($usuarioId);

if (!$usuario) {
    logError('No se pudo obtener el usuario recién creado con ID: ' . $usuarioId);
    redireccionarConMensaje('../pages/register.php', 'Error al procesar el registro. Por favor, inténtalo de nuevo.', 'error');
}

logInfo('Usuario registrado correctamente: ' . $email);
iniciarSesion($usuario);

redireccionarConMensaje('../index.php', '¡Cuenta creada correctamente! Bienvenido/a.', 'success');