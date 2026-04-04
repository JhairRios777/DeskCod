<?php
namespace Controllers;

use Models\ClientesModel;
use Controllers\Autorizable;

class ClientesController {
use Autorizable;
    private $model;
    private const UPLOAD_DIR_LOGOS = 'Content/Uploads/logos/';
    private const MAX_SIZE         = 3 * 1024 * 1024;
    private const TIPOS            = ['image/jpeg', 'image/jpg', 'image/png'];

    public function __construct() {
        $this->model = new ClientesModel();
        $this->requireLogin();
        $this->requirePermiso('Clientes', 'ver');
    }

    public function index(): array {
        $clientes      = $this->model->obtenerTodos();
        $flash_success = $_SESSION['flash_success'] ?? null;
        $flash_error   = $_SESSION['flash_error']   ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
        return compact('clientes', 'flash_success', 'flash_error');
    }

    public function Registry(int $id = 0): array {

        $cliente = null;
        $error   = null;
        $success = null;
        $planes  = $id === 0 ? $this->model->obtenerPlanes() : [];

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
                header("Location: " . ($id > 0 ? "/Clientes/Registry/{$id}" : '/Clientes/Registry'));
                exit();
            }

            try {
                if ($id > 0) {
                    $this->model->actualizar($id, $datos);

                    if (!empty($_FILES['logo']['name'])) {
                        $this->procesarYGuardarLogo($id, $cliente['logo'] ?? null, $datos['nombre']);
                    }

                    $this->auditar('CLIENTE_ACTUALIZADO', $id);

                } else {
                    $nuevoId = $this->model->crear($datos);

                    if (!empty($_POST['plan_id']) && !empty($_POST['fecha_inicio'])) {
                        $this->crearSuscripcionInicial($nuevoId);
                    }

                    if (!empty($_FILES['logo']['name'])) {
                        $this->procesarYGuardarLogo($nuevoId, null, $datos['nombre']);
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
                header("Location: " . ($id > 0 ? "/Clientes/Registry/{$id}" : '/Clientes/Registry'));
                exit();
            }
        }

        $error   = $_SESSION['flash_error']   ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        return compact('cliente', 'planes', 'error', 'success');
    }

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

    // ============================================
    // procesarYGuardarLogo()
    // Nombre del archivo: slug del nombre del cliente
    // Ej: "Zona Marcol" → zona-marcol_42.jpg
    // ============================================
    private function procesarYGuardarLogo(int $clienteId, ?string $logoActual, string $nombreCliente): void {
        $file = $_FILES['logo'];

        if ($file['error'] !== UPLOAD_ERR_OK) return;

        if ($file['size'] > self::MAX_SIZE) {
            $_SESSION['flash_error'] = 'El logo no puede superar 3MB.';
            return;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, self::TIPOS)) {
            $_SESSION['flash_error'] = 'El logo debe ser JPG o PNG.';
            return;
        }

        // Genera nombre legible: "Zona Marcol" → "zona-marcol_42.jpg"
        $ext    = $mime === 'image/png' ? 'png' : 'jpg';
        $slug   = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $nombreCliente), '-'));
        $slug   = substr($slug, 0, 40); // máximo 40 chars
        $nombre = $slug . '_' . $clienteId . '.' . $ext;
        $dirAbs = ROOT . self::UPLOAD_DIR_LOGOS;

        if (!is_dir($dirAbs)) mkdir($dirAbs, 0755, true);

        if (!move_uploaded_file($file['tmp_name'], $dirAbs . $nombre)) {
            $_SESSION['flash_error'] = 'No se pudo guardar el logo.';
            return;
        }

        // Elimina logo anterior si existe y es diferente al nuevo
        if ($logoActual && file_exists(ROOT . $logoActual) && $logoActual !== self::UPLOAD_DIR_LOGOS . $nombre) {
            unlink(ROOT . $logoActual);
        }

        $ruta = self::UPLOAD_DIR_LOGOS . $nombre;
        $db   = (new \Config\Conexion())->getConexion();
        $db->prepare("UPDATE clientes SET logo = ? WHERE id = ?")
           ->execute([$ruta, $clienteId]);
    }

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
                   $accion, 'clientes', $registroId,
                   $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
               ]);
        } catch (\PDOException $e) {
            error_log("[DeskCod] Auditoría: " . $e->getMessage());
        }
    }
}
?>