<?php
$page_title = 'Panel de Administrador - setTattoo($INK)';
$active_page = 'dashboard';
$base_path = '../../';
require_once __DIR__ . '/../../componentes/header.php';
require_once __DIR__ . '/../../services/reservas.php';
require_once __DIR__ . '/../../services/usuarios.php';
require_once __DIR__ . '/../../services/servicios.php';

// Obtener todas las reservas
$reservas = obtenerReservas();
// Obtener artistas para el formulario
$artistas = obtenerUsuarios('artista');
// Obtener clientes para el formulario
$clientes = obtenerUsuarios('cliente');
// Obtener servicios para el formulario
$servicios = obtenerServicios();
?>

<div class="container-fluid mt-4">
    <h2 class="text-light mb-4 bg-dark p-3 rounded">Gestión de Reservas
        <span><a href="reportPdf.php" class="btn btn-primary ">Descargar</a></span>
    </h2>

    <div>
        <form class="d-flex justify-content-center" id="createForm" onsubmit="return nuevaReserva(event)">
            <div class="mb-3">
                <label class="form-label">Cliente:</label>
                <select name="cliente_id" class="form-select" required>
                    <option value="">Seleccione un cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?= $cliente['id'] ?>"><?= htmlspecialchars($cliente['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Artista:</label>
                <select name="artista_id" class="form-select" required>
                    <option value="">Seleccione un artista</option>
                    <?php foreach ($artistas as $artista): ?>
                        <option value="<?= $artista['id'] ?>"><?= htmlspecialchars($artista['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Servicio:</label>
                <select name="servicio_id" class="form-select" required>
                    <option value="">Seleccione un servicio</option>
                    <?php foreach ($servicios as $servicio): ?>
                        <option value="<?= $servicio['id'] ?>"><?= htmlspecialchars($servicio['nombre']) ?> - <?= htmlspecialchars($servicio['precio']) ?>€</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha y Hora:</label>
                <input type="datetime-local" name="fecha_hora" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Observaciones:</label>
                <textarea name="observaciones" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Nueva Reserva</button>
        </form>
    </div>

    <div class="card shadow mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-light">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Artista</th>
                            <th>Servicio</th>
                            <th>Fecha y Hora</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $reserva): ?>
                            <tr>
                                <td><?= htmlspecialchars($reserva['id']) ?></td>
                                <td><?= htmlspecialchars($reserva['cliente_nombre']) ?></td>
                                <td><?= htmlspecialchars($reserva['artista_nombre']) ?></td>
                                <td><?= htmlspecialchars($reserva['servicio_nombre']) ?> - <?= htmlspecialchars($reserva['servicio_precio']) ?>€</td>
                                <td><?= date('d/m/Y H:i', strtotime($reserva['fecha_hora'])) ?></td>
                                <td><span class="badge bg-<?= getEstadoBadgeClass($reserva['estado']) ?>"><?= htmlspecialchars($reserva['estado']) ?></span></td>
                                <td><?= htmlspecialchars($reserva['observaciones'] ?? 'N/A') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="openEditModal(<?= $reserva['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $reserva['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-info dropdown-toggle" data-bs-toggle="dropdown">
                                            Estado
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="cambiarEstado(<?= $reserva['id'] ?>, 'pendiente')">Pendiente</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="cambiarEstado(<?= $reserva['id'] ?>, 'confirmada')">Confirmada</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="cambiarEstado(<?= $reserva['id'] ?>, 'completada')">Completada</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="cambiarEstado(<?= $reserva['id'] ?>, 'cancelada')">Cancelada</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edición -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-light">
                <h5 class="modal-title">Editar Reserva</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Formulario de edición -->
                <form id="editForm" onsubmit="return submitForm(event)">
                    <input type="hidden" name="id" id="editReservaId">

                    <div class="mb-3">
                        <label class="form-label text-dark">Cliente:</label>
                        <select name="cliente_id" class="form-select" required>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['id'] ?>"><?= htmlspecialchars($cliente['nombre'] . ' ' . ($cliente['apellido'] ?? '')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark">Artista:</label>
                        <select name="artista_id" class="form-select" required>
                            <?php foreach ($artistas as $artista): ?>
                                <option value="<?= $artista['id'] ?>"><?= htmlspecialchars($artista['nombre'] . ' ' . ($artista['apellido'] ?? '')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark">Servicio:</label>
                        <select name="servicio_id" class="form-select" required>
                            <?php foreach ($servicios as $servicio): ?>
                                <option value="<?= $servicio['id'] ?>"><?= htmlspecialchars($servicio['nombre']) ?> - <?= htmlspecialchars($servicio['precio']) ?>€</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark">Fecha y Hora:</label>
                        <input type="datetime-local" name="fecha_hora" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark">Estado:</label>
                        <select name="estado" class="form-select" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="confirmada">Confirmada</option>
                            <option value="completada">Completada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark">Observaciones:</label>
                        <textarea name="observaciones" class="form-control" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Función para obtener la clase de badge según el estado
function getEstadoBadgeClass($estado)
{
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
?>

<script>
    function openEditModal(reservaId) {
        console.log(reservaId);

        fetch(`../../services/reservas.php?target=reservas&action=read&id=${reservaId}`, {
                method: 'POST',
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('editReservaId').value = data.id;
                document.querySelector('#editModal select[name="cliente_id"]').value = data.cliente_id;
                document.querySelector('#editModal select[name="artista_id"]').value = data.artista_id;
                document.querySelector('#editModal select[name="servicio_id"]').value = data.servicio_id;

                // Formatear fecha y hora para el input datetime-local
                const fechaHora = new Date(data.fecha_hora);
                const fechaFormateada = fechaHora.toISOString().slice(0, 16);
                document.querySelector('#editModal input[name="fecha_hora"]').value = fechaFormateada;

                document.querySelector('#editModal select[name="estado"]').value = data.estado;
                document.querySelector('#editModal textarea[name="observaciones"]').value = data.observaciones || '';
            })
            .finally(() => new bootstrap.Modal(document.getElementById('editModal')).show());
    }

    function submitForm(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const reservaId = formData.get('id');

        fetch(`../../services/reservas.php?target=reservas&action=update&id=${reservaId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error al actualizar la reserva');
                }
            });

        return false;
    }

    function nuevaReserva(event) {
        event.preventDefault();
        console.log('Creando nueva reserva');

        const formData = new FormData(document.getElementById('createForm'));

        fetch(`../../services/reservas.php?target=reservas&action=create`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log(response);
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error al crear la reserva');
                }
            });
    }

    function confirmDelete(reservaId) {
        if (confirm('¿Estás seguro de eliminar esta reserva?')) {
            fetch(`../../services/reservas.php?target=reservas&action=delete&id=${reservaId}`, {
                    method: 'POST',

                })
                .then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Error al eliminar la reserva');
                    }
                });
        }
    }

    function cambiarEstado(reservaId, estado) {
        fetch(`../../services/reservas.php?target=reservas&action=cambiar_estado&id=${reservaId}&estado=${estado}`, {
                method: 'POST',
            })
            .then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error al cambiar el estado de la reserva');
                }
            });
    }
</script>

<?php
require_once __DIR__ . '/../../componentes/footer.php';
?>