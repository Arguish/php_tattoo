<?php
/**
 * Servicio CRUD para la tabla facturas
 * Proporciona funciones para crear, leer, actualizar y eliminar facturas
 */

require_once 'db_connection.php';

/**
 * Obtiene todas las facturas
 * @param bool $soloPagadas Filtrar solo facturas pagadas (opcional)
 * @param string $fechaDesde Filtrar desde fecha (opcional, formato Y-m-d)
 * @param string $fechaHasta Filtrar hasta fecha (opcional, formato Y-m-d)
 * @return array|false Array de facturas o false en caso de error
 */
function obtenerFacturas($soloPagadas = null, $fechaDesde = null, $fechaHasta = null) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $sql = "SELECT f.*, 
                r.fecha_hora as reserva_fecha, 
                c.nombre as cliente_nombre, c.apellido as cliente_apellido,
                a.nombre as artista_nombre, a.apellido as artista_apellido,
                s.nombre as servicio_nombre
               FROM facturas f 
               JOIN reservas r ON f.reserva_id = r.id
               JOIN usuarios c ON r.cliente_id = c.id
               JOIN usuarios a ON r.artista_id = a.id
               JOIN servicios s ON r.servicio_id = s.id";
        
        $params = [];
        $where = [];
        
        if ($soloPagadas !== null) {
            $where[] = "f.pagada = ?";
            $params[] = $soloPagadas ? 1 : 0;
        }
        
        if ($fechaDesde) {
            $where[] = "DATE(f.fecha_emision) >= ?";
            $params[] = $fechaDesde;
        }
        
        if ($fechaHasta) {
            $where[] = "DATE(f.fecha_emision) <= ?";
            $params[] = $fechaHasta;
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY f.fecha_emision DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error al obtener facturas: ' . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene una factura por su ID
 * @param int $id ID de la factura
 * @return array|false Datos de la factura o false en caso de error
 */
function obtenerFacturaPorId($id) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $stmt = $pdo->prepare("SELECT f.*, 
                              r.fecha_hora as reserva_fecha, r.observaciones as reserva_observaciones,
                              c.nombre as cliente_nombre, c.apellido as cliente_apellido, c.email as cliente_email, c.telefono as cliente_telefono,
                              a.nombre as artista_nombre, a.apellido as artista_apellido,
                              s.nombre as servicio_nombre, s.descripcion as servicio_descripcion
                              FROM facturas f 
                              JOIN reservas r ON f.reserva_id = r.id
                              JOIN usuarios c ON r.cliente_id = c.id
                              JOIN usuarios a ON r.artista_id = a.id
                              JOIN servicios s ON r.servicio_id = s.id
                              WHERE f.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Error al obtener factura por ID: ' . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene una factura por ID de reserva
 * @param int $reservaId ID de la reserva
 * @return array|false Datos de la factura o false en caso de error
 */
function obtenerFacturaPorReserva($reservaId) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $stmt = $pdo->prepare("SELECT * FROM facturas WHERE reserva_id = ?");
        $stmt->execute([$reservaId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Error al obtener factura por reserva: ' . $e->getMessage());
        return false;
    }
}

/**
 * Crea una nueva factura
 * @param array $datos Datos de la factura (reserva_id, total, metodo_pago)
 * @return int|false ID de la factura creada o false en caso de error
 */
function crearFactura($datos) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        if (empty($datos['reserva_id']) || !isset($datos['total']) || empty($datos['metodo_pago'])) {
            return false;
        }
        
        $stmt = $pdo->prepare("SELECT id, estado FROM reservas WHERE id = ?");
        $stmt->execute([$datos['reserva_id']]);
        $reserva = $stmt->fetch();
        
        if (!$reserva) {
            return false; 
        }
        
        $stmt = $pdo->prepare("SELECT id FROM facturas WHERE reserva_id = ?");
        $stmt->execute([$datos['reserva_id']]);
        if ($stmt->fetch()) {
            return false; 
        }
        
        $reservaId = (int)$datos['reserva_id'];
        $total = (float)$datos['total'];
        $metodoPago = sanitizeInput($datos['metodo_pago']);
        $pagada = isset($datos['pagada']) ? (bool)$datos['pagada'] : false;
        
        $stmt = $pdo->prepare("INSERT INTO facturas (reserva_id, total, metodo_pago, pagada) 
                            VALUES (?, ?, ?, ?)");
        $stmt->execute([$reservaId, $total, $metodoPago, $pagada]);
        
        $facturaId = $pdo->lastInsertId();
        
        if ($facturaId && $reserva['estado'] != 'completada') {
            $stmt = $pdo->prepare("UPDATE reservas SET estado = 'completada' WHERE id = ?");
            $stmt->execute([$reservaId]);
        }
        
        return $facturaId;
    } catch (PDOException $e) {
        error_log('Error al crear factura: ' . $e->getMessage());
        return false;
    }
}

/**
 * Actualiza una factura existente
 * @param int $id ID de la factura
 * @param array $datos Datos a actualizar
 * @return bool True si se actualizó correctamente, false en caso contrario
 */
function actualizarFactura($id, $datos) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $campos = [];
        $valores = [];
        
        if (isset($datos['total'])) {
            $campos[] = "total = ?";
            $valores[] = (float)$datos['total'];
        }
        
        if (isset($datos['metodo_pago'])) {
            $campos[] = "metodo_pago = ?";
            $valores[] = sanitizeInput($datos['metodo_pago']);
        }
        
        if (isset($datos['pagada'])) {
            $campos[] = "pagada = ?";
            $valores[] = (bool)$datos['pagada'];
        }
        
        if (empty($campos)) {
            return false; 
        }
        
        $sql = "UPDATE facturas SET " . implode(", ", $campos) . " WHERE id = ?";
        $valores[] = $id;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($valores);
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error al actualizar factura: ' . $e->getMessage());
        return false;
    }
}

/**
 * Marca una factura como pagada
 * @param int $id ID de la factura
 * @return bool True si se marcó correctamente, false en caso contrario
 */
function marcarFacturaPagada($id) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $stmt = $pdo->prepare("UPDATE facturas SET pagada = TRUE WHERE id = ?");
        $stmt->execute([$id]);
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error al marcar factura como pagada: ' . $e->getMessage());
        return false;
    }
}

/**
 * Elimina una factura
 * @param int $id ID de la factura
 * @return bool True si se eliminó correctamente, false en caso contrario
 */
function eliminarFactura($id) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $stmt = $pdo->prepare("DELETE FROM facturas WHERE id = ?");
        $stmt->execute([$id]);
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error al eliminar factura: ' . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene estadísticas de facturación por período
 * @param string $fechaDesde Fecha inicial (formato Y-m-d)
 * @param string $fechaHasta Fecha final (formato Y-m-d)
 * @return array|false Estadísticas o false en caso de error
 */
function obtenerEstadisticasFacturacion($fechaDesde, $fechaHasta) {
    try {
        $pdo = getConnection();
        if (!$pdo) return false;
        
        $stmt = $pdo->prepare("SELECT 
                            COUNT(*) as total_facturas,
                            SUM(total) as ingresos_totales,
                            ROUND(AVG(total), 2) as promedio_factura,
                            SUM(CASE WHEN pagada = 1 THEN total ELSE 0 END) as ingresos_cobrados,
                            SUM(CASE WHEN pagada = 0 THEN total ELSE 0 END) as ingresos_pendientes,
                            COUNT(CASE WHEN pagada = 1 THEN 1 END) as facturas_pagadas,
                            COUNT(CASE WHEN pagada = 0 THEN 1 END) as facturas_pendientes
                            FROM facturas
                            WHERE fecha_emision BETWEEN ? AND ?");
        $stmt->execute([$fechaDesde . ' 00:00:00', $fechaHasta . ' 23:59:59']);
        
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Error al obtener estadísticas de facturación: ' . $e->getMessage());
        return false;
    }
}
?>