<?php
// ============================================
// Cookie de sesión muere al cerrar navegador
// Timeout de inactividad: 2 horas
// ============================================
$timeout = 7200; // segundos — ajusta a tu preferencia

session_set_cookie_params([
    'lifetime' => 0,        // 0 = muere al cerrar el navegador
    'path'     => '/',
    'secure'   => false,    // cambiar a true si usas HTTPS en producción
    'httponly' => true,     // JS no puede leer la cookie de sesión
    'samesite' => 'Strict',
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Timeout por inactividad ───────────────────
// Si el usuario lleva más de $timeout segundos sin actividad
// se destruye la sesión y se redirige al login
if (isset($_SESSION['_last_activity'])) {
    if (time() - $_SESSION['_last_activity'] > $timeout) {
        session_unset();
        session_destroy();
        header('Location: /Login?timeout=1');
        exit();
    }
}
// Actualiza el timestamp en cada request
$_SESSION['_last_activity'] = time();

// ── Bootstrap ────────────────────────────────
require_once "Define.php";
require_once "Config" . DIRECTORY_SEPARATOR . "JRequest.php";
require_once "Config" . DIRECTORY_SEPARATOR . "JRouter.php";
require_once "Config" . DIRECTORY_SEPARATOR . "AutoLoad.php";

Config\AutoLoad::run();

$request          = new Config\JRequest();
$rutasPublicas    = ['Auth'];
$controllerActual = $request->getController();
$methodActual     = $request->getMethod();

// ── Protección global de rutas ────────────────
if (!in_array($controllerActual, $rutasPublicas)) {
    if (!isset($_SESSION['system']['UserName'])) {
        header('Location: /Login');
        exit();
    }
}

$metodosJson = [
    'suscripciones', 'cuentas',
    'suspender', 'reactivar', 'cambiarPlan',
    'desactivar', 'cambiarEstado', 'asignar', 'comentar',
    'regenerarToken', // ← confirma que está aquí
];

$esRutaPublica = in_array($controllerActual, $rutasPublicas);
$esPost        = $_SERVER['REQUEST_METHOD'] === 'POST';
$esMetodoJson  = in_array($methodActual, $metodosJson);

// Todo POST y métodos JSON van sin template
if ($esPost || $esMetodoJson) {
    Config\JRouter::run($request);
    exit();
}

if (!$esRutaPublica) {
    include_once "Template" . DIRECTORY_SEPARATOR . "index.php";
}

Config\JRouter::run($request);
?>