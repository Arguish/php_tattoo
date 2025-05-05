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


define('LOG_ERROR', 'ERROR');
define('LOG_WARNING', 'WARNING');
define('LOG_INFO', 'INFO');
define('LOG_DEBUG', 'DEBUG');

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
        
        if (php_sapi_name() === 'cli' || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')) {
            
            echo $mensaje_formateado . "\n";
        } else {
            
            $color = 'black';
            switch ($nivel) {
                case LOG_ERROR:
                    $color = 'red';
                    break;
                case LOG_WARNING:
                    $color = 'orange';
                    break;
                case LOG_INFO:
                    $color = 'blue';
                    break;
                case LOG_DEBUG:
                    $color = 'gray';
                    break;
            }
            
            echo "<pre style='color: $color; margin: 0; padding: 5px; font-family: monospace;'>$mensaje_formateado</pre>";
        }
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
