<?php
$page_title = 'Gestión de Citas - setTattoo($INK)';
$active_page = 'citas';
$base_path = '../../';

require_once '../../utils/auth.php';

// Verificar si el usuario está autenticado y es administrador
if (!estaLogueado() || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ' . $base_path . 'pages/login.php');
    exit;
}

// Incluir servicios necesarios
require_once '../../services/reservas.php';
require_once '../../services/usuarios.php';

// Obtener todas las citas
$citas = obtenerReservas();

// Obtener artistas para el filtro
$artistas = obtenerUsuarios('artista');

// Obtener clientes para el formulario
$clientes = obtenerUsuarios('cliente');

// Obtener servicios para el formulario
require_once '../../services/servicios.php';
$servicios = obtenerServicios(true);
include_once '../../componentes/header.php';
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Gestión de Citas</h1>
            <p class="lead">Administra todas las citas del estudio</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevaCita">
                <i class="bi bi-calendar-plus"></i> Nueva Cita
            </a>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="filtroArtista" class="form-label">Artista</label>
                    <select class="form-select" id="filtroArtista" name="artista_id">
                        <option value="">Todos los artistas</option>
                        <?php foreach ($artistas as $artista): ?>
                            <option value="<?php echo $artista['id']; ?>">
                                <?php echo htmlspecialchars($artista['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroEstado" class="form-label">Estado</label>
                    <select class="form-select" id="filtroEstado" name="estado">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="confirmada">Confirmada</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroFecha" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="filtroFecha" name="fecha">
                </div>
                <div class="col-md-3">
                    <label for="busqueda" class="form-label">Buscar cliente</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="busqueda" name="q" placeholder="Nombre, email...">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Calendario de citas -->
    <div class="card mb-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="citasTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="calendario-tab" data-bs-toggle="tab" data-bs-target="#calendario" type="button" role="tab" aria-controls="calendario" aria-selected="true">Calendario</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="lista-tab" data-bs-toggle="tab" data-bs-target="#lista" type="button" role="tab" aria-controls="lista" aria-selected="false">Lista</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="citasTabsContent">
                <!-- Vista de Calendario -->
                <div class="tab-pane fade show active" id="calendario" role="tabpanel" aria-labelledby="calendario-tab">
                    <div id="calendar"></div>
                </div>
                
                <!-- Vista de Lista -->
                <div class="tab-pane fade" id="lista" role="tabpanel" aria-labelledby="lista-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Artista</th>
                                    <th>Servicio</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($citas) && !empty($citas)) : ?>
                                    <?php foreach ($citas as $cita) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($cita['id']); ?></td>
                                            <td><?php echo htmlspecialchars($cita['cliente_nombre']); ?></td>
                                            <td><?php echo htmlspecialchars($cita['artista_nombre']); ?></td>
                                            <td><?php echo htmlspecialchars($cita['servicio_nombre']); ?></td>
                                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($cita['fecha']))); ?></td>
                                            <td><?php echo htmlspecialchars(date('H:i', strtotime($cita['hora']))); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo getEstadoCitaBadgeClass($cita['estado']); ?>">
                                                    <?php echo htmlspecialchars(ucfirst($cita['estado'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditarCita" data-id="<?php echo $cita['id']; ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalCancelarCita" data-id="<?php echo $cita['id']; ?>">
                                                        <i class="bi bi-x-circle"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalCompletarCita" data-id="<?php echo $cita['id']; ?>">
                                                        <i class="bi bi-check-circle"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No se encontraron citas</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Cita -->
<div class="modal fade" id="modalNuevaCita" tabindex="-1" aria-labelledby="modalNuevaCitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNuevaCitaLabel">Crear Nueva Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevaCita" action="../../services/reservas.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cliente_id" class="form-label">Cliente</label>
                            <select class="form-select" id="cliente_id" name="cliente_id" required>
                                <option value="">Seleccionar cliente</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?php echo $cliente['id']; ?>">
                                        <?php echo htmlspecialchars($cliente['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="artista_id" class="form-label">Artista</label>
                            <select class="form-select" id="artista_id" name="artista_id" required>
                                <option value="">Seleccionar artista</option>
                                <?php foreach ($artistas as $artista): ?>
                                    <option value="<?php echo $artista['id']; ?>">
                                        <?php echo htmlspecialchars($artista['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="servicio_id" class="form-label">Servicio</label>
                            <select class="form-select" id="servicio_id" name="servicio_id" required>
                                <option value="">Seleccionar servicio</option>
                                <?php foreach ($servicios as $servicio): ?>
                                    <option value="<?php echo $servicio['id']; ?>">
                                        <?php echo htmlspecialchars($servicio['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="duracion" class="form-label">Duración (minutos)</label>
                            <input type="number" class="form-control" id="duracion" name="duracion" min="30" step="30" value="60" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hora" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="hora" name="hora" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas</label>
                        <textarea class="form-control" id="notas" name="notas" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formNuevaCita" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Cita -->
<div class="modal fade" id="modalEditarCita" tabindex="-1" aria-labelledby="modalEditarCitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarCitaLabel">Editar Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarCita" action="../../services/reservas.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <!-- Aquí irían los mismos campos que en el formulario de nueva cita -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formEditarCita" class="btn btn-primary">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cancelar Cita -->
<div class="modal fade" id="modalCancelarCita" tabindex="-1" aria-labelledby="modalCancelarCitaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCancelarCitaLabel">Cancelar Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas cancelar esta cita?</p>
                <form id="formCancelarCita" action="../../services/reservas.php" method="POST">
                    <input type="hidden" name="action" value="cancel">
                    <input type="hidden" name="id" id="cancel_id">
                    <div class="mb-3">
                        <label for="motivo_cancelacion" class="form-label">Motivo de cancelación</label>
                        <textarea class="form-control" id="motivo_cancelacion" name="motivo_cancelacion" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" form="formCancelarCita" class="btn btn-danger">Cancelar Cita</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Completar Cita -->
<div class="modal fade" id="modalCompletarCita" tabindex="-1" aria-labelledby="modalCompletarCitaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCompletarCitaLabel">Completar Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Confirmas que esta cita ha sido completada?</p>
                <form id="formCompletarCita" action="../../services/reservas.php" method="POST">
                    <input type="hidden" name="action" value="complete">
                    <input type="hidden" name="id" id="complete_id">
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="generar_factura" name="generar_factura" value="1">
                        <label class="form-check-label" for="generar_factura">Generar factura automáticamente</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" form="formCompletarCita" class="btn btn-success">Completar Cita</button>
            </div>
        </div>
    </div>
</div>

<script>
// Inicializar calendario cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Aquí iría el código para inicializar el calendario con FullCalendar.js
    // Este es un ejemplo básico, se necesitaría incluir la librería FullCalendar
    
    // --- Lógica de filtrado cruzado artista-servicio ---
    // Crear un mapa de servicios por artista
    var serviciosPorArtista = {};
    <?php
    // Agrupar servicios por artista_id
    $mapServicios = [];
    foreach ($servicios as $servicio) {
        $aid = $servicio['artista_id'];
        if (!isset($mapServicios[$aid])) $mapServicios[$aid] = [];
        $mapServicios[$aid][] = [
            'id' => $servicio['id'],
            'nombre' => $servicio['nombre']
        ];
    }
    ?>
    serviciosPorArtista = <?php echo json_encode($mapServicios); ?>;

    // Guardar todos los servicios para restaurar si no hay artista seleccionado
    var todosServicios = <?php echo json_encode(array_map(function($s){return ['id'=>$s['id'],'nombre'=>$s['nombre']];}, $servicios)); ?>;

    function actualizarServiciosPorArtista(artistaId) {
        var selectServicio = document.getElementById('servicio_id');
        selectServicio.innerHTML = '<option value="">Seleccionar servicio</option>';
        var servicios = artistaId && serviciosPorArtista[artistaId] ? serviciosPorArtista[artistaId] : todosServicios;
        servicios.forEach(function(servicio) {
            var opt = document.createElement('option');
            opt.value = servicio.id;
            opt.textContent = servicio.nombre;
            selectServicio.appendChild(opt);
        });
    }

    var selectArtista = document.getElementById('artista_id');
    if (selectArtista) {
        selectArtista.addEventListener('change', function() {
            actualizarServiciosPorArtista(this.value);
        });
        // Inicializar servicios según artista seleccionado por defecto
        actualizarServiciosPorArtista(selectArtista.value);
    }

    // Configurar modales
    const modalEditarCita = document.getElementById('modalEditarCita');
    if (modalEditarCita) {
        modalEditarCita.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const citaId = button.getAttribute('data-id');
            document.getElementById('edit_id').value = citaId;
            // Aquí se cargarían los datos de la cita mediante AJAX
        });
    }
    
    const modalCancelarCita = document.getElementById('modalCancelarCita');
    if (modalCancelarCita) {
        modalCancelarCita.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const citaId = button.getAttribute('data-id');
            document.getElementById('cancel_id').value = citaId;
        });
    }
    
    const modalCompletarCita = document.getElementById('modalCompletarCita');
    if (modalCompletarCita) {
        modalCompletarCita.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const citaId = button.getAttribute('data-id');
            document.getElementById('complete_id').value = citaId;
        });
    }
});
</script>

<?php
// Función auxiliar para determinar la clase de la insignia según el estado de la cita
function getEstadoCitaBadgeClass($estado) {
    switch ($estado) {
        case 'pendiente':
            return 'warning';
        case 'confirmada':
            return 'primary';
        case 'completada':
            return 'success';
        case 'cancelada':
            return 'danger';
        default:
            return 'secondary';
    }
}

include_once '../../componentes/footer.php';
?>