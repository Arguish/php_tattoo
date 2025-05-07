<?php
$page_title = 'Panel de Recepcionista - setTattoo($INK)';
$active_page = 'dashboard';
$base_path = '../../';

require_once '../../utils/auth.php';

// Verificar si el usuario está autenticado y es recepcionista
if (!estaLogueado() || $_SESSION['usuario_rol'] !== 'recepcionista') {
    header('Location: ' . $base_path . 'pages/login.php');
    exit;
}

include_once '../../componentes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Panel de Recepcionista</h1>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Hola! <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h5>
                    <p class="card-text">Este es tu panel de recepcionista donde podrás gestionar citas y clientes.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gestión de Citas</h5>
                    <p class="card-text">Administra las citas del estudio.</p>
                    <a href="#" class="btn btn-primary">Ir a Citas</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gestión de Clientes</h5>
                    <p class="card-text">Administra la información de los clientes.</p>
                    <a href="#" class="btn btn-primary">Ir a Clientes</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Calendario</h5>
                    <p class="card-text">Visualiza el calendario de citas del estudio.</p>
                    <a href="#" class="btn btn-primary">Ver Calendario</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../../componentes/footer.php'; ?>