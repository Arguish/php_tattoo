<?php


require_once 'db_connection.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/logger.php';
require_once __DIR__ . '/../mail/sendMail.php';
require_once __DIR__ . '/../mail/citaConfirmada.php';

logDebug('Intentando procesar acción con request: ' . json_encode($_REQUEST));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_REQUEST['action']) && isset($_REQUEST['target']) && $_REQUEST['target'] == 'reservas') {
    header('Content-Type: application/json');

    logDebug('Intentando procesar acción en reservas: ' . $_REQUEST['action']);
    try {


        switch ($_REQUEST['action']) {
            case 'create':
                if (!checkRole(['admin', 'recepcionista', 'cliente'])) {
                    throw new Exception('Acceso denegado: se requieren privilegios de administrador o artista');
                }
                if (checkRole(['cliente'])) {
                    $reservasPendientes = obtenerReservas('pendiente', $_SESSION['usuario_id']);
                    $reservasConfirmadas = obtenerReservas('confirmada', $_SESSION['usuario_id']);
                    if (count($reservasPendientes) + count($reservasConfirmadas) >= 1) {
                        logWarning('Ya tienes una reserva pendiente o confirmada');
                        throw new Exception('Ya tienes alguna reserva pendiente o confirmada');
                    }
                }
                logDebug('Intentando crear reserva con datos: ' . json_encode($_REQUEST));
                $reservaId = crearReserva($_REQUEST);
                echo json_encode(['success' => $reservaId]);

                logDebug("Intentando enviar correo de confirmación de reserva");
                logDebug("Datos de la reserva: " . json_encode($reservaId));

                if ($reservaId) {
                    // Obtener datos completos de la reserva recién creada
                    $reserva = obtenerReservaPorId($reservaId);

                    logDebug("Intentando enviar correo con datos: " . json_encode($reserva));

                    if ($reserva) {
                        // Obtener email del cliente
                        $pdo = getConnection();
                        $stmt = $pdo->prepare("SELECT email FROM usuarios WHERE id = ?");
                        $stmt->execute([$reserva['cliente_id']]);
                        $emailCliente = $stmt->fetchColumn();

                        // Formatear fecha y hora
                        $fechaHora = $reserva['fecha_hora'];
                        $observaciones = $reserva['observaciones'] ?? '';

                        $mail = setupMail([
                            'subject' => 'Confirmación de Reserva',
                            //'to' => $emailCliente,
                            'to' => "jhergon8@gmail.com",
                            'body' => generarEmailCitaConfirmada(
                                $reserva['cliente_nombre'],
                                $fechaHora,
                                $reserva['artista_nombre'],
                                $reserva['servicio_nombre'],
                                $observaciones
                            )
                        ]);
                        $mail->send();
                    }
                }
                exit;

            case 'update':
                logDebug('Intentando actualizar reserva con ID: ' . json_encode($_REQUEST));

                // Verify permissions: Admin OR artista assigned to the reservation
                $reserva = null;
                if (!empty($_REQUEST['id'])) {
                    $reserva = obtenerReservaPorId($_REQUEST['id']);
                }

                if (!checkRole(['admin', 'recepcionista']) && (!$reserva || $reserva['artista_id'] != $_SESSION['usuario_id'])) {
                    logError('Acceso denegado: se requieren mayores privilegios');
                    throw new Exception('Acceso denegado: se requieren mayores privilegios');
                }

                if (empty($_REQUEST['id'])) {
                    logError('ID de reserva requerido');
                    throw new Exception('ID de reserva requerido');
                }
                logDebug('Intentando actualizar reserva con ID: ' . $_REQUEST['id']);

                $success = actualizarReserva($_REQUEST['id'], $_REQUEST);
                echo json_encode(['success' => $success]);
                exit;

            case 'read':
                logDebug('Intentando obtener reserva con ID: ' . ($_REQUEST['id'] ?? 'todas'));
                $id = $_REQUEST['id'] ?? null;
                if ($id) {
                    $reserva = obtenerReservaPorId($id);
                    logDebug('Reserva obtenida: ' . json_encode($reserva));
                    echo json_encode($reserva ?: ['error' => 'Reserva no encontrada']);
                } else {
                    // Filtros opcionales
                    $estado = $_REQUEST['estado'] ?? null;
                    $clienteId = $_REQUEST['cliente_id'] ?? null;
                    $artistaId = $_REQUEST['artista_id'] ?? null;
                    $fechaDesde = $_REQUEST['fecha_desde'] ?? null;
                    $fechaHasta = $_REQUEST['fecha_hasta'] ?? null;

                    $reservas = obtenerReservas($estado, $clienteId, $artistaId, $fechaDesde, $fechaHasta);
                    logDebug('Reservas obtenidas: ' . count($reservas));
                    echo json_encode($reservas ?: ['error' => 'Error al obtener reservas']);
                }
                exit;

            case 'delete':
                if (!checkRole(['admin', 'recepcionista'])) {
                    throw new Exception('Acceso denegado: se requieren privilegios de administrador');
                }
                logDebug('Intentando eliminar reserva con ID: ' . $_REQUEST['id']);
                if (empty($_REQUEST['id'])) {
                    throw new Exception('ID de reserva requerido');
                }
                if (!eliminarReserva($_REQUEST['id'])) {
                    throw new Exception('Error al eliminar la reserva');
                }
                echo json_encode(['success' => true]);
                exit;

            case 'cambiar_estado':
                logDebug('Intentando cambiar estado de reserva con ID: ' . $_REQUEST['id']);
                if (empty($_REQUEST['id']) || empty($_REQUEST['estado'])) {
                    throw new Exception('ID de reserva y estado requeridos');
                }

                // Verificar permisos: Admin o artista asignado a la reserva
                $reserva = obtenerReservaPorId($_REQUEST['id']);
                if (!checkRole(['admin']) && (!$reserva || $reserva['artista_id'] != $_SESSION['usuario_id'])) {
                    throw new Exception('Acceso denegado: se requieren mayores privilegios');
                }

                $success = cambiarEstadoReserva($_REQUEST['id'], $_REQUEST['estado']);
                echo json_encode(['success' => $success]);
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

function obtenerReservas($estado = null, $clienteId = null, $artistaId = null, $fechaDesde = null, $fechaHasta = null)
{
    logdebug('Obteniendo reservas' . ($estado ? " con estado: $estado" : '') . ($clienteId ? " con cliente ID: $clienteId" : '') . ($artistaId ? " con artista ID: $artistaId" : '') . ($fechaDesde ? " desde $fechaDesde" : '') . ($fechaHasta ? " hasta $fechaHasta" : ''));
    try {
        $pdo = getConnection();
        if (!$pdo) return false;

        $sql = "SELECT r.*, 
                c.nombre as cliente_nombre, a.nombre as artista_nombre, s.nombre as servicio_nombre, s.precio as servicio_precio
               FROM reservas r 
               JOIN usuarios c ON r.cliente_id = c.id
               JOIN usuarios a ON r.artista_id = a.id
               JOIN servicios s ON r.servicio_id = s.id";

        $params = [];
        $where = [];

        if ($estado) {
            $where[] = "r.estado = ?";
            $params[] = $estado;
        }

        if ($clienteId) {
            $where[] = "r.cliente_id = ?";
            $params[] = $clienteId;
        }

        if ($artistaId) {
            $where[] = "r.artista_id = ?";
            $params[] = $artistaId;
        }

        if ($fechaDesde) {
            $where[] = "DATE(r.fecha_hora) >= ?";
            $params[] = $fechaDesde;
        }

        if ($fechaHasta) {
            $where[] = "DATE(r.fecha_hora) <= ?";
            $params[] = $fechaHasta;
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY r.fecha_hora";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        logDebug('SQL obtenido: ' . $sql);
        logDebug('Parámetros obtencion: ' . json_encode($params));
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error al obtener reservas: ' . $e->getMessage());
        return false;
    }
}

function obtenerReservaPorId($id)
{
    try {
        $pdo = getConnection();
        if (!$pdo) return false;

        $stmt = $pdo->prepare("SELECT r.*, 
                              c.nombre as cliente_nombre, 
                              a.nombre as artista_nombre, 
                              s.nombre as servicio_nombre, s.precio as servicio_precio
                              FROM reservas r 
                              JOIN usuarios c ON r.cliente_id = c.id
                              JOIN usuarios a ON r.artista_id = a.id
                              JOIN servicios s ON r.servicio_id = s.id
                              WHERE r.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Error al obtener reserva por ID: ' . $e->getMessage());
        return false;
    }
}

function crearReserva($datos)
{
    try {
        $pdo = getConnection();
        if (!$pdo) return false;

        if (
            empty($datos['cliente_id']) || empty($datos['artista_id']) ||
            empty($datos['servicio_id']) || empty($datos['fecha_hora'])
        ) {
            return false;
        }

        $clienteId = (int)$datos['cliente_id'];
        $artistaId = (int)$datos['artista_id'];
        $servicioId = (int)$datos['servicio_id'];
        $fechaHora = $datos['fecha_hora'];
        $estado = isset($datos['estado']) ? sanitizeInput($datos['estado']) : 'pendiente';
        $observaciones = isset($datos['observaciones']) ? sanitizeInput($datos['observaciones']) : null;

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservas 
                            WHERE artista_id = ? 
                            AND fecha_hora BETWEEN DATE_SUB(?, INTERVAL 30 MINUTE) AND DATE_ADD(?, INTERVAL 30 MINUTE)
                            AND estado IN ('pendiente', 'confirmada')");
        $stmt->execute([$artistaId, $fechaHora, $fechaHora]);
        if ($stmt->fetchColumn() > 0) {
            return false;
        }

        $stmt = $pdo->prepare("INSERT INTO reservas (cliente_id, artista_id, servicio_id, fecha_hora, estado, observaciones) 
                            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$clienteId, $artistaId, $servicioId, $fechaHora, $estado, $observaciones]);

        logDebug('Reserva creada con ID: ' . $pdo->lastInsertId());

        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log('Error al crear reserva: ' . $e->getMessage());
        return false;
    }
}

function actualizarReserva($id, $datos)
{
    try {
        $pdo = getConnection();
        if (!$pdo) return false;

        $campos = [];
        $valores = [];

        if (isset($datos['cliente_id'])) {
            $campos[] = "cliente_id = ?";
            $valores[] = (int)$datos['cliente_id'];
        }

        if (isset($datos['artista_id'])) {
            $campos[] = "artista_id = ?";
            $valores[] = (int)$datos['artista_id'];
        }

        if (isset($datos['servicio_id'])) {
            $campos[] = "servicio_id = ?";
            $valores[] = (int)$datos['servicio_id'];
        }

        if (isset($datos['fecha_hora'])) {
            $campos[] = "fecha_hora = ?";
            $valores[] = $datos['fecha_hora'];
        }

        if (isset($datos['estado'])) {
            $campos[] = "estado = ?";
            $valores[] = sanitizeInput($datos['estado']);
        }

        if (isset($datos['observaciones'])) {
            $campos[] = "observaciones = ?";
            $valores[] = sanitizeInput($datos['observaciones']);
        }

        if (empty($campos)) {
            return false;
        }

        $sql = "UPDATE reservas SET " . implode(", ", $campos) . " WHERE id = ?";
        $valores[] = $id;
        logDebug('SQL: ' . $sql);
        logDebug('Parámetros: ' . json_encode($valores));

        $stmt = $pdo->prepare($sql);
        $stmt->execute($valores);

        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error al actualizar reserva: ' . $e->getMessage());
        return false;
    }
}

function cambiarEstadoReserva($id, $estado)
{
    try {
        $pdo = getConnection();
        if (!$pdo) return false;

        $estadosValidos = ['pendiente', 'confirmada', 'completada', 'cancelada'];
        if (!in_array($estado, $estadosValidos)) {
            return false;
        }

        $stmt = $pdo->prepare("UPDATE reservas SET estado = ? WHERE id = ?");
        $stmt->execute([$estado, $id]);

        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error al cambiar estado de reserva: ' . $e->getMessage());
        return false;
    }
}

function eliminarReserva($id)
{
    try {
        $pdo = getConnection();
        if (!$pdo) return false;

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM facturas WHERE reserva_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            return cambiarEstadoReserva($id, 'cancelada');
        }

        $stmt = $pdo->prepare("DELETE FROM reservas WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error al eliminar reserva: ' . $e->getMessage());
        return false;
    }
}

function obtenerReservasPorDia($artistaId, $fecha)
{
    try {
        $pdo = getConnection();
        if (!$pdo) return false;

        $stmt = $pdo->prepare("SELECT r.*, 
                            c.nombre as cliente_nombre, c.apellido as cliente_apellido,
                            s.nombre as servicio_nombre, s.duracion as servicio_duracion
                            FROM reservas r 
                            JOIN usuarios c ON r.cliente_id = c.id
                            JOIN servicios s ON r.servicio_id = s.id
                            WHERE r.artista_id = ? 
                            AND DATE(r.fecha_hora) = ?
                            AND r.estado IN ('pendiente', 'confirmada')
                            ORDER BY r.fecha_hora");
        $stmt->execute([$artistaId, $fecha]);

        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error al obtener reservas por día: ' . $e->getMessage());
        return false;
    }
}
