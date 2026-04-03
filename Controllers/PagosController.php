<?php
namespace Controllers;

use Models\PagosModel;

class PagosController {

    private $model;
    private const UPLOAD_DIR = 'Content/Uploads/comprobantes/';
    private const MAX_SIZE   = 5 * 1024 * 1024;
    private const TIPOS      = ['image/jpeg', 'image/jpg', 'image/png'];

    public function __construct() {
        $this->model = new PagosModel();
    }

    // ============================================
    // GET /Pagos — lista de pagos + cuentas
    // ============================================
    public function index(): array {
        $pagos         = $this->model->obtenerTodos();
        $cuentas       = $this->model->obtenerCuentasTodas();
        $flash_success = $_SESSION['flash_success'] ?? null;
        $flash_error   = $_SESSION['flash_error']   ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $totalPagos   = count($pagos);
        $totalMes     = array_sum(array_map(fn($p) =>
            date('Y-m', strtotime($p['fecha_pago'] ?? $p['created_at'])) === date('Y-m')
                ? (float)$p['monto'] : 0, $pagos));
        $totalGeneral = array_sum(array_column($pagos, 'monto'));
        $totalPendiente = array_sum(array_map(
            fn($c) => (float)$c['saldo_pendiente'], $cuentas
        ));

        return compact('pagos', 'cuentas', 'flash_success', 'flash_error',
                       'totalPagos', 'totalMes', 'totalGeneral', 'totalPendiente');
    }

    // ============================================
    // GET  /Pagos/Registry   → registrar pago
    // POST /Pagos/Registry   → guardar
    // ============================================
    public function Registry(): array {
        $clientes    = $this->model->obtenerClientes();
        $metodosPago = $this->model->obtenerMetodosPago();
        $clientePreId = (int)($_GET['cliente'] ?? 0);
        $cuentaPreId  = (int)($_GET['cuenta']  ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Registrar'])) {
            $datos   = $this->sanitizar();
            $errores = $this->validar($datos);

            $rutaImagen = null;
            if (!empty($_FILES['comprobante']['name'])) {
                $resultImg = $this->procesarImagen($_FILES['comprobante']);
                if ($resultImg['error']) {
                    $errores[] = $resultImg['error'];
                } else {
                    $rutaImagen = $resultImg['ruta'];
                }
            }

            if (!empty($errores)) {
                if ($rutaImagen && file_exists(ROOT . $rutaImagen)) unlink(ROOT . $rutaImagen);
                $_SESSION['flash_error'] = implode(' ', $errores);
                header('Location: /Pagos/Registry');
                exit();
            }

            $datos['comprobante_imagen'] = $rutaImagen;

            try {
                $result = $this->model->registrar($datos);
                $this->auditar('PAGO_REGISTRADO', $result['pago_id'] ?? 0);
                $_SESSION['flash_success'] = "Pago registrado. Factura: {$result['numero_factura']}";
                header('Location: /Pagos');
                exit();
            } catch (\PDOException $e) {
                if ($rutaImagen && file_exists(ROOT . $rutaImagen)) unlink(ROOT . $rutaImagen);
                error_log("[DeskCod] Pagos Registry: " . $e->getMessage());
                $_SESSION['flash_error'] = 'Error al registrar el pago.';
                header('Location: /Pagos/Registry');
                exit();
            }
        }

        $error   = $_SESSION['flash_error']   ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        return compact('clientes', 'metodosPago', 'clientePreId', 'cuentaPreId', 'error', 'success');
    }

    // ============================================
    // GET  /Pagos/Cuenta      → crear cuenta
    // POST /Pagos/Cuenta      → guardar cuenta
    // ============================================
    public function Cuenta(): array {
        $clientes = $this->model->obtenerClientes();
        $clientePreId = (int)($_GET['cliente'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['CrearCuenta'])) {
            $datos = [
                'cliente_id'     => (int)($_POST['cliente_id']   ?? 0),
                'tipo'           => trim($_POST['tipo']           ?? 'sistema'),
                'descripcion'    => trim(htmlspecialchars($_POST['descripcion'] ?? '')),
                'monto_total'    => (float)str_replace(',', '.', $_POST['monto_total'] ?? 0),
                'suscripcion_id' => (int)($_POST['suscripcion_id'] ?? 0) ?: null,
                'notas'          => trim($_POST['notas'] ?? ''),
            ];

            $errores = [];
            if (empty($datos['cliente_id']))  $errores[] = 'Selecciona un cliente.';
            if (empty($datos['descripcion'])) $errores[] = 'La descripción es obligatoria.';
            if ($datos['monto_total'] <= 0)   $errores[] = 'El monto debe ser mayor a 0.';

            if (!empty($errores)) {
                $_SESSION['flash_error'] = implode(' ', $errores);
                header('Location: /Pagos/Cuenta');
                exit();
            }

            try {
                $nuevoId = $this->model->crearCuenta($datos);
                $this->auditar('CUENTA_CREADA', $nuevoId);
                $_SESSION['flash_success'] = 'Cuenta por cobrar creada correctamente.';
                header('Location: /Pagos');
                exit();
            } catch (\PDOException $e) {
                error_log("[DeskCod] Cuenta crear: " . $e->getMessage());
                $_SESSION['flash_error'] = 'Error al crear la cuenta.';
                header('Location: /Pagos/Cuenta');
                exit();
            }
        }

        $error   = $_SESSION['flash_error']   ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        return compact('clientes', 'clientePreId', 'error', 'success');
    }

    // ============================================
    // JSON endpoints
    // ============================================
    public function suscripciones(): void {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');
        $clienteId = (int)($_GET['cliente_id'] ?? 0);
        echo json_encode($clienteId ? $this->model->obtenerSuscripcionesCliente($clienteId) : []);
        exit();
    }

    public function cuentas(): void {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');
        $clienteId = (int)($_GET['cliente_id'] ?? 0);
        echo json_encode($clienteId ? $this->model->obtenerCuentasCliente($clienteId) : []);
        exit();
    }

    // ── Helpers ───────────────────────────────

    private function procesarImagen(array $file): array {
        if ($file['error'] !== UPLOAD_ERR_OK)
            return ['error' => 'Error al subir el comprobante.', 'ruta' => null];
        if ($file['size'] > self::MAX_SIZE)
            return ['error' => 'El comprobante no puede superar 5MB.', 'ruta' => null];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, self::TIPOS))
            return ['error' => 'Solo se aceptan imágenes JPG o PNG.', 'ruta' => null];

        $ext    = $mime === 'image/png' ? 'png' : 'jpg';
        $nombre = 'comp_' . date('Ymd') . '_' . uniqid() . '.' . $ext;
        $dirAbs = ROOT . self::UPLOAD_DIR;

        if (!is_dir($dirAbs)) mkdir($dirAbs, 0755, true);
        if (!move_uploaded_file($file['tmp_name'], $dirAbs . $nombre))
            return ['error' => 'No se pudo guardar el comprobante.', 'ruta' => null];

        return ['error' => null, 'ruta' => self::UPLOAD_DIR . $nombre];
    }

    private function sanitizar(): array {
        return [
            'cliente_id'     => (int)($_POST['cliente_id']     ?? 0),
            'cuenta_id'      => (int)($_POST['cuenta_id']      ?? 0) ?: null,
            'suscripcion_id' => (int)($_POST['suscripcion_id'] ?? 0) ?: null,
            'metodo_pago_id' => (int)($_POST['metodo_pago_id'] ?? 0),
            'concepto'       => trim(htmlspecialchars($_POST['concepto']   ?? '')),
            'monto'          => (float)str_replace(',', '.', $_POST['monto'] ?? 0),
            'referencia'     => trim($_POST['referencia']                  ?? ''),
            'notas'          => trim($_POST['notas']                       ?? ''),
            'fecha_pago'     => trim($_POST['fecha_pago']                  ?? ''),
        ];
    }

    private function validar(array $d): array {
        $e = [];
        if (empty($d['cliente_id']))     $e[] = 'Selecciona un cliente.';
        if (empty($d['metodo_pago_id'])) $e[] = 'Selecciona un método de pago.';
        if (empty($d['concepto']))       $e[] = 'El concepto es obligatorio.';
        if ($d['monto'] <= 0)            $e[] = 'El monto debe ser mayor a 0.';
        return $e;
    }

    private function auditar(string $accion, int $registroId): void {
        try {
            $db = (new \Config\Conexion())->getConexion();
            $db->prepare("INSERT INTO auditoria_acciones (empleado_id,accion,tabla,registro_id,ip) VALUES (?,?,?,?,?)")
               ->execute([
                   $_SESSION['system']['UserID'] ?? 1,
                   $accion, 'pagos', $registroId,
                   $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
               ]);
        } catch (\PDOException $e) {
            error_log("[DeskCod] Auditoría: " . $e->getMessage());
        }
    }
}
?>