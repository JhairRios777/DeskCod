<?php
namespace Controllers;

use Models\SuscripcionesModel;
use Controllers\Autorizable;

class SuscripcionesController {
use Autorizable;
    private $model;

    public function __construct() {
        $this->model = new SuscripcionesModel();
        $this->requireLogin();                      
        $this->requirePermiso('suscripciones', 'ver');
    }

    // ============================================
    // GET /Suscripciones
    // Lista todas las suscripciones
    // Acepta ?cliente=ID para filtrar por cliente
    // ============================================
    public function index(): array {
        $clienteId     = (int)($_GET['cliente'] ?? 0);
        $flash_success = $_SESSION['flash_success'] ?? null;
        $flash_error   = $_SESSION['flash_error']   ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $suscripciones = $clienteId > 0
            ? $this->model->obtenerPorCliente($clienteId)
            : $this->model->obtenerTodas();

        $clienteFiltro = $clienteId;
        return compact('suscripciones', 'flash_success', 'flash_error', 'clienteFiltro');
    }

    // ============================================
    // GET  /Suscripciones/Registry     → crear
    // GET  /Suscripciones/Registry/1   → editar
    // POST /Suscripciones/Registry     → guardar
    // ============================================
    public function Registry(int $id = 0): array {
        $clientes     = $this->model->obtenerClientes();
        $planes       = $this->model->obtenerPlanes();
        $suscripcion  = null;
        $clientePreId = (int)($_GET['cliente'] ?? 0);

        if ($id > 0) {
            $suscripcion = $this->model->obtenerPorId($id);
            if (!$suscripcion) {
                header('Location: /Suscripciones');
                exit();
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Registrar'])) {

            $datos   = $this->sanitizar();
            $errores = $this->validar($datos);

            if (!empty($errores)) {
                $_SESSION['flash_error'] = implode(' ', $errores);
                $url = $id > 0 ? "/Suscripciones/Registry/{$id}" : '/Suscripciones/Registry';
                header("Location: {$url}");
                exit();
            }

            try {
                $this->model->crear($datos);
                $this->auditar('SUSCRIPCION_CREADA', $datos['cliente_id']);
                $_SESSION['flash_success'] = 'Suscripción creada correctamente.';
                header("Location: /Suscripciones?cliente={$datos['cliente_id']}");
                exit();

            } catch (\PDOException $e) {
                error_log("[DeskCod] Registry suscripcion: " . $e->getMessage());
                $_SESSION['flash_error'] = 'Error al guardar. Intenta de nuevo.';
                header('Location: /Suscripciones/Registry');
                exit();
            }
        }

        $error   = $_SESSION['flash_error']   ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        return compact('suscripcion', 'clientes', 'planes', 'error', 'success', 'clientePreId');
    }

    // ============================================
    // POST /Suscripciones/suspender
    // Suspende la suscripción activa del cliente
    // ============================================
    public function suspender(): void {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        $clienteId = (int)($_POST['cliente_id'] ?? 0);
        if (!$clienteId) {
            echo json_encode(['success' => false, 'message' => 'ID inválido.']);
            exit();
        }

        try {
            $this->model->suspender($clienteId);
            $this->auditar('SUSCRIPCION_SUSPENDIDA', $clienteId);
            echo json_encode(['success' => true, 'message' => 'Suscripción suspendida. Los días restantes quedan congelados.']);
        } catch (\PDOException $e) {
            error_log("[DeskCod] Suspender: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al suspender.']);
        }
        exit();
    }

    // ============================================
    // POST /Suscripciones/reactivar
    // Reactiva y extiende los días suspendidos
    // ============================================
    public function reactivar(): void {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        $clienteId = (int)($_POST['cliente_id'] ?? 0);
        if (!$clienteId) {
            echo json_encode(['success' => false, 'message' => 'ID inválido.']);
            exit();
        }

        try {
            $this->model->reactivar($clienteId);
            $this->auditar('SUSCRIPCION_REACTIVADA', $clienteId);
            echo json_encode(['success' => true, 'message' => 'Suscripción reactivada. La fecha de vencimiento fue extendida.']);
        } catch (\PDOException $e) {
            error_log("[DeskCod] Reactivar: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al reactivar.']);
        }
        exit();
    }

    // ============================================
    // POST /Suscripciones/cambiarPlan
    // Programa el cambio de plan al vencer
    // ============================================
    public function cambiarPlan(): void {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        $clienteId = (int)($_POST['cliente_id'] ?? 0);
        $planId    = (int)($_POST['plan_id']    ?? 0);

        if (!$clienteId || !$planId) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
            exit();
        }

        try {
            $this->model->cambiarPlan($clienteId, $planId);
            $this->auditar('SUSCRIPCION_CAMBIO_PLAN', $clienteId);
            echo json_encode(['success' => true, 'message' => 'Cambio de plan programado. Se aplicará al vencer la suscripción actual.']);
        } catch (\PDOException $e) {
            error_log("[DeskCod] CambiarPlan: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al programar el cambio.']);
        }
        exit();
    }

    // ── Helpers ───────────────────────────────

    private function sanitizar(): array {
        return [
            'cliente_id'       => (int)($_POST['cliente_id']       ?? 0),
            'plan_id'          => (int)($_POST['plan_id']          ?? 0),
            'fecha_inicio'     => trim($_POST['fecha_inicio']      ?? ''),
            'fecha_vencimiento'=> trim($_POST['fecha_vencimiento'] ?? ''),
            'notas'            => trim($_POST['notas']             ?? ''),
        ];
    }

    private function validar(array $d): array {
        $e = [];
        if (empty($d['cliente_id'])) $e[] = 'Selecciona un cliente.';
        if (empty($d['plan_id']))    $e[] = 'Selecciona un plan.';
        if (empty($d['fecha_inicio']))      $e[] = 'La fecha de inicio es obligatoria.';
        if (empty($d['fecha_vencimiento'])) $e[] = 'La fecha de vencimiento es obligatoria.';
        if (!empty($d['fecha_inicio']) && !empty($d['fecha_vencimiento'])) {
            if ($d['fecha_vencimiento'] <= $d['fecha_inicio']) {
                $e[] = 'La fecha de vencimiento debe ser posterior a la de inicio.';
            }
        }
        return $e;
    }

    private function auditar(string $accion, int $registroId): void {
        try {
            $db = (new \Config\Conexion())->getConexion();
            $db->prepare("INSERT INTO auditoria_acciones (empleado_id,accion,tabla,registro_id,ip) VALUES (?,?,?,?,?)")
               ->execute([
                   $_SESSION['system']['UserID'] ?? 1,
                   $accion,
                   'suscripciones',
                   $registroId,
                   $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
               ]);
        } catch (\PDOException $e) {
            error_log("[DeskCod] Auditoría: " . $e->getMessage());
        }
    }
}
?>