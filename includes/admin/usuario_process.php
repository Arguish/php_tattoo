<?php
/**
 * Procesamiento de operaciones CRUD para usuarios desde el panel de administrador
 */

require_once '../../utils/auth.php';
require_once '../../utils/validation.php';
require_once '../../utils/logger.php';
require_once '../../services/usuarios.php';

// Verificar si el usuario está autenticado y es administrador
if (!estaLogueado() || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ../../pages/login.php');
    exit;
}

// Verificar que se haya enviado una acción
if (!isset($_POST['action'])) {
    redireccionarConMensaje('../../pages/admin/usuarios.php', 'Acción no especificada', 'error');
    exit;
}

$action = $_POST['action'];

switch ($action) {
    case 'create':
        crearUsuario();
        break;
    case 'update':
        actualizarUsuario();
        break;
    case 'delete':
        eliminarUsuario();
        break;
    case 'reset_password':
        resetearPassword();
        break;
    default:
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'Acción no válida', 'error');
        break;
}

/**
 * Función para crear un nuevo usuario
 */
function crearUsuario() {
    // Validar campos obligatorios
    $campos_requeridos = ['nombre', 'email', 'password', 'rol'];
    foreach ($campos_requeridos as $campo) {
        if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
            redireccionarConMensaje('../../pages/admin/usuarios.php', 'Todos los campos son obligatorios', 'error');
            exit;
        }
    }

    // Sanitizar y validar datos
    $nombre = sanitizeInput($_POST['nombre']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password']; // No sanitizamos la contraseña para no alterar su valor
    $rol = sanitizeInput($_POST['rol']);
    $activo = isset($_POST['activo']) ? 1 : 0;

    // Validar email
    if (!validarEmail($email)) {
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'El formato del email no es válido', 'error');
        exit;
    }

    // Validar rol
    $roles_validos = ['admin', 'artista', 'recepcionista', 'cliente'];
    if (!in_array($rol, $roles_validos)) {
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'El rol seleccionado no es válido', 'error');
        exit;
    }

    // Verificar si el email ya existe
    if (emailExiste($email)) {
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'El email ya está registrado', 'error');
        exit;
    }

    // Crear el usuario
    $resultado = crearUsuario($nombre, $email, $password, $rol, $activo);

    if ($resultado) {
        logInfo('Usuario creado correctamente: ' . $email . ' con rol: ' . $rol);
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'Usuario creado correctamente', 'success');
    } else {
        logError('Error al crear usuario: ' . $email);
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'Error al crear el usuario', 'error');
    }
}

/**
 * Función para actualizar un usuario existente
 */
function actualizarUsuario() {
    // Validar campos obligatorios
    $campos_requeridos = ['id', 'nombre', 'email', 'rol'];
    foreach ($campos_requeridos as $campo) {
        if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
            redireccionarConMensaje('../../pages/admin/usuarios.php', 'Todos los campos son obligatorios', 'error');
            exit;
        }
    }

    // Sanitizar y validar datos
    $id = (int)$_POST['id'];
    $nombre = sanitizeInput($_POST['nombre']);
    $email = sanitizeInput($_POST['email']);
    $rol = sanitizeInput($_POST['rol']);
    $activo = isset($_POST['activo']) ? 1 : 0;

    // Validar email
    if (!validarEmail($email)) {
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'El formato del email no es válido', 'error');
        exit;
    }

    // Validar rol
    $roles_validos = ['admin', 'artista', 'recepcionista', 'cliente'];
    if (!in_array($rol, $roles_validos)) {
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'El rol seleccionado no es válido', 'error');
        exit;
    }

    // Verificar si el usuario existe
    $usuario = obtenerUsuarioPorId($id);
    if (!$usuario) {
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'El usuario no existe', 'error');
        exit;
    }

    // Verificar si el email ya existe para otro usuario
    if ($email !== $usuario['email'] && emailExiste($email)) {
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'El email ya está registrado por otro usuario', 'error');
        exit;
    }

    // Actualizar el usuario
    $resultado = actualizarUsuario($id, $nombre, $email, $rol, $activo);

    if ($resultado) {
        logInfo('Usuario actualizado correctamente: ' . $email);
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'Usuario actualizado correctamente', 'success');
    } else {
        logError('Error al actualizar usuario: ' . $email);
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'Error al actualizar el usuario', 'error');
    }
}

/**
 * Función para eliminar un usuario
 */
function eliminarUsuario() {
    // Validar ID
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'ID de usuario no especificado', 'error');
        exit;
    }

    $id = (int)$_POST['id'];

    // Verificar si el usuario existe
    $usuario = obtenerUsuarioPorId($id);
    if (!$usuario) {
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'El usuario no existe', 'error');
        exit;
    }

    // Evitar que un administrador se elimine a sí mismo
    if ($id === (int)$_SESSION['usuario_id']) {
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'No puedes eliminar tu propio usuario', 'error');
        exit;
    }

    // Eliminar el usuario
    $resultado = eliminarUsuario($id);

    if ($resultado) {
        logInfo('Usuario eliminado correctamente: ' . $usuario['email']);
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'Usuario eliminado correctamente', 'success');
    } else {
        logError('Error al eliminar usuario: ' . $usuario['email']);
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'Error al eliminar el usuario', 'error');
    }
}

/**
 * Función para resetear la contraseña de un usuario
 */
function resetearPassword() {
    // Validar campos obligatorios
    $campos_requeridos = ['id', 'new_password', 'confirm_password'];
    foreach ($campos_requeridos as $campo) {
        if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
            redireccionarConMensaje('../../pages/admin/usuarios.php', 'Todos los campos son obligatorios', 'error');
            exit;
        }
    }

    $id = (int)$_POST['id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificar que las contraseñas coincidan
    if ($new_password !== $confirm_password) {
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'Las contraseñas no coinciden', 'error');
        exit;
    }

    // Verificar si el usuario existe
    $usuario = obtenerUsuarioPorId($id);
    if (!$usuario) {
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'El usuario no existe', 'error');
        exit;
    }

    // Actualizar la contraseña
    $resultado = actualizarPasswordUsuario($id, $new_password);

    if ($resultado) {
        logInfo('Contraseña actualizada correctamente para el usuario: ' . $usuario['email']);
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'Contraseña actualizada correctamente', 'success');
    } else {
        logError('Error al actualizar contraseña para el usuario: ' . $usuario['email']);
        redireccionarConMensaje('../../pages/admin/usuarios.php', 'Error al actualizar la contraseña', 'error');
    }
}