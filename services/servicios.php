<?php


require_once 'db_connection.php';

function obtenerServicios($soloActivos = false, $artistaId = null)
{
    try {
        $pdo = getConnection();
        if (!$pdo) return false;

        $sql = "SELECT s.*
               FROM servicios s ";
        $params = [];
        $where = [];

        if ($soloActivos) {
            $where[] = "s.activo = TRUE";
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

function obtenerServicioPorId($id)
{
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

function crearServicio($datos)
{
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

function actualizarServicio($id, $datos)
{
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

function eliminarServicio($id)
{
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

function eliminarServicioFisico($id)
{
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

function buscarServicios($termino)
{
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
