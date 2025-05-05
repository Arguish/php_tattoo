<?php
/**
 * Utilidades para validación de datos
 * Proporciona funciones para validar entradas de formularios
 */


require_once __DIR__ . '/../services/db_connection.php';




/**
 * Sanitiza una entrada de texto con opciones adicionales
 * @param string $input Texto a sanitizar
 * @return string Texto sanitizado con opciones UTF-8
 */
function sanitizeInputExtended($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Valida un email
 * @param string $email Email a validar
 * @return bool True si el email es válido
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida una contraseña
 * @param string $password Contraseña a validar
 * @return array Array con resultado de validación y mensaje
 */
function validarPassword($password) {
    $resultado = ['valido' => true, 'mensaje' => ''];
    
    
    if (strlen($password) < 6) {
        $resultado['valido'] = false;
        $resultado['mensaje'] = 'La contraseña debe tener al menos 6 caracteres';
        return $resultado;
    }
    
    
    if (!preg_match('/\d/', $password)) {
        $resultado['valido'] = false;
        $resultado['mensaje'] = 'La contraseña debe contener al menos un número';
        return $resultado;
    }
    
    return $resultado;
}

/**
 * Valida que dos contraseñas coincidan
 * @param string $password Contraseña
 * @param string $confirmPassword Confirmación de contraseña
 * @return bool True si las contraseñas coinciden
 */
function passwordsCoinciden($password, $confirmPassword) {
    return $password === $confirmPassword;
}

/**
 * Genera un mensaje de error formateado para mostrar en el formulario
 * @param string $mensaje Mensaje de error
 * @return string HTML con el mensaje de error
 */
function mensajeError($mensaje) {
    return '<div class="invalid-feedback d-block">' . $mensaje . '</div>';
}

/**
 * Genera un mensaje de éxito formateado
 * @param string $mensaje Mensaje de éxito
 * @return string HTML con el mensaje de éxito
 */
function mensajeExito($mensaje) {
    return '<div class="alert alert-success">' . $mensaje . '</div>';
}

/**
 * Redirecciona a una URL con un mensaje
 * @param string $url URL de destino
 * @param string $mensaje Mensaje a mostrar
 * @param string $tipo Tipo de mensaje (error, success)
 */
function redireccionarConMensaje($url, $mensaje, $tipo = 'error') {
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = $tipo;
    
    
    header('Location: ' . $url);
    exit;
}