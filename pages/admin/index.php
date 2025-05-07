<?php
$page_title = 'Panel de Administrador - setTattoo($INK)';
$active_page = 'dashboard';
$base_path = '../../';

require_once '../../utils/auth.php';

// Verificar si el usuario está autenticado y es administrador
if (!estaLogueado() || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ' . $base_path . 'pages/login.php');
    exit;
}

include_once '../../componentes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Panel de Administrador</h1>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Hola! <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h5>
                    <p class="card-text">Este es tu panel de administrador donde podrás gestionar todos los aspectos del estudio de tatuajes.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gestión de Usuarios</h5>
                    <p class="card-text">Administra los usuarios del sistema.</p>
                    <a href="#" class="btn btn-primary">Ir a Usuarios</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gestión de Servicios</h5>
                    <p class="card-text">Administra los servicios ofrecidos.</p>
                    <a href="#" class="btn btn-primary">Ir a Servicios</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reportes</h5>
                    <p class="card-text">Visualiza reportes y estadísticas.</p>
                    <a href="#" class="btn btn-primary">Ver Reportes</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../../componentes/footer.php'; ?>