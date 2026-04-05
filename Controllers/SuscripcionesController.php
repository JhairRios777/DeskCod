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
    // ============================================
    public function index(): array {
        $clienteId     = (int)($_GET['cliente'] ?? 0);
        $flash_success = $_SESSION['flash_success'] ?? null;
        $flash_error   = $_SESSION['flash_error']   ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $suscripciones = $clienteId > 0
            ? $this->model->obtenerPorCliente($clienteId)
            : $this->model->obtenerTodas();

        // Planes reales para el modal de cambiar plan
        $planes = $this->model->obtenerPlanes();

        $clienteFiltro = $clienteId;
        return compact('suscripciones', 'planes', 'flash_success', 'flash_error', 'clienteFiltro');
        error_log("[DEBUG index planes] " . json_encode($planes));
return compact('suscripciones', 'planes', 'flash_success', 'flash_error', 'clienteFiltro');
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

                $tipo = $datos['tipo_periodo'] === 'anual' ? 'anual' : 'mensual';
                $_SESSION['flash_success'] = "Suscripción {$tipo} creada correctamente.";
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
        $planId      = (int)($_POST['plan_id']      ?? 0);
        $tipoPeriodo = trim($_POST['tipo_periodo']   ?? 'mensual');
        $fechaInicio = trim($_POST['fecha_inicio']   ?? '');

        // Calcula la fecha de vencimiento según el tipo de período
        // El JS ya la calcula en la vista, pero la recalculamos en backend
        // por seguridad — no confiamos en el input del cliente
        $fechaVencimiento = trim($_POST['fecha_vencimiento'] ?? '');

        if (!empty($fechaInicio) && $planId > 0) {
            try {
                $db   = (new \Config\Conexion())->getConexion();
                $stmt = $db->prepare("SELECT duracion_dias, descuento_anual FROM planes WHERE id = ? LIMIT 1");
                $stmt->execute([$planId]);
                $plan = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($plan) {
                    $inicio = new \DateTime($fechaInicio);
                    if ($tipoPeriodo === 'anual') {
                        // Anual = 365 días
                        $inicio->modify('+365 days');
                    } else {
                        // Mensual = duracion_dias del plan
                        $inicio->modify('+' . (int)$plan['duracion_dias'] . ' days');
                    }
                    $fechaVencimiento = $inicio->format('Y-m-d');
                }
            } catch (\PDOException $e) {
                error_log("[DeskCod] Sanitizar suscripcion: " . $e->getMessage());
            }
        }

        return [
            'cliente_id'        => (int)($_POST['cliente_id'] ?? 0),
            'plan_id'           => $planId,
            'tipo_periodo'      => $tipoPeriodo,
            'fecha_inicio'      => $fechaInicio,
            'fecha_vencimiento' => $fechaVencimiento,
            'notas'             => trim($_POST['notas'] ?? ''),
        ];
    }

    private function validar(array $d): array {
        $e = [];
        if (empty($d['cliente_id']))        $e[] = 'Selecciona un cliente.';
        if (empty($d['plan_id']))           $e[] = 'Selecciona un plan.';
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
                   $accion, 'suscripciones', $registroId,
                   $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
               ]);
        } catch (\PDOException $e) {
            error_log("[DeskCod] Auditoría: " . $e->getMessage());
        }
    }
}
?>