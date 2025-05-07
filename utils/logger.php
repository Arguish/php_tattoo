<?php
/**
 * Sistema de logging para la aplicación
 * Proporciona funciones para registrar errores y depuración
 */


if (!function_exists('getenv') || !getenv('DB_HOST')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__. '/../');
    $dotenv->load();
}


if (!defined('LOG_ERROR')) define('LOG_ERROR', 'ERROR');
if (!defined('LOG_WARNING')) define('LOG_WARNING', 'WARNING');
if (!defined('LOG_INFO')) define('LOG_INFO', 'INFO');
if (!defined('LOG_DEBUG')) define('LOG_DEBUG', 'DEBUG');

/**
 * Registra un mensaje en el log del sistema
 * @param string $mensaje Mensaje a registrar
 * @param string $nivel Nivel del mensaje (ERROR, WARNING, INFO, DEBUG)
 * @param array $contexto Datos adicionales para el mensaje
 */
function logMessage($mensaje, $nivel = LOG_ERROR, $contexto = []) {
    
    $dev_mode = isset($_ENV['DEV_MODE']) && $_ENV['DEV_MODE'] === 'true';
    
    
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
    $caller = $backtrace[1] ?? ['file' => 'unknown'];
    $archivoOrigen = str_replace(__DIR__.'/../', '', $caller['file']);

    
    if (!$dev_mode && $nivel === LOG_DEBUG) {
        return;
    }
    
    
    $timestamp = date('Y-m-d H:i:s');
    $mensaje_formateado = "[$timestamp] [$nivel] [$archivoOrigen] $mensaje";
    
    
    if (!empty($contexto)) {
        $mensaje_formateado .= ' ' . json_encode($contexto, JSON_UNESCAPED_UNICODE);
    }
    
    
    $logPath = $_ENV['LOG_PATH'] ?? 'logs';
if (!is_dir($logPath)) {
    mkdir($logPath, 0755, true);
}
if (!is_writable($logPath)) {
    error_log("Directorio de logs no escribible: $logPath");
    return;
}
$logFile = $logPath . '/app-' . date('Y-m-d') . '.log';
$logHandle = fopen($logFile, 'a');
if ($logHandle) {
    fwrite($logHandle, $mensaje_formateado . PHP_EOL);
    fclose($logHandle);
} else {
    error_log("Error al abrir archivo de log: $logFile");
}
    
    
    if ($dev_mode) {
        // No imprimir mensajes en la web ni en CLI, solo registrar en archivo
    }
}

/**
 * Registra un error
 * @param string $mensaje Mensaje de error
 * @param array $contexto Datos adicionales para el mensaje
 */
function logError($mensaje, $contexto = []) {
    logMessage($mensaje, LOG_ERROR, $contexto);
}

/**
 * Registra una advertencia
 * @param string $mensaje Mensaje de advertencia
 * @param array $contexto Datos adicionales para el mensaje
 */
function logWarning($mensaje, $contexto = []) {
    logMessage($mensaje, LOG_WARNING, $contexto);
}

/**
 * Registra información
 * @param string $mensaje Mensaje informativo
 * @param array $contexto Datos adicionales para el mensaje
 */
function logInfo($mensaje, $contexto = []) {
    logMessage($mensaje, LOG_INFO, $contexto);
}
/**
 * Registra un mensaje de depuración (solo en modo desarrollo)
 * @param string $mensaje Mensaje de depuración
 * @param array $contexto Datos adicionales para el mensaje
 */
function logDebug($mensaje, $contexto = []) {
    logMessage($mensaje, LOG_DEBUG, $contexto);
}

// Configuración para evitar mostrar errores y logs en la salida HTML
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
