<?php
// ============================================
// Define.php — Constantes y configuración base
// Es el primer archivo que carga el sistema
// ============================================

define('DS',   DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__)) . DS);

// ============================================
// Carga el archivo .env
// Lee cada línea y registra las variables en
// $_ENV y en el entorno del proceso con putenv()
// No requiere librerías externas como dotenv
// ============================================
$envFile = ROOT . '.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignora comentarios
        if (strpos(trim($line), '#') === 0) continue;

        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);

            // Registra en $_ENV y en el entorno del sistema
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
}

// ============================================
// Configuración de errores según el entorno
// development: muestra errores en pantalla
// production:  oculta errores, los loguea
// ============================================
$appEnv = $_ENV['APP_ENV'] ?? 'production';

if ($appEnv === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
    ini_set('log_errors', 1);

    // Crea la carpeta logs si no existe
    $logsDir = ROOT . 'logs';
    if (!is_dir($logsDir)) {
        mkdir($logsDir, 0755, true);
    }
    ini_set('error_log', $logsDir . DS . 'error.log');
}
?>