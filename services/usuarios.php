<?php

require_once 'db_connection.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__. '/../utils/logger.php';

// Manejar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_REQUEST['action'])&& isset($_REQUEST['target']) && $_REQUEST['target']=='users') {
    header('Content-Type: application/json');
    
    try {
        logDebug('Intentando procesar acción en usuarios: '. $_REQUEST['action']);


        switch ($_REQUEST['action']) {
            case 'create':
                if (!checkRole(['admin'])) {
                    throw new Exception('Acceso denegado: se requieren privilegios de administrador');
                }
                logDebug('Intentando crear usuario con datos: '. json_encode($_REQUEST));
                $success=crearUsuario($_REQUEST);
                echo json_encode(['success' => $success]);
                exit;

            case 'update':
                logDebug('Intentando actualizar usuario con ID: '. json_encode($_REQUEST));
                
                // Verify permissions: Admin OR owner of the account
                if (!checkRole(['admin']) && $_REQUEST['id'] != $_SESSION['usuario_id']) {
                    throw new Exception('Acceso denegado: se requieren mayores privilegios');
                }
                
                if (empty($_REQUEST['id'])) {
                    throw new Exception('ID de usuario requerido');
                }
                
                // If not admin, remove role from data to prevent privilege escalation
                if (!checkRole(['admin'])) {
                    unset($_REQUEST['rol']);
                    logDebug('Actualización de usuario no admin - eliminado campo rol');
                }
                
                $success = actualizarUsuario($_REQUEST['id'], $_REQUEST);
                echo json_encode(['success' => $success]);
                exit;

            case 'read':
                logDebug('Intentando obtener usuario con ID: '. $_REQUEST['id']);
                $id = $_REQUEST['id'] ?? null;
                if ($id) {
                    $usuario = obtenerUsuarioPorId($id);
                    logDebug('Usuario obtenido: '. ($usuario));
                    echo json_encode($usuario ?: ['error' => 'Usuario no encontrado']);
                } else {
                    $usuarios = obtenerUsuarios();
                    logDebug('Usuarios obtenidos: ');
                    echo json_encode($usuarios ?: ['error' => 'Error al obtener usuarios']);
                }
                exit;

            case 'delete':
                if (!checkRole(['admin'])) {
                    throw new Exception('Acceso denegado: se requieren privilegios de administrador');
                }
                logDebug('Intentando eliminar usuario con ID: '. $_REQUEST['id']);
                if (empty($_REQUEST['id'])) {
                    throw new Exception('ID de usuario requerido');
                }
                if (!eliminarUsuarioFisico($_REQUEST['id'])) {
                    throw new Exception('Error al eliminar el usuario');
                }
                echo json_encode(['success' => true]);
                exit;

            default:
                throw new Exception('Acción no válida');
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

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
        
        
        if (empty($datos['nombre']) || empty($datos['email']) || empty($datos['rol'])) {
            logWarning('Datos incompletos al crear usuario', $datos);
            return false;
        }
        
        
        $nombre = sanitizeInput($datos['nombre']);
        $email = sanitizeInput($datos['email']);
        if ($datos['action']){
            $password = password_hash(123456, PASSWORD_DEFAULT); 
            logWarning('Asignada contraseña por defecto');
        }else{
            $password = password_hash($datos['password'], PASSWORD_DEFAULT); 
        }
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

function eliminarUsuarioFisico($id) {
    try {
        $pdo = getConnection();
        if (!$pdo) {
            logError('Conexión DB fallida en eliminarUsuarioFisico');
            return false;
        }

        $pdo->beginTransaction();

        // Eliminar facturas relacionadas primero
        $stmtFacturas = $pdo->prepare("DELETE FROM facturas WHERE reserva_id IN (SELECT id FROM reservas WHERE cliente_id = ?)");
        $stmtFacturas->execute([intval($id)]);

        // Eliminar reservas relacionadas
        $stmtReservas = $pdo->prepare("DELETE FROM reservas WHERE cliente_id = ?");
        $stmtReservas->execute([intval($id)]);

        // Eliminar usuario
        $stmtUsuario = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmtUsuario->execute([intval($id)]);
        
        $affected = $stmtUsuario->rowCount();
        $pdo->commit();
        
        logDebug("Filas afectadas al eliminar: ".$affected);
        return $affected > 0;
    } catch (PDOException $e) {
        $pdo->rollBack();
        logError('Error DB eliminación física: '.$e->getMessage().' ID: '.$id);
        return false;
    } catch (Exception $e) {
        $pdo->rollBack();
        logError('Error general: '.$e->getMessage());
        return false;
    }
}

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
function actualizarPasswordUsuario($id, $new_password) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;

        // Verificar si el usuario existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();
        if (!$usuario) {
            return false;
        }

        // Hashear la nueva contraseña
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        if (!$hashed_password || strlen($hashed_password) < 20) {
            return false;
        }

        // Actualizar la contraseña
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $id]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error al actualizar la contraseña del usuario: ' . $e->getMessage());
        return false;
    }
}
?>