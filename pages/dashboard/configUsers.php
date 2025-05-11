<?php
$page_title = 'Panel de Administrador - setTattoo($INK)';
$active_page = 'dashboard';
$base_path = '../../';
require_once __DIR__ . '/../../componentes/header.php';
require_once __DIR__ . '/../../services/usuarios.php';

// Obtener todos los usuarios
$usuarios = obtenerUsuarios();
?>

<div class="container-fluid mt-4">
    <h2 class="text-light mb-4 bg-dark p-3 rounded">Gestión de Usuarios</h2>

    <div>
        <form class="d-flex justify-content-center" id="createForm" onsubmit="return nuevoUsuario(event)">
            <div class="mb-3">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Rol:</label>
                <select name="rol" class="form-select" required>
                    <option value="admin">Admin</option>
                    <option value="artista">Artista</option>
                    <option value="cliente">Cliente</option>
                    <option value="recepcionista">Recepcionista</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Teléfono:</label>
                <input type="tel" name="telefono" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Nuevo Usuario</button>
        </form>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-light">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Teléfono</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= htmlspecialchars($usuario['id']) ?></td>
                                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                <td><span class="badge bg-primary"><?= htmlspecialchars($usuario['rol']) ?></span></td>
                                <td><?= htmlspecialchars($usuario['telefono'] ?? 'N/A') ?></td>
                                <td><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="openEditModal(<?= $usuario['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $usuario['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
                <h5 class="modal-title">Editar Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Formulario de edición -->
                <form id="editForm" onsubmit="return submitForm(event)">
                    <input type="hidden" name="id" id="editUserId">

                    <div class="mb-3">
                        <label class="form-label text-dark">Nombre:</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark">Email:</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark">Rol:</label>
                        <select name="rol" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="artista">Artista</option>
                            <option value="cliente">Cliente</option>
                            <option value="recepcionista">Recepcionista</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark">Teléfono:</label>
                        <input type="tel" name="telefono" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openEditModal(userId) {
        console.log(userId);

        fetch(`../../services/usuarios.php?target=users&action=read&id=${userId}`, {
                method: 'POST',
                target: 'users',
            })
            .then(response => response.json())
            .then(data => {
                console.log(data.email);
                document.getElementById('editUserId').value = data.id;
                document.querySelector('#editModal input[name="nombre"]').value = data.nombre;
                document.querySelector('#editModal input[name="email"]').value = data.email;
                document.querySelector('#editModal select[name="rol"]').value = data.rol;
                document.querySelector('#editModal input[name="telefono"]').value = data.telefono || '';


            }).finally(() => new bootstrap.Modal(document.getElementById('editModal')).show());
    }

    function submitForm(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const userId = formData.get('id');

        fetch(`../../services/usuarios.php?target=users&action=update&id=${userId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error al actualizar el usuario');
                }
            });

        return false;
    }

    function nuevoUsuario(event) {
        event.preventDefault();
        console.log('Submission started');

        const formData = new FormData(document.getElementById('createForm'));

        fetch(`../../services/usuarios.php?target=users&action=create`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log(response);
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error al eliminar el usuario');
                }
            });
    }

    function confirmDelete(userId) {
        if (confirm('¿Estás seguro de eliminar este usuario?')) {
            fetch(`../../services/usuarios.php?target=users&action=delete&id=${userId}`, {
                    method: 'POST',
                    target: 'users'
                })
                .then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Error al eliminar el usuario');
                    }
                });
        }
    }
</script>

<?php
require_once __DIR__ . '/../../componentes/footer.php';
?>