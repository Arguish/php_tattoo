<?php
/**
 * Servicio CRUD para la tabla servicios
 * Proporciona funciones para crear, leer, actualizar y eliminar servicios
 */

require_once 'db_connection.php';

/**
 * Obtiene todos los servicios
 * @param bool $soloActivos Filtrar solo servicios activos
 * @param int $artistaId Filtrar por artista (opcional)
 * @return array|false Array de servicios o false en caso de error
 */
function obtenerServicios($soloActivos = false, $artistaId = null) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $sql = "SELECT s.*, u.nombre as artista_nombre, u.apellido as artista_apellido 
               FROM servicios s 
               LEFT JOIN usuarios u ON s.artista_id = u.id";
        $params = [];
        $where = [];
        
        if ($soloActivos) {
            $where[] = "s.activo = TRUE";
        }
        
        if ($artistaId) {
            $where[] = "s.artista_id = ?";
            $params[] = $artistaId;
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error al obtener servicios: ' . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene un servicio por su ID
 * @param int $id ID del servicio
 * @return array|false Datos del servicio o false en caso de error
 */
function obtenerServicioPorId($id) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $stmt = $pdo->prepare("SELECT s.*, u.nombre as artista_nombre, u.apellido as artista_apellido 
                              FROM servicios s 
                              LEFT JOIN usuarios u ON s.artista_id = u.id 
                              WHERE s.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Error al obtener servicio por ID: ' . $e->getMessage());
        return false;
    }
}

/**
 * Crea un nuevo servicio
 * @param array $datos Datos del servicio (nombre, descripcion, duracion, precio, artista_id)
 * @return int|false ID del servicio creado o false en caso de error
 */
function crearServicio($datos) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        if (empty($datos['nombre']) || !isset($datos['duracion']) || !isset($datos['precio']) || empty($datos['artista_id'])) {
            return false;
        }
        
        $nombre = sanitizeInput($datos['nombre']);
        $descripcion = isset($datos['descripcion']) ? sanitizeInput($datos['descripcion']) : null;
        $duracion = (int)$datos['duracion'];
        $precio = (float)$datos['precio'];
        $artistaId = (int)$datos['artista_id'];
        $activo = isset($datos['activo']) ? (bool)$datos['activo'] : true;
        
        $stmt = $pdo->prepare("INSERT INTO servicios (nombre, descripcion, duracion, precio, artista_id, activo) 
                            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $duracion, $precio, $artistaId, $activo]);
        
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log('Error al crear servicio: ' . $e->getMessage());
        return false;
    }
}

/**
 * Actualiza un servicio existente
 * @param int $id ID del servicio
 * @param array $datos Datos a actualizar
 * @return bool True si se actualizó correctamente, false en caso contrario
 */
function actualizarServicio($id, $datos) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $campos = [];
        $valores = [];
        
        if (isset($datos['nombre'])) {
            $campos[] = "nombre = ?";
            $valores[] = sanitizeInput($datos['nombre']);
        }
        
        if (isset($datos['descripcion'])) {
            $campos[] = "descripcion = ?";
            $valores[] = sanitizeInput($datos['descripcion']);
        }
        
        if (isset($datos['duracion'])) {
            $campos[] = "duracion = ?";
            $valores[] = (int)$datos['duracion'];
        }
        
        if (isset($datos['precio'])) {
            $campos[] = "precio = ?";
            $valores[] = (float)$datos['precio'];
        }
        
        if (isset($datos['artista_id'])) {
            if (empty($datos['artista_id'])) {
                return false; 
            }
            $campos[] = "artista_id = ?";
            $valores[] = (int)$datos['artista_id'];
        }
        
        if (isset($datos['activo'])) {
            $campos[] = "activo = ?";
            $valores[] = (bool)$datos['activo'];
        }
        
        if (empty($campos)) {
            return false; 
        }
        
        $sql = "UPDATE servicios SET " . implode(", ", $campos) . " WHERE id = ?";
        $valores[] = $id;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($valores);
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error al actualizar servicio: ' . $e->getMessage());
        return false;
    }
}

/**
 * Elimina un servicio (desactivación lógica)
 * @param int $id ID del servicio
 * @return bool True si se desactivó correctamente, false en caso contrario
 */
function eliminarServicio($id) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $stmt = $pdo->prepare("UPDATE servicios SET activo = FALSE WHERE id = ?");
        $stmt->execute([$id]);
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error al eliminar servicio: ' . $e->getMessage());
        return false;
    }
}

/**
 * Elimina físicamente un servicio de la base de datos
 * @param int $id ID del servicio
 * @return bool True si se eliminó correctamente, false en caso contrario
 */
function eliminarServicioFisico($id) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservas WHERE servicio_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            return false; 
        }
        
        $stmt = $pdo->prepare("DELETE FROM servicios WHERE id = ?");
        $stmt->execute([$id]);
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error al eliminar físicamente servicio: ' . $e->getMessage());
        return false;
    }
}

/**
 * Busca servicios por nombre o descripción
 * @param string $termino Término de búsqueda
 * @return array|false Resultados de la búsqueda o false en caso de error
 */
function buscarServicios($termino) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $termino = "%" . sanitizeInput($termino) . "%";
        
        $stmt = $pdo->prepare("SELECT s.*, u.nombre as artista_nombre, u.apellido as artista_apellido 
                            FROM servicios s 
                            LEFT JOIN usuarios u ON s.artista_id = u.id 
                            WHERE s.nombre LIKE ? OR s.descripcion LIKE ?");
        $stmt->execute([$termino, $termino]);
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error al buscar servicios: ' . $e->getMessage());
        return false;
    }
}
?>