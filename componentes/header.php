<?php
// importar esaLogueado de auth.php
require_once __DIR__ . '/../utils/auth.php';

// Verificar si el usuario está autenticado
$usuario_autenticado = estaLogueado();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'setTattoo($INK)'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $base_path; ?>index.php">setTattoo($INK)</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active_page === 'home' ? 'active' : ''; ?>" <?php echo $active_page === 'home' ? 'aria-current="page"' : ''; ?> href="<?php echo $base_path; ?>index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active_page === 'about' ? 'active' : ''; ?>" <?php echo $active_page === 'about' ? 'aria-current="page"' : ''; ?> href="<?php echo $base_path; ?>pages/about.php">Nosotros</a>
                    </li>
                    <?php if ($usuario_autenticado): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active_page === 'dashboard' ? 'active' : ''; ?>" <?php echo $active_page === 'dashboard' ? 'aria-current="page"' : ''; ?> href="<?php echo $base_path; ?>pages/dashboard">Mi cuenta</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_path; ?>includes/logout.php">Cerrar Sesión</a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active_page === 'login' ? 'active' : ''; ?>" <?php echo $active_page === 'login' ? 'aria-current="page"' : ''; ?> href="<?php echo $base_path; ?>pages/login.php">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active_page === 'register' ? 'active' : ''; ?>" <?php echo $active_page === 'register' ? 'aria-current="page"' : ''; ?> href="<?php echo $base_path; ?>pages/register.php">Registrarse</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>