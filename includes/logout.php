<?php
/**
 * Procesamiento de cierre de sesión
 */

require_once '../utils/auth.php';
require_once '../utils/logger.php';

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Registrar el cierre de sesión en los logs si el usuario estaba autenticado
if (isset($_SESSION['usuario_email'])) {
    logInfo('Cierre de sesión para el usuario: ' . $_SESSION['usuario_email']);
}

// Cerrar la sesión
cerrarSesion();

// Redirigir a la página de inicio
header('Location: ../index.php');
exit;