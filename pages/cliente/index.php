<?php
$page_title = 'Mi Cuenta - setTattoo($INK)';
$active_page = 'dashboard';
$base_path = '../../';

require_once '../../utils/auth.php';

// Verificar si el usuario está autenticado y es cliente
if (!estaLogueado() || $_SESSION['usuario_rol'] !== 'cliente') {
    header('Location: ' . $base_path . 'pages/login.php');
    exit;
}

include_once '../../componentes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Mi Cuenta</h1>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Hola! <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h5>
                    <p class="card-text">Este es tu panel personal donde podrás gestionar tus citas y ver tu historial de tatuajes.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Mis Citas</h5>
                    <p class="card-text">Visualiza y gestiona tus próximas citas.</p>
                    <a href="#" class="btn btn-primary">Ver Citas</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Historial</h5>
                    <p class="card-text">Revisa tu historial de tatuajes y servicios.</p>
                    <a href="#" class="btn btn-primary">Ver Historial</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Mi Perfil</h5>
                    <p class="card-text">Actualiza tu información personal.</p>
                    <a href="#" class="btn btn-primary">Editar Perfil</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../../componentes/footer.php'; ?>