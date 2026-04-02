<?php
namespace Controllers;

use Models\EmpleadosModel;

class EmpleadosController {

    private $model;

    public function __construct() {
        $this->model = new EmpleadosModel();
        if (!($_SESSION['system']['EsAdmin'] ?? false)) {
            header('Location: /Home');
            exit();
        }
    }

    // ============================================
    // GET /Empleados
    // ============================================
    public function index(): array {
        $empleados     = $this->model->obtenerTodos();
        $flash_success = $_SESSION['flash_success'] ?? null;
        $flash_error   = $_SESSION['flash_error']   ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
        return compact('empleados', 'flash_success', 'flash_error');
    }

    // ============================================
    // GET  /Empleados/Registry
    // GET  /Empleados/Registry/1
    // POST → guardar
    // ============================================
    public function Registry(int $id = 0): array {
        $roles    = $this->model->obtenerRoles();
        $empleado = null;

        if ($id > 0) {
            $empleado = $this->model->obtenerPorId($id);
            if (!$empleado) {
                header('Location: /Empleados');
                exit();
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Registrar'])) {
            $datos   = $this->sanitizarEmpleado();
            $errores = $this->validarEmpleado($datos, $id);

            if (!empty($errores)) {
                $_SESSION['flash_error'] = implode(' ', $errores);
                header("Location: " . ($id > 0 ? "/Empleados/Registry/{$id}" : '/Empleados/Registry'));
                exit();
            }

            try {
                if ($id > 0) {
                    $this->model->actualizar($id, $datos);
                    if (!empty($datos['password'])) {
                        $this->model->cambiarPassword($id, $datos['password']);
                    }
                    $this->auditar('EMPLEADO_ACTUALIZADO', $id);
                } else {
                    $nuevoId = $this->model->crear($datos);
                    $this->auditar('EMPLEADO_CREADO', $nuevoId);
                }

                $_SESSION['flash_success'] = $id > 0
                    ? 'Empleado actualizado correctamente.'
                    : 'Empleado creado correctamente.';
                header('Location: /Empleados');
                exit();

            } catch (\PDOException $e) {
                $msg = match(true) {
                    str_contains($e->getMessage(), 'email ya está registrado') => 'El correo ya está registrado.',
                    str_contains($e->getMessage(), 'username ya está en uso')  => 'El nombre de usuario ya está en uso.',
                    default => 'Error al guardar. Intenta de nuevo.'
                };
                error_log("[DeskCod] Registry empleado: " . $e->getMessage());
                $_SESSION['flash_error'] = $msg;
                header("Location: " . ($id > 0 ? "/Empleados/Registry/{$id}" : '/Empleados/Registry'));
                exit();
            }
        }

        $error   = $_SESSION['flash_error']   ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        return compact('empleado', 'roles', 'error', 'success');
    }

    // ============================================
    // GET  /Empleados/Roles      → lista de roles
    // ============================================
    public function Roles(): array {
        $roles         = $this->model->obtenerRolesTodos();
        $flash_success = $_SESSION['flash_success'] ?? null;
        $flash_error   = $_SESSION['flash_error']   ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
        return compact('roles', 'flash_success', 'flash_error');
    }

    // ============================================
    // GET  /Empleados/RolesRegistry     → crear rol
    // GET  /Empleados/RolesRegistry/1   → editar rol
    // POST → guardar rol + permisos
    // ============================================
    public function RolesRegistry(int $id = 0): array {
        $modulos  = $this->model->obtenerModulos();
        $acciones = $this->model->obtenerAcciones();
        $rol      = null;
        $permisos = [];

        if ($id > 0) {
            $rol = $this->model->obtenerRolPorId($id);
            if (!$rol) {
                header('Location: /Empleados/Roles');
                exit();
            }
            // Carga permisos actuales del rol
            $rawPermisos = $this->model->obtenerPermisosPorRol($id);
            foreach ($rawPermisos as $p) {
                $permisos[$p['modulo_id']][$p['accion_id']] = (bool)$p['permitido'];
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['GuardarRol'])) {
            $datosRol = [
                'nombre'      => trim(htmlspecialchars($_POST['nombre']      ?? '')),
                'descripcion' => trim(htmlspecialchars($_POST['descripcion'] ?? '')),
                'es_admin'    => isset($_POST['es_admin']) ? 1 : 0,
            ];

            if (empty($datosRol['nombre'])) {
                $_SESSION['flash_error'] = 'El nombre del rol es obligatorio.';
                header("Location: " . ($id > 0 ? "/Empleados/RolesRegistry/{$id}" : '/Empleados/RolesRegistry'));
                exit();
            }

            try {
                if ($id > 0) {
                    $this->model->actualizarRol($id, $datosRol);
                    $rolId = $id;
                } else {
                    $rolId = $this->model->crearRol($datosRol);
                }

                // Guarda los permisos de la matriz
                foreach ($modulos as $modulo) {
                    foreach ($acciones as $accion) {
                        $key       = "permiso_{$modulo['id']}_{$accion['id']}";
                        $permitido = isset($_POST[$key]) ? 1 : 0;
                        $this->model->guardarPermiso($rolId, $modulo['id'], $accion['id'], $permitido);
                    }
                }

                $this->auditar('ROL_GUARDADO', $rolId);
                $_SESSION['flash_success'] = $id > 0
                    ? 'Rol actualizado correctamente.'
                    : 'Rol creado correctamente.';
                header('Location: /Empleados/Roles');
                exit();

            } catch (\PDOException $e) {
                $msg = str_contains($e->getMessage(), 'nombre del rol ya existe')
                    ? 'El nombre del rol ya existe.'
                    : 'Error al guardar. Intenta de nuevo.';
                error_log("[DeskCod] RolesRegistry: " . $e->getMessage());
                $_SESSION['flash_error'] = $msg;
                header("Location: " . ($id > 0 ? "/Empleados/RolesRegistry/{$id}" : '/Empleados/RolesRegistry'));
                exit();
            }
        }

        $error   = $_SESSION['flash_error']   ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        return compact('rol', 'modulos', 'acciones', 'permisos', 'error', 'success');
    }

    // ============================================
    // POST /Empleados/desactivar — JSON
    // ============================================
    public function desactivar(): void {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido.']);
            exit();
        }

        // No puede desactivarse a sí mismo
        if ($id === (int)($_SESSION['system']['UserID'] ?? 0)) {
            echo json_encode(['success' => false, 'message' => 'No puedes desactivar tu propia cuenta.']);
            exit();
        }

        try {
            $this->model->desactivar($id);
            $this->auditar('EMPLEADO_DESACTIVADO', $id);
            echo json_encode(['success' => true, 'message' => 'Empleado desactivado correctamente.']);
        } catch (\PDOException $e) {
            $msg = str_contains($e->getMessage(), 'único administrador')
                ? 'No puedes desactivar al único administrador.'
                : 'Error al desactivar.';
            error_log("[DeskCod] Desactivar empleado: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $msg]);
        }
        exit();
    }

    // ── Helpers ───────────────────────────────

    private function sanitizarEmpleado(): array {
        return [
            'nombre'   => trim(htmlspecialchars($_POST['nombre']   ?? '')),
            'email'    => trim(strtolower($_POST['email']          ?? '')),
            'username' => trim(strtolower($_POST['username']       ?? '')),
            'password' => $_POST['password']                       ?? '',
            'rol_id'   => (int)($_POST['rol_id']                   ?? 0),
            'telefono' => trim($_POST['telefono']                  ?? ''),
        ];
    }

    private function validarEmpleado(array $d, int $id): array {
        $e = [];
        if (empty($d['nombre']))   $e[] = 'El nombre es obligatorio.';
        if (empty($d['email']))    $e[] = 'El correo es obligatorio.';
        elseif (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) $e[] = 'El correo no es válido.';
        if (empty($d['username'])) $e[] = 'El nombre de usuario es obligatorio.';
        if (empty($d['rol_id']))   $e[] = 'Selecciona un rol.';
        // Password obligatorio solo al crear
        if ($id === 0 && empty($d['password'])) $e[] = 'La contraseña es obligatoria.';
        if (!empty($d['password']) && strlen($d['password']) < 8) $e[] = 'La contraseña debe tener al menos 8 caracteres.';
        return $e;
    }

    private function auditar(string $accion, int $registroId): void {
        try {
            $db = (new \Config\Conexion())->getConexion();
            $db->prepare("INSERT INTO auditoria_acciones (empleado_id,accion,tabla,registro_id,ip) VALUES (?,?,?,?,?)")
               ->execute([
                   $_SESSION['system']['UserID'] ?? 1,
                   $accion,
                   'empleados',
                   $registroId,
                   $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
               ]);
        } catch (\PDOException $e) {
            error_log("[DeskCod] Auditoría: " . $e->getMessage());
        }
    }
}
?>