<?php
$page_title = 'Gestión de Usuarios - setTattoo($INK)';
$active_page = 'usuarios';
$base_path = '../../';

require_once '../../utils/auth.php';

// Verificar si el usuario está autenticado y es administrador
if (!estaLogueado() || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ' . $base_path . 'pages/login.php');
    exit;
}

// Incluir servicios necesarios
require_once '../../services/usuarios.php';

// Obtener todos los usuarios
$usuarios = obtenerUsuarios();

include_once '../../componentes/header.php';
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Gestión de Usuarios</h1>
            <p class="lead">Administra todos los usuarios del sistema</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
                <i class="bi bi-person-plus"></i> Nuevo Usuario
            </a>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="filtroRol" class="form-label">Filtrar por rol</label>
                    <select class="form-select" id="filtroRol" name="rol">
                        <option value="">Todos los roles</option>
                        <option value="admin">Administrador</option>
                        <option value="artista">Artista</option>
                        <option value="recepcionista">Recepcionista</option>
                        <option value="cliente">Cliente</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filtroEstado" class="form-label">Estado</label>
                    <select class="form-select" id="filtroEstado" name="estado">
                        <option value="">Todos</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="busqueda" class="form-label">Buscar</label>
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

    <!-- Tabla de usuarios -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($usuarios) && !empty($usuarios)) : ?>
                            <?php foreach ($usuarios as $usuario) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getRolBadgeClass($usuario['rol']); ?>">
                                            <?php echo htmlspecialchars(ucfirst($usuario['rol'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $usuario['activo'] ? 'success' : 'danger'; ?>">
                                            <?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($usuario['fecha_registro']); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditarUsuario" data-id="<?php echo $usuario['id']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="#" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalEliminarUsuario" data-id="<?php echo $usuario['id']; ?>">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            <a href="#" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalResetPassword" data-id="<?php echo $usuario['id']; ?>">
                                                <i class="bi bi-key"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="7" class="text-center">No se encontraron usuarios</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Usuario -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNuevoUsuarioLabel">Crear Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevoUsuario" action="../../includes/admin/usuario_process.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol</label>
                        <select class="form-select" id="rol" name="rol" required>
                            <option value="">Seleccionar rol</option>
                            <option value="admin">Administrador</option>
                            <option value="artista">Artista</option>
                            <option value="recepcionista">Recepcionista</option>
                            <option value="cliente">Cliente</option>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" checked>
                        <label class="form-check-label" for="activo">Usuario activo</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formNuevoUsuario" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarUsuario" action="../../includes/admin/usuario_process.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label for="edit_nombre" class="form-label">Nombre completo</label>
                        <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_rol" class="form-label">Rol</label>
                        <select class="form-select" id="edit_rol" name="rol" required>
                            <option value="">Seleccionar rol</option>
                            <option value="admin">Administrador</option>
                            <option value="artista">Artista</option>
                            <option value="recepcionista">Recepcionista</option>
                            <option value="cliente">Cliente</option>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_activo" name="activo" value="1">
                        <label class="form-check-label" for="edit_activo">Usuario activo</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formEditarUsuario" class="btn btn-primary">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar Usuario -->
<div class="modal fade" id="modalEliminarUsuario" tabindex="-1" aria-labelledby="modalEliminarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarUsuarioLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.</p>
                <form id="formEliminarUsuario" action="../../includes/admin/usuario_process.php" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formEliminarUsuario" class="btn btn-danger">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade" id="modalResetPassword" tabindex="-1" aria-labelledby="modalResetPasswordLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalResetPasswordLabel">Restablecer Contraseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formResetPassword" action="../../includes/admin/usuario_process.php" method="POST">
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="id" id="reset_id">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nueva contraseña</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmar contraseña</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formResetPassword" class="btn btn-primary">Restablecer</button>
            </div>
        </div>
    </div>
</div>

<script>
// Función para cargar datos de usuario en el modal de edición
document.addEventListener('DOMContentLoaded', function() {
    const modalEditarUsuario = document.getElementById('modalEditarUsuario');
    if (modalEditarUsuario) {
        modalEditarUsuario.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-id');
            
            // Aquí deberías hacer una petición AJAX para obtener los datos del usuario
            // Por ahora, simulamos con datos de la tabla
            const userRow = button.closest('tr');
            const userName = userRow.cells[1].textContent.trim();
            const userEmail = userRow.cells[2].textContent.trim();
            const userRol = userRow.cells[3].querySelector('.badge').textContent.trim().toLowerCase();
            const userActivo = userRow.cells[4].querySelector('.badge').textContent.trim() === 'Activo';
            
            // Rellenar el formulario
            document.getElementById('edit_id').value = userId;
            document.getElementById('edit_nombre').value = userName;
            document.getElementById('edit_email').value = userEmail;
            document.getElementById('edit_rol').value = userRol;
            document.getElementById('edit_activo').checked = userActivo;
        });
    }
    
    // Configurar modales de eliminar y reset password
    const modalEliminarUsuario = document.getElementById('modalEliminarUsuario');
    if (modalEliminarUsuario) {
        modalEliminarUsuario.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-id');
            document.getElementById('delete_id').value = userId;
        });
    }
    
    const modalResetPassword = document.getElementById('modalResetPassword');
    if (modalResetPassword) {
        modalResetPassword.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-id');
            document.getElementById('reset_id').value = userId;
        });
    }
});

// Función auxiliar para validar contraseñas coincidentes
document.getElementById('formResetPassword').addEventListener('submit', function(event) {
    const password = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        event.preventDefault();
        alert('Las contraseñas no coinciden');
    }
});
</script>

<?php
// Función auxiliar para determinar la clase de la insignia según el rol
function getRolBadgeClass($rol) {
    switch ($rol) {
        case 'admin':
            return 'danger';
        case 'artista':
            return 'primary';
        case 'recepcionista':
            return 'info';
        case 'cliente':
            return 'success';
        default:
            return 'secondary';
    }
}

include_once '../../componentes/footer.php';
?>