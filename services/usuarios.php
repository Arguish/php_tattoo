<?php
/**
 * Servicio CRUD para la tabla usuarios
 * Proporciona funciones para crear, leer, actualizar y eliminar usuarios
 */

require_once 'db_connection.php';

/**
 * Obtiene todos los usuarios
 * @param string $rol Filtrar por rol (opcional)
 * @return array|false Array de usuarios o false en caso de error
 */
function obtenerUsuarios($rol = null) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $sql = "SELECT id, nombre, email, rol, telefono, fecha_registro, activo FROM usuarios";
        $params = [];
        
        if ($rol) {
            $sql .= " WHERE rol = ?";
            $params[] = $rol;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error al obtener usuarios: ' . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene un usuario por su ID
 * @param int $id ID del usuario
 * @return array|false Datos del usuario o false en caso de error
 */
function obtenerUsuarioPorId($id) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $stmt = $pdo->prepare("SELECT id, nombre, email, rol, telefono, fecha_registro, activo FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Error al obtener usuario por ID: ' . $e->getMessage());
        return false;
    }
}

/**
 * Crea un nuevo usuario
 * @param array $datos Datos del usuario (nombre, email, password, rol, telefono)
 * @return int|false ID del usuario creado o false en caso de error
 */
function crearUsuario($datos) {
    try {
        if (!function_exists('logDebug')) {
            require_once __DIR__ . '/../utils/logger.php';
        }
        
        $pdo = getConnection();
        if (!$pdo) {
            logError('No se pudo conectar a la base de datos al crear usuario');
            return false;
        }
        
        
        if (empty($datos['nombre']) || empty($datos['email']) || empty($datos['password']) || empty($datos['rol'])) {
            logWarning('Datos incompletos al crear usuario', $datos);
            return false;
        }
        
        
        $nombre = sanitizeInput($datos['nombre']);
        $email = sanitizeInput($datos['email']);
        $password = password_hash($datos['password'], PASSWORD_DEFAULT); 
        $rol = sanitizeInput($datos['rol']);
        $telefono = isset($datos['telefono']) ? sanitizeInput($datos['telefono']) : null;
        
        
        if (!$password || strlen($password) < 20) {
            logError('Error al generar hash de contraseña para: ' . $email);
            return false;
        }
        
        logDebug('Creando usuario con email: ' . $email . ' y rol: ' . $rol);
        
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol, telefono) 
                            VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $password, $rol, $telefono]);
        
        $id = $pdo->lastInsertId();
        logInfo('Usuario creado correctamente con ID: ' . $id);
        return $id;
    } catch (PDOException $e) {
        logError('Error al crear usuario: ' . $e->getMessage(), isset($datos['email']) ? ['email' => $datos['email']] : []);
        return false;
    }
}

/**
 * Actualiza un usuario existente
 * @param int $id ID del usuario
 * @param array $datos Datos a actualizar
 * @return bool True si se actualizó correctamente, false en caso contrario
 */
function actualizarUsuario($id, $datos) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        
        $campos = [];
        $valores = [];
        
        if (isset($datos['nombre'])) {
            $campos[] = "nombre = ?";
            $valores[] = sanitizeInput($datos['nombre']);
        }
        
        
        if (isset($datos['email'])) {
            $campos[] = "email = ?";
            $valores[] = sanitizeInput($datos['email']);
        }
        
        if (isset($datos['password'])) {
            $campos[] = "password = ?";
            $valores[] = password_hash($datos['password'], PASSWORD_DEFAULT);
        }
        
        if (isset($datos['rol'])) {
            $campos[] = "rol = ?";
            $valores[] = sanitizeInput($datos['rol']);
        }
        
        if (isset($datos['telefono'])) {
            $campos[] = "telefono = ?";
            $valores[] = sanitizeInput($datos['telefono']);
        }
        
        if (isset($datos['activo'])) {
            $campos[] = "activo = ?";
            $valores[] = (bool)$datos['activo'];
        }
        
        if (empty($campos)) {
            return false; 
        }
        
        $sql = "UPDATE usuarios SET " . implode(", ", $campos) . " WHERE id = ?";
        $valores[] = $id;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($valores);
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error al actualizar usuario: ' . $e->getMessage());
        return false;
    }
}

/**
 * Elimina un usuario (desactivación lógica)
 * @param int $id ID del usuario
 * @return bool True si se desactivó correctamente, false en caso contrario
 */
function eliminarUsuario($id) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        
        $stmt = $pdo->prepare("UPDATE usuarios SET activo = FALSE WHERE id = ?");
        $stmt->execute([$id]);
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error al eliminar usuario: ' . $e->getMessage());
        return false;
    }
}

/**
 * Elimina físicamente un usuario de la base de datos
 * @param int $id ID del usuario
 * @return bool True si se eliminó correctamente, false en caso contrario
 */
function eliminarUsuarioFisico($id) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error al eliminar físicamente usuario: ' . $e->getMessage());
        return false;
    }
}

/**
 * Busca usuarios por nombre, apellido o email
 * @param string $termino Término de búsqueda
 * @return array|false Resultados de la búsqueda o false en caso de error
 */
function buscarUsuarios($termino) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $termino = "%" . sanitizeInput($termino) . "%";
        
        $stmt = $pdo->prepare("SELECT id, nombre, email, rol, telefono, fecha_registro, activo 
                            FROM usuarios 
                            WHERE nombre LIKE ? OR email LIKE ?");
        $stmt->execute([$termino, $termino]);
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error al buscar usuarios: ' . $e->getMessage());
        return false;
    }
}
?>