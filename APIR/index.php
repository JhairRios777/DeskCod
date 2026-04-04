<?php
// ============================================
// APIR/index.php — Router de la API DeskCod
// ============================================

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ── Carga dependencias ────────────────────────
define('API_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

require_once API_ROOT . 'Define.php';
require_once API_ROOT . 'Config' . DS . 'Conexion.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/Tickets.php';

// ── Detecta la ruta ───────────────────────────
// Apache reescribe /API/tickets → APIR/index.php
// La ruta original se puede recuperar desde:
//   1. $_SERVER['REDIRECT_URL']  — cuando mod_rewrite redirige
//   2. $_GET['url']              — si el .htaccess pasa ?url=
//   3. $_SERVER['REQUEST_URI']   — acceso directo

$uri = $_SERVER['REDIRECT_URL']
    ?? $_GET['url']
    ?? $_SERVER['REQUEST_URI']
    ?? '';

$uri = parse_url($uri, PHP_URL_PATH) ?? $uri;
$uri = trim($uri, '/');

// Elimina prefijos: API/, APIR/, APIR/index.php
$uri = preg_replace('#^(APIR/index\.php/?|APIR/?|API/?)#i', '', $uri);
$uri = trim($uri, '/');

// Si viene vacío y hay query string con 'url', úsalo
if (empty($uri) && !empty($_SERVER['QUERY_STRING'])) {
    parse_str($_SERVER['QUERY_STRING'], $qs);
    if (!empty($qs['url'])) {
        $uri = trim(preg_replace('#^API/?#i', '', $qs['url']), '/');
    }
}

$partes  = !empty($uri) ? array_values(array_filter(explode('/', $uri))) : [];
$recurso = strtolower($partes[0] ?? '');
$id      = isset($partes[1]) && is_numeric($partes[1]) ? (int)$partes[1] : null;
$accion  = strtolower($partes[2] ?? '');
$metodo  = $_SERVER['REQUEST_METHOD'];

// ── Router ────────────────────────────────────
try {
    switch ($recurso) {

        case '':
        case 'status':
            echo json_encode([
                'success' => true,
                'status'  => 'online',
                'version' => '1.0',
                'message' => 'DeskCod API operativa',
                'time'    => date('Y-m-d H:i:s'),
            ]);
            break;

        case 'tickets':
            $auth    = new ApiAuth();
            $cliente = $auth->validar();
            $ctrl    = new ApiTickets($cliente);

            if ($id === null) {
                if ($metodo === 'GET')      $ctrl->listar();
                elseif ($metodo === 'POST') $ctrl->crear();
                else responderError(405, 'Método no permitido.');

            } else {
                if ($accion === '') {
                    if ($metodo === 'GET') $ctrl->detalle($id);
                    else responderError(405, 'Método no permitido.');

                } elseif ($accion === 'comentarios') {
                    if ($metodo === 'GET') $ctrl->comentarios($id);
                    else responderError(405, 'Método no permitido.');

                } elseif ($accion === 'comentar') {
                    if ($metodo === 'POST') $ctrl->comentar($id);
                    else responderError(405, 'Método no permitido.');

                } else {
                    responderError(404, 'Acción no encontrada.');
                }
            }
            break;

        default:
            responderError(404, "Endpoint '{$recurso}' no encontrado.");
            break;
    }

} catch (\Throwable $e) {
    error_log("[DeskCod API] " . $e->getMessage());
    responderError(500, 'Error interno del servidor.');
}

function responderError(int $codigo, string $mensaje): void {
    http_response_code($codigo);
    echo json_encode(['success' => false, 'error' => $mensaje, 'code' => $codigo]);
    exit();
}
?>
