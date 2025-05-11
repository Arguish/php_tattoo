<?php
$page_title = 'Registro - setTattoo($INK)';
$active_page = 'register';
$base_path = '../';

require_once '../utils/messages.php';

include_once '../componentes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-container">
                    <h2 class="text-center mb-4">Crear una cuenta</h2>

                    <?php echo mostrarMensajes(); ?>

                    <form action="../includes/register_process.php" method="post" id="registerForm">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre completo</label>
                            <input type="text" class="form-control <?php echo campoTieneError('nombre') ? 'is-invalid' : ''; ?>"
                                id="nombre" name="nombre" value="<?php echo valorAnteriorCampo('nombre'); ?>" required>
                            <?php if (campoTieneError('nombre')): ?>
                                <div class="invalid-feedback"><?php echo mensajeErrorCampo('nombre'); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control <?php echo campoTieneError('email') ? 'is-invalid' : ''; ?>"
                                id="email" name="email" value="<?php echo valorAnteriorCampo('email'); ?>" required>
                            <?php if (campoTieneError('email')): ?>
                                <div class="invalid-feedback"><?php echo mensajeErrorCampo('email'); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control <?php echo campoTieneError('password') ? 'is-invalid' : ''; ?>"
                                id="password" name="password" required>
                            <?php if (campoTieneError('password')): ?>
                                <div class="invalid-feedback"><?php echo mensajeErrorCampo('password'); ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted">La contraseña debe tener al menos 6 caracteres y contener al menos un número.</small> <!-- TODO: cambiar color del texto -->
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control <?php echo campoTieneError('confirm_password') ? 'is-invalid' : ''; ?>"
                                id="confirm_password" name="confirm_password" required>
                            <?php if (campoTieneError('confirm_password')): ?>
                                <div class="invalid-feedback"><?php echo mensajeErrorCampo('confirm_password'); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input <?php echo campoTieneError('terms') ? 'is-invalid' : ''; ?>"
                                id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">Acepto los términos y condiciones</label>
                            <?php if (campoTieneError('terms')): ?>
                                <div class="invalid-feedback"><?php echo mensajeErrorCampo('terms'); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Registrarse</button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include_once '../componentes/footer.php';
?>