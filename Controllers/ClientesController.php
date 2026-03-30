<?php
namespace Controllers;

use Models\ClientesModel;

// ============================================
// ClientesController.php
// Responsabilidad única: gestión de clientes
// Las suscripciones se gestionan en
// SuscripcionesController
// ============================================

class ClientesController {

    private $model;

    public function __construct() {
        $this->model = new ClientesModel();
    }

    // ============================================
    // GET /Clientes — lista de clientes
    // ============================================
    public function index(): array {
        $clientes      = $this->model->obtenerTodos();
        $flash_success = $_SESSION['flash_success'] ?? null;
        $flash_error   = $_SESSION['flash_error']   ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
        return compact('clientes', 'flash_success', 'flash_error');
    }

    // ============================================
    // GET  /Clientes/Registry     → formulario vacío
    // GET  /Clientes/Registry/1   → formulario editar
    // POST /Clientes/Registry     → guardar nuevo
    // POST /Clientes/Registry/1   → guardar cambios
    // ============================================
    public function Registry(int $id = 0): array {

        $cliente = null;
        $error   = null;
        $success = null;

        // Al crear se puede asignar plan inicial
        // Al editar solo se modifican datos del cliente
        $planes = $id === 0 ? $this->model->obtenerPlanes() : [];

        if ($id > 0) {
            $cliente = $this->model->obtenerPorId($id);
            if (!$cliente) {
                header('Location: /Clientes');
                exit();
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Registrar'])) {

            $datos   = $this->sanitizar();
            $errores = $this->validar($datos);

            if (!empty($errores)) {
                $_SESSION['flash_error'] = implode(' ', $errores);
                $url = $id > 0 ? "/Clientes/Registry/{$id}" : '/Clientes/Registry';
                header("Location: {$url}");
                exit();
            }

            try {
                if ($id > 0) {
                    // Editar — solo datos del cliente
                    // Las suscripciones se gestionan en /Suscripciones
                    $this->model->actualizar($id, $datos);
                    $this->auditar('CLIENTE_ACTUALIZADO', $id);

                } else {
                    // Crear — con plan inicial opcional
                    $nuevoId = $this->model->crear($datos);

                    if (!empty($_POST['plan_id']) && !empty($_POST['fecha_inicio'])) {
                        $this->crearSuscripcionInicial($nuevoId);
                    }

                    $this->auditar('CLIENTE_CREADO', $nuevoId);
                }

                $_SESSION['flash_success'] = $id > 0
                    ? 'Cliente actualizado correctamente.'
                    : 'Cliente registrado correctamente.';
                header('Location: /Clientes');
                exit();

            } catch (\PDOException $e) {
                $msg = str_contains($e->getMessage(), 'email ya está registrado')
                    ? 'El correo electrónico ya está registrado.'
                    : 'Error al guardar. Intenta de nuevo.';
                error_log("[DeskCod] Registry cliente: " . $e->getMessage());
                $_SESSION['flash_error'] = $msg;
                $url = $id > 0 ? "/Clientes/Registry/{$id}" : '/Clientes/Registry';
                header("Location: {$url}");
                exit();
            }
        }

        // GET — recupera mensajes flash
        $error   = $_SESSION['flash_error']   ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        return compact('cliente', 'planes', 'error', 'success');
    }

    // ============================================
    // POST /Clientes/desactivar — soft delete JSON
    // ============================================
    public function desactivar(): void {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            exit();
        }

        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido.']);
            exit();
        }

        try {
            $this->model->desactivar($id);
            $this->auditar('CLIENTE_DESACTIVADO', $id);
            echo json_encode(['success' => true, 'message' => 'Cliente desactivado correctamente.']);
        } catch (\PDOException $e) {
            error_log("[DeskCod] Desactivar cliente: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al desactivar.']);
        }
        exit();
    }

    // ── Helpers privados ──────────────────────

    // Crea la suscripción inicial al registrar un cliente nuevo
    private function crearSuscripcionInicial(int $clienteId): void {
        $db   = (new \Config\Conexion())->getConexion();
        $stmt = $db->prepare("CALL sp_suscripciones_crear(?,?,?,?,?)");
        $stmt->execute([
            $clienteId,
            (int)$_POST['plan_id'],
            $_POST['fecha_inicio'],
            $_POST['fecha_vencimiento'],
            trim($_POST['notas_suscripcion'] ?? ''),
        ]);
        $stmt->closeCursor();
    }

    private function sanitizar(): array {
        return [
            'nombre'         => trim(htmlspecialchars($_POST['nombre']         ?? '')),
            'email'          => trim(strtolower($_POST['email']                ?? '')),
            'telefono'       => trim($_POST['telefono']                        ?? ''),
            'empresa_nombre' => trim(htmlspecialchars($_POST['empresa_nombre'] ?? '')),
            'nit_ruc'        => trim($_POST['nit_ruc']                         ?? ''),
            'direccion'      => trim(htmlspecialchars($_POST['direccion']      ?? '')),
        ];
    }

    private function validar(array $d): array {
        $e = [];
        if (empty($d['nombre'])) $e[] = 'El nombre es obligatorio.';
        if (empty($d['email']))  $e[] = 'El correo es obligatorio.';
        elseif (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) $e[] = 'El correo no es válido.';
        return $e;
    }

    private function auditar(string $accion, int $registroId): void {
        try {
            $db = (new \Config\Conexion())->getConexion();
            $db->prepare("INSERT INTO auditoria_acciones (empleado_id,accion,tabla,registro_id,ip) VALUES (?,?,?,?,?)")
               ->execute([
                   $_SESSION['system']['UserID'] ?? 1,
                   $accion,
                   'clientes',
                   $registroId,
                   $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
               ]);
        } catch (\PDOException $e) {
            error_log("[DeskCod] Auditoría: " . $e->getMessage());
        }
    }
}
?>