<?php
namespace Controllers;

use Models\AuthModel;

class AuthController {

    private $model;

    public function __construct() {
        $this->model = new AuthModel();
    }

    // ============================================
    // GET  /Auth/login  → vista
    // POST /Auth/login  → procesar
    // ============================================
    public function login(): array {
        // Limpia cualquier output previo antes de responder JSON
        while (ob_get_level() > 0) ob_end_clean();

        if (isset($_SESSION['system']['UserName'])) {
            header('Location: /Home');
            exit();
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            $username = trim(strtolower($_POST['username'] ?? ''));
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Ingresa tu usuario y contraseña.']);
                exit();
            }

            try {
                $empleado = $this->model->buscarPorUsername($username);

                if (!$empleado) {
                    echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos.']);
                    exit();
                }

                if (!$empleado['activo']) {
                    echo json_encode(['success' => false, 'message' => 'Tu cuenta está desactivada. Contacta al administrador.']);
                    exit();
                }

                if (!empty($empleado['bloqueado_hasta']) && strtotime($empleado['bloqueado_hasta']) > time()) {
                    $restantes = ceil((strtotime($empleado['bloqueado_hasta']) - time()) / 60);
                    echo json_encode(['success' => false, 'message' => "Cuenta bloqueada. Intenta en {$restantes} minuto(s)."]);
                    exit();
                }

                if (!password_verify($password, $empleado['hash'])) {
                    $this->model->incrementarIntento($username);
                    $empleadoAct = $this->model->buscarPorUsername($username);
                    $intentos    = (int)($empleadoAct['intentos_fallidos'] ?? 0);
                    $restantes   = 5 - $intentos;

                    if ($intentos >= 5) {
                        echo json_encode(['success' => false, 'message' => 'Cuenta bloqueada por 15 minutos.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => "Contraseña incorrecta. Te quedan {$restantes} intento(s)."]);
                    }
                    exit();
                }

                // ── Login exitoso ──
                $this->model->resetIntentos($username);
                $this->model->actualizarLogin($empleado['id']);

                session_regenerate_id(true);

                $permisos    = $this->model->obtenerPermisos($empleado['rol_id']);
                $permisosMap = [];
                foreach ($permisos as $p) {
                    $permisosMap[$p['modulo']][$p['accion']] = 1;
                }

                $_SESSION['system'] = [
                    'UserID'   => $empleado['id'],
                    'UserName' => $empleado['nombre'],
                    'Email'    => $empleado['email'],
                    'RolID'    => $empleado['rol_id'],
                    'EsAdmin'  => $empleado['es_admin'],
                    'Foto'     => $empleado['foto'] ?? null,
                    'Permisos' => $permisosMap,
                ];

                $this->auditar('LOGIN_OK', $empleado['id']);

                echo json_encode(['success' => true, 'redirect' => '/Home']);
                exit();

            } catch (\Throwable $e) {
                error_log("[DeskCod] Login error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error interno. Intenta de nuevo.']);
                exit();
            }
        }

        return compact('error');
    }

    // ============================================
    // POST /Auth/logout
    // ============================================
    public function logout(): void {
        $userId = $_SESSION['system']['UserID'] ?? null;
        if ($userId) $this->auditar('LOGOUT', $userId);
        session_destroy();
        header('Location: /Auth/login');
        exit();
    }

    private function auditar(string $accion, int $userId): void {
        try {
            $db = (new \Config\Conexion())->getConexion();
            $db->prepare("INSERT INTO auditoria_acciones (empleado_id,accion,tabla,registro_id,ip) VALUES (?,?,?,?,?)")
               ->execute([$userId, $accion, 'empleados', $userId, $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0']);
        } catch (\Throwable $e) {
            error_log("[DeskCod] Auditoría: " . $e->getMessage());
        }
    }
}
?>