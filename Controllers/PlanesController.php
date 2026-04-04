<?php
namespace Controllers;

use Models\PlanesModel;
use Controllers\Autorizable;

class PlanesController {
    use Autorizable;

    private $model;

    public function __construct() {
        $this->model = new PlanesModel();
        $this->requireLogin();
        $this->requirePermiso('planes', 'ver');
    }

    public function index(): array {
        $planes        = $this->model->obtenerTodos();
        $flash_success = $_SESSION['flash_success'] ?? null;
        $flash_error   = $_SESSION['flash_error']   ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
        return compact('planes', 'flash_success', 'flash_error');
    }

    public function Registry(int $id = 0): array {
        $plan = null;

        if ($id > 0) {
            $plan = $this->model->obtenerPorId($id);
            if (!$plan) {
                header('Location: /Planes');
                exit();
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Registrar'])) {
            $datos   = $this->sanitizar();
            $errores = $this->validar($datos);

            if (!empty($errores)) {
                $_SESSION['flash_error'] = implode(' ', $errores);
                header("Location: " . ($id > 0 ? "/Planes/Registry/{$id}" : '/Planes/Registry'));
                exit();
            }

            try {
                if ($id > 0) {
                    $this->model->actualizar($id, $datos);
                    $this->auditar('PLAN_ACTUALIZADO', $id);
                    $_SESSION['flash_success'] = 'Plan actualizado correctamente.';
                } else {
                    $nuevoId = $this->model->crear($datos);
                    $this->auditar('PLAN_CREADO', $nuevoId);
                    $_SESSION['flash_success'] = 'Plan creado correctamente.';
                }
                header('Location: /Planes');
                exit();

            } catch (\PDOException $e) {
                $msg = str_contains($e->getMessage(), 'nombre del plan ya existe')
                    ? 'Ya existe un plan con ese nombre.'
                    : 'Error al guardar. Intenta de nuevo.';
                error_log("[DeskCod] Planes Registry: " . $e->getMessage());
                $_SESSION['flash_error'] = $msg;
                header("Location: " . ($id > 0 ? "/Planes/Registry/{$id}" : '/Planes/Registry'));
                exit();
            }
        }

        $error   = $_SESSION['flash_error']   ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        return compact('plan', 'error', 'success');
    }

    public function desactivar(): void {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido.']);
            exit();
        }

        try {
            $this->model->desactivar($id);
            $this->auditar('PLAN_DESACTIVADO', $id);
            echo json_encode(['success' => true, 'message' => 'Plan desactivado correctamente.']);
        } catch (\PDOException $e) {
            $msg = str_contains($e->getMessage(), 'suscripciones activas')
                ? 'No puedes desactivar un plan con suscripciones activas.'
                : 'Error al desactivar.';
            error_log("[DeskCod] Planes desactivar: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $msg]);
        }
        exit();
    }

    private function sanitizar(): array {
        return [
            'nombre'          => trim(htmlspecialchars($_POST['nombre']      ?? '')),
            'descripcion'     => trim($_POST['descripcion']                  ?? ''),
            'precio'          => (float)str_replace(',', '.', $_POST['precio'] ?? 0),
            'duracion_dias'   => (int)($_POST['duracion_dias']               ?? 30),
            'max_tickets'     => ($_POST['max_tickets'] ?? '') !== '' ? (int)$_POST['max_tickets'] : null,
            'descuento_anual' => (float)str_replace(',', '.', $_POST['descuento_anual'] ?? 0),
        ];
    }

    private function validar(array $d): array {
        $e = [];
        if (empty($d['nombre']))               $e[] = 'El nombre es obligatorio.';
        if ($d['precio'] <= 0)                 $e[] = 'El precio debe ser mayor a 0.';
        if ($d['duracion_dias'] <= 0)          $e[] = 'La duración debe ser mayor a 0.';
        if ($d['descuento_anual'] < 0 || $d['descuento_anual'] > 100)
                                               $e[] = 'El descuento debe estar entre 0 y 100.';
        return $e;
    }

    private function auditar(string $accion, int $registroId): void {
        try {
            $db = (new \Config\Conexion())->getConexion();
            $db->prepare("INSERT INTO auditoria_acciones (empleado_id,accion,tabla,registro_id,ip) VALUES (?,?,?,?,?)")
               ->execute([
                   $_SESSION['system']['UserID'] ?? 1,
                   $accion, 'planes', $registroId,
                   $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
               ]);
        } catch (\PDOException $e) {
            error_log("[DeskCod] Auditoría: " . $e->getMessage());
        }
    }
}
?>