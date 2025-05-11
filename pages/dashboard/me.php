<?php
$page_title = 'Panel de Administrador - setTattoo($INK)';
$active_page = 'dashboard';
$base_path = '../../';
require_once __DIR__ . '/../../componentes/header.php';
require_once __DIR__ . '/../../services/usuarios.php';

// Obtener todos los usuarios
$data = obtenerUsuarioPorId($_SESSION['usuario_id']);
?>


<div class="container-fluid mt-4">
    <h2 class="text-light mb-4 bg-dark p-3 rounded">Gestión de Usuarios</h2>
</div>

<form id="editForm" onsubmit="return submitForm(event)">
    <input type="hidden" name="id" id="editUserId" value="<?= $data['id'] ?? '' ?>">
    <input type="hidden" name="rol" value="<?= $data['rol'] ?? '' ?>">

    <div class="mb-3">
        <label class="form-label text-dark">Nombre:</label>
        <input type="text" name="nombre" class="form-control" required value="<?= $data['nombre'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label text-dark">Email:</label>
        <input type="email" name="email" value="<?= $data['email'] ?? '' ?>" class="form-control" required>
    </div>


    <div class="mb-3">
        <label class="form-label text-dark">Teléfono:</label>
        <input type="tel" name="telefono" value="<?= $data['telefono'] ?? '' ?>" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
</form>



<script>
    function submitForm(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const userId = formData.get('id');

        fetch(`../../services/usuarios.php?action=update&id=${userId}`, {
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
</script>

<?php
require_once __DIR__ . '/../../componentes/footer.php';
?>