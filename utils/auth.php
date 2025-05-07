<?php
/**
 * Utilidades para autenticación de usuarios
 * Proporciona funciones para login, registro y validación de usuarios
 */

require_once __DIR__ . '/../services/usuarios.php';

/**
 * Valida las credenciales de un usuario
 * @param string $email Email del usuario
 * @param string $password Contraseña del usuario
 * @return array|false Datos del usuario si las credenciales son válidas, false en caso contrario
 */
function validarCredenciales($email, $password) {
    try {
        
        require_once __DIR__ . '/logger.php';
        
        $pdo = getConnection();
        if (!$pdo) {
            logError('No se pudo conectar a la base de datos al validar credenciales');
            return false;
        }
        
        logDebug('Intentando validar credenciales para el email: ' . $email);
        
        $stmt = $pdo->prepare("SELECT id, nombre, email, password, rol, activo FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if (!$usuario) {
            logDebug('Usuario no encontrado con el email: ' . $email);
            return false; 
        }
        
        if (!$usuario['activo']) {
            logDebug('Usuario inactivo: ' . $email);
            return false; 
        }
        
        
        logDebug('Verificando contraseña para usuario: ' . $email);
        
        
        $passwordValida = false;
        
        
        if (substr($usuario['password'], 0, 4) === '$2y$') {
            logDebug('Contraseña en formato hash moderno');
            $passwordValida = password_verify($password, $usuario['password']);
        } 
        
        else if (strlen($usuario['password']) === 32 || strlen($usuario['password']) === 40) {
            logWarning('Contraseña en formato hash antiguo para: ' . $email . '. Actualizando...');
            
            $passwordValida = (md5($password) === $usuario['password'] || sha1($password) === $usuario['password']);
            
            
            if ($passwordValida) {
                actualizarHashContraseña($usuario['id'], $password);
            }
        } 
        
        else if (strlen($usuario['password']) === 64 && ctype_xdigit($usuario['password'])) {
            logDebug('Contraseña en formato SHA2 (256 bits)');
            logDebug($password."//".$usuario['password']."//".hash('sha256', $password));
            $passwordValida = hash_equals(strtolower($usuario['password']), strtolower(hash('sha256', $password)));
            $hashedInput = hash('sha256', $password);
            $passwordValida = hash_equals(strtolower($usuario['password']), strtolower($hashedInput));
            
            if ($passwordValida) {
                actualizarHashContraseña($usuario['id'], $password);
            }
        }
        else {
            logDebug('Formato de contraseña desconocido. Intentando verificación estándar');
            logDebug($password."//".$usuario['password']);
            $passwordValida = password_verify($password, $usuario['password']);
        }
        
        logDebug('Resultado de verificación de contraseña: ' . ($passwordValida ? 'correcta' : 'incorrecta'));
        
        if ($passwordValida) {
            
            unset($usuario['password']);
            logInfo('Inicio de sesión exitoso para: ' . $email);
            return $usuario;
        }
        
        logDebug('Contraseña incorrecta para: ' . $email);
        return false; 
    } catch (PDOException $e) {
        logError('Error al validar credenciales: ' . $e->getMessage(), ['email' => $email]);
        return false;
    }
}

/**
 * Inicia sesión para un usuario
 * @param array $usuario Datos del usuario
 * @param bool $recordar Si se debe recordar la sesión
 * @return bool True si se inició sesión correctamente
 */
function iniciarSesion($usuario, $recordar = false) {
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_rol'] = $usuario['rol'];
    $_SESSION['autenticado'] = true;
    
    
    if ($recordar) {
        $token = bin2hex(random_bytes(32)); 
        
        
        
        
        
        setcookie('auth_token', $token, time() + (86400 * 30), '/', '', false, true);
    }
    
    return true;
}

/**
 * Cierra la sesión del usuario
 * @return bool True si se cerró sesión correctamente
 */
function cerrarSesion() {
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    
    if (isset($_COOKIE['auth_token'])) {
        
        
        
        
        setcookie('auth_token', '', time() - 3600, '/', '', false, true);
    }
    
    
    $_SESSION = array();
    session_destroy();
}

/**
 * Actualiza el hash de la contraseña de un usuario al formato seguro actual
 * @param int $usuarioId ID del usuario
 * @param string $password Contraseña en texto plano
 * @return bool True si se actualizó correctamente
 */
function actualizarHashContraseña($usuarioId, $password) {
    try {
        
        if (!function_exists('logDebug')) {
            require_once __DIR__ . '/logger.php';
        }
        
        logDebug('Actualizando hash de contraseña para usuario ID: ' . $usuarioId);
        
        $pdo = getConnection();
        if (!$pdo) {
            logError('No se pudo conectar a la base de datos al actualizar hash');
            return false;
        }
        
        
        $nuevoHash = password_hash($password, PASSWORD_DEFAULT);
        
        
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $resultado = $stmt->execute([$nuevoHash, $usuarioId]);
        
        if ($resultado) {
            logInfo('Hash de contraseña actualizado correctamente para usuario ID: ' . $usuarioId);
        } else {
            logError('Error al actualizar hash de contraseña para usuario ID: ' . $usuarioId);
        }
        
        return $resultado;
    } catch (PDOException $e) {
        logError('Error en la base de datos al actualizar hash: ' . $e->getMessage(), ['usuario_id' => $usuarioId]);
        return false;
    }
    return true;
}
    


/**
 * Verifica si un email ya está registrado
 * @param string $email Email a verificar
 * @return bool True si el email ya está registrado
 */
function emailExiste($email) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        return $stmt->fetch() !== false;
    } catch (PDOException $e) {
        error_log('Error al verificar email: ' . $e->getMessage());
        return false;
    }
}

/**
 * Valida los datos de registro de un usuario
 * @param array $datos Datos del formulario de registro
 * @return array Array con errores encontrados, vacío si no hay errores
 */
function validarDatosRegistro($datos) {
    $errores = [];
    
    
    if (empty($datos['nombre'])) {
        $errores['nombre'] = 'El nombre es obligatorio';
    } elseif (strlen($datos['nombre']) < 2 || strlen($datos['nombre']) > 50) {
        $errores['nombre'] = 'El nombre debe tener entre 2 y 50 caracteres';
    }
    
    
        
    
    
    
    if (empty($datos['email'])) {
        $errores['email'] = 'El email es obligatorio';
    } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'El formato del email no es válido';
    } elseif (emailExiste($datos['email'])) {
        $errores['email'] = 'Este email ya está registrado';
    }
    
    
    if (empty($datos['password'])) {
        $errores['password'] = 'La contraseña es obligatoria';
    } elseif (strlen($datos['password']) < 6) {
        $errores['password'] = 'La contraseña debe tener al menos 6 caracteres';
    }
    
    
    if (isset($datos['confirm_password'])) {
        if ($datos['password'] !== $datos['confirm_password']) {
            $errores['confirm_password'] = 'Las contraseñas no coinciden';
        }
    }
    
    
    if (isset($datos['terms']) && $datos['terms'] != 'on' && $datos['terms'] != '1') {
        $errores['terms'] = 'Debes aceptar los términos y condiciones';
    }
    
    return $errores;
}

// metodo para comprobar si el usuario esta logueado
function estaLogueado() {
    //comprobar si existe una cookie con el nombre auth_token
    if (isset($_COOKIE['auth_token'])) {
        //si existe, comprobar si existe una sesion con el nombre auth_token
        if (isset($_SESSION['auth_token'])) {
            //si existe, comprobar si el valor de la cookie es igual al valor de la sesion
            if ($_COOKIE['auth_token'] === $_SESSION['auth_token']) {
                return true;
            }
        }
    }
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }  
    return isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === true;
}