<?php
/**
 * Utilidades para mostrar mensajes al usuario
 * Proporciona funciones para mostrar mensajes de error, éxito, etc.
 */

/**
 * Muestra los mensajes almacenados en la sesión
 * @return string HTML con los mensajes
 */
function mostrarMensajes() {
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $html = '';
    
    
    if (isset($_SESSION['mensaje'])) {
        $tipo = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : 'error';
        $clase = ($tipo == 'error') ? 'alert-danger' : 
                 (($tipo == 'success') ? 'alert-success' : 
                 (($tipo == 'info') ? 'alert-info' : 'alert-warning'));
        
        $html .= '<div class="alert ' . $clase . ' alert-dismissible fade show" role="alert">';
        $html .= $_SESSION['mensaje'];
        $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        $html .= '</div>';
        
        
        unset($_SESSION['mensaje']);
        unset($_SESSION['tipo_mensaje']);
    }
    
    
    if (isset($_SESSION['errores_registro'])) {
        $html .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        $html .= '<ul class="mb-0">';
        
        foreach ($_SESSION['errores_registro'] as $campo => $error) {
            $html .= '<li>' . $error . '</li>';
        }
        
        $html .= '</ul>';
        $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        $html .= '</div>';
        
        
        unset($_SESSION['errores_registro']);
    }
    
    return $html;
}

/**
 * Obtiene el valor anterior de un campo de formulario
 * @param string $campo Nombre del campo
 * @param string $default Valor por defecto
 * @return string Valor del campo
 */
function valorAnteriorCampo($campo, $default = '') {
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['datos_registro']) && isset($_SESSION['datos_registro'][$campo])) {
        $valor = $_SESSION['datos_registro'][$campo];
        return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
    }
    
    return $default;
}

/**
 * Verifica si un campo tiene error
 * @param string $campo Nombre del campo
 * @return bool True si el campo tiene error
 */
function campoTieneError($campo) {
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['errores_registro']) && isset($_SESSION['errores_registro'][$campo]);
}

/**
 * Obtiene el mensaje de error de un campo
 * @param string $campo Nombre del campo
 * @return string Mensaje de error
 */
function mensajeErrorCampo($campo) {
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['errores_registro']) && isset($_SESSION['errores_registro'][$campo])) {
        return $_SESSION['errores_registro'][$campo];
    }
    
    return '';
}