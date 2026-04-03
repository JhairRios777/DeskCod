<?php
namespace Controllers;

use Config\Conexion;

class PerfilController {

    private $db;

    public function __construct() {
        $this->db = (new Conexion())->getConexion();
    }

    // ============================================
    // GET /Perfil — vista del perfil
    // POST /Perfil → guardar cambios
    // ============================================
    public function index(): array {
        $userId = (int)($_SESSION['system']['UserID'] ?? 0);

        // Obtiene datos actuales del empleado
        $stmt = $this->db->prepare("
            SELECT e.*, r.nombre AS rol_nombre
            FROM empleados e
            INNER JOIN roles r ON r.id = e.rol_id
            WHERE e.id = ? LIMIT 1
        ");
        $stmt->execute([$userId]);
        $empleado = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        // Últimos accesos — últimas 10 entradas de auditoría de login
        $stmt = $this->db->prepare("
            SELECT accion, ip, created_at
            FROM auditoria_acciones
            WHERE empleado_id = ?
              AND accion IN ('LOGIN_OK','LOGOUT')
            ORDER BY created_at DESC
            LIMIT 10
        ");
        $stmt->execute([$userId]);
        $accesos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        // Actividad reciente propia — últimas 15 acciones
        $stmt = $this->db->prepare("
            SELECT accion, tabla, registro_id, ip, created_at
            FROM auditoria_acciones
            WHERE empleado_id = ?
              AND accion NOT IN ('LOGIN_OK','LOGOUT')
            ORDER BY created_at DESC
            LIMIT 15
        ");
        $stmt->execute([$userId]);
        $actividad = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['ActualizarPerfil'])) {
                $this->actualizarPerfil($userId);
            } elseif (isset($_POST['CambiarPassword'])) {
                $this->cambiarPassword($userId, $empleado['password_hash']);
            }
        }

        $error   = $_SESSION['flash_error']   ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        return compact('empleado', 'accesos', 'actividad', 'error', 'success');
    }

    // ── Actualizar datos del perfil ──
    private function actualizarPerfil(int $userId): void {
        $nombre   = trim(htmlspecialchars($_POST['nombre']   ?? ''));
        $email    = trim(strtolower($_POST['email']          ?? ''));
        $username = trim(strtolower($_POST['username']       ?? ''));
        $telefono = trim($_POST['telefono']                  ?? '');

        if (empty($nombre) || empty($email) || empty($username)) {
            $_SESSION['flash_error'] = 'Nombre, email y usuario son obligatorios.';
            header('Location: /Perfil');
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'El correo no es válido.';
            header('Location: /Perfil');
            exit();
        }

        try {
            // Verifica que email y username no estén en uso por otro empleado
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM empleados
                WHERE (email = ? OR username = ?) AND id != ?
            ");
            $stmt->execute([$email, $username, $userId]);
            if ($stmt->fetchColumn() > 0) {
                $_SESSION['flash_error'] = 'El email o usuario ya está en uso por otro empleado.';
                header('Location: /Perfil');
                exit();
            }

            $stmt = $this->db->prepare("
                UPDATE empleados SET nombre=?, email=?, username=?, telefono=? WHERE id=?
            ");
            $stmt->execute([$nombre, $email, $username, $telefono, $userId]);

            // Actualiza la sesión
            $_SESSION['system']['UserName'] = $nombre;
            $_SESSION['system']['Email']    = $email;

            $this->auditar('PERFIL_ACTUALIZADO', $userId);
            $_SESSION['flash_success'] = 'Perfil actualizado correctamente.';

        } catch (\PDOException $e) {
            error_log("[DeskCod] Perfil actualizar: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Error al actualizar el perfil.';
        }

        header('Location: /Perfil');
        exit();
    }

    // ── Cambiar contraseña ──
    private function cambiarPassword(int $userId, string $hashActual): void {
        $actual  = $_POST['password_actual']  ?? '';
        $nueva   = $_POST['password_nueva']   ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if (!password_verify($actual, $hashActual)) {
            $_SESSION['flash_error'] = 'La contraseña actual no es correcta.';
            header('Location: /Perfil');
            exit();
        }

        if (strlen($nueva) < 8) {
            $_SESSION['flash_error'] = 'La nueva contraseña debe tener al menos 8 caracteres.';
            header('Location: /Perfil');
            exit();
        }

        if ($nueva !== $confirm) {
            $_SESSION['flash_error'] = 'Las contraseñas no coinciden.';
            header('Location: /Perfil');
            exit();
        }

        try {
            $hash = password_hash($nueva, PASSWORD_BCRYPT, ['cost' => 10]);
            $stmt = $this->db->prepare("UPDATE empleados SET password_hash=? WHERE id=?");
            $stmt->execute([$hash, $userId]);

            $this->auditar('PASSWORD_CAMBIADO', $userId);
            $_SESSION['flash_success'] = 'Contraseña actualizada correctamente.';

        } catch (\PDOException $e) {
            error_log("[DeskCod] Password cambiar: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Error al cambiar la contraseña.';
        }

        header('Location: /Perfil');
        exit();
    }

    private function auditar(string $accion, int $registroId): void {
        try {
            $this->db->prepare("INSERT INTO auditoria_acciones (empleado_id,accion,tabla,registro_id,ip) VALUES (?,?,?,?,?)")
                ->execute([
                    $_SESSION['system']['UserID'] ?? 1,
                    $accion, 'empleados', $registroId,
                    $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
                ]);
        } catch (\PDOException $e) {
            error_log("[DeskCod] Auditoría: " . $e->getMessage());
        }
    }
}
?>