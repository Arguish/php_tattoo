<?php


$page_title = 'setTattoo($INK) - Inicio';
$active_page = 'home';
$base_path = '';

include_once 'componentes/header.php';
?>

    <header class="hero-section py-5 text-center text-white bg-dark">
        <div class="container">
            <h1 class="display-4">Hola! a nuestro setTattoo($INK)</h1>
            <p class="lead">Arte y profesionalismo en cada diseño</p>
            <a href="pages/register.php" class="btn btn-primary btn-lg mt-3">Reserva tu cita</a>
        </div>
    </header>

    <section class="services py-5">
        <div class="container">
            <h2 class="text-center mb-4">Nuestros Servicios</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h3 class="card-title">Tatuajes Personalizados</h3>
                            <p class="card-text">Diseños únicos creados específicamente para ti por nuestros artistas profesionales.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h3 class="card-title">Retoques</h3>
                            <p class="card-text">Mejoramos y revitalizamos tus tatuajes existentes para que luzcan como nuevos.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h3 class="card-title">Asesoría</h3>
                            <p class="card-text">Consulta con nuestros expertos para encontrar el diseño perfecto para ti.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
include_once 'componentes/footer.php';
?>