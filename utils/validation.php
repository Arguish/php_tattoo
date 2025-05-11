<?php


require_once __DIR__ . '/../services/db_connection.php';




function sanitizeInputExtended($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

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

function passwordsCoinciden($password, $confirmPassword) {
    return $password === $confirmPassword;
}

function mensajeError($mensaje) {
    return '<div class="invalid-feedback d-block">' . $mensaje . '</div>';
}

function mensajeExito($mensaje) {
    return '<div class="alert alert-success">' . $mensaje . '</div>';
}

function redireccionarConMensaje($url, $mensaje, $tipo = 'error') {
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = $tipo;
    
    
    header('Location: ' . $url);
    exit;
}