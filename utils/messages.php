<?php

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

function campoTieneError($campo) {
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['errores_registro']) && isset($_SESSION['errores_registro'][$campo]);
}

function mensajeErrorCampo($campo) {
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['errores_registro']) && isset($_SESSION['errores_registro'][$campo])) {
        return $_SESSION['errores_registro'][$campo];
    }
    
    return '';
}