<?php
$page_title = 'Panel de Administrador - setTattoo($INK)';
$active_page = 'dashboard';
$base_path = '../../';

require_once '../../utils/auth.php';


include_once '../../componentes/header.php';
var_dump($_SESSION); // Comentado para producción

$user_role = $_SESSION['usuario_rol'];

$styledRoles = [
    'admin' => 'Administrador',
    'recepcionista' => 'Recepcionista',
    'artista' => 'Artista',
    'cliente' => 'Cliente',
];

// Estilos personalizados para el dashboard
echo '<style>
    .dashboard-card {
        transition: all 0.3s ease;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    @media (max-width: 768px) {
        .dashboard-card {
            width: 100%;
            margin-right: 0 !important;
        }
        .dashboard-card .card {
            width: 100% !important;
        }
    }
</style>';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Panel de <?php echo htmlspecialchars($styledRoles[$user_role]); ?></h1>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Hola! <?php echo htmlspecialchars($_SESSION["usuario_nombre"]); ?></h5>
                    <p class="card-text">Este es tu panel de <?php echo htmlspecialchars($styledRoles[$user_role]); ?> donde podrás gestionar todos los aspectos del estudio de tatuajes.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor flexible para módulos del dashboard -->
    <div class="d-flex flex-wrap justify-content-center mt-4">
        <?php if (in_array($_SESSION['usuario_rol'], ['admin','recepcionista'])): ?>
        <!-- Sección de Gestión de Usuarios -->
        <div class="dashboard-card mb-3 me-3">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Gestión de Usuarios</h5>
                    <p class="card-text">Administra los usuarios del sistema.</p>
                    <a href="configUsers.php" class="btn btn-primary">Ir a Usuarios</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (in_array($_SESSION['usuario_rol'], ['admin','recepcionista'])): ?>
        <!-- Sección de Gestión de Citas -->
        <div class="dashboard-card mb-3 me-3">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Gestión de Citas</h5>
                    <p class="card-text">Administra todas las citas del estudio.</p>
                    <a href="config.php?type=appointment" class="btn btn-primary">Ir a Citas</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (in_array($_SESSION['usuario_rol'], ['admin', 'artista','recepcionista'])): ?>
        <!-- Sección de Gestión de Servicios -->
        <div class="dashboard-card mb-3 me-3">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Gestión de Servicios</h5>
                    <p class="card-text">Administra los servicios ofrecidos.</p>
                    <a href="config.php?type=tattoo" class="btn btn-primary">Ir a Servicios</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (in_array($_SESSION['usuario_rol'], ['admin','recepcionista'])): ?>
        <!-- Sección de Gestión de Clientes -->
        <div class="dashboard-card mb-3 me-3">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Gestión de Clientes</h5>
                    <p class="card-text">Administra la información de los clientes.</p>
                    <a href="config.php?type=user&filter=client" class="btn btn-primary">Ir a Clientes</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (in_array($_SESSION['usuario_rol'], ['admin', 'artista'])): ?>
        <!-- Sección de Gestión de Artistas -->
        <div class="dashboard-card mb-3 me-3">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Portafolio de Artistas</h5>
                    <p class="card-text">Administra el portafolio de todos los artistas.</p>
                    <a href="config.php?type=tattoo&filter=portfolio" class="btn btn-primary">Ir al Portafolio</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (in_array($_SESSION['usuario_rol'], ['admin','recepcionista'])): ?>
        <!-- Sección de Facturación -->
        <div class="dashboard-card mb-3 me-3">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Facturación</h5>
                    <p class="card-text">Gestiona facturas y pagos del estudio.</p>
                    <a href="config.php?type=invoice" class="btn btn-primary">Ir a Facturas</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (in_array($_SESSION['usuario_rol'], ['admin'])): ?>
        <!-- Sección de Reportes -->
        <div class="dashboard-card mb-3 me-3">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Reportes</h5>
                    <p class="card-text">Visualiza reportes y estadísticas.</p>
                    <a href="config.php?type=report" class="btn btn-primary">Ver Reportes</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (in_array($_SESSION['usuario_rol'], ['admin'])): ?>
        <!-- Sección de Configuración -->
        <div class="dashboard-card mb-3 me-3">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Configuración</h5>
                    <p class="card-text">Configura parámetros del sistema.</p>
                    <a href="config.php?type=setting" class="btn btn-primary">Ir a Configuración</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once '../../componentes/footer.php'; ?>