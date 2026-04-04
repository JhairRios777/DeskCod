<?php
namespace Controllers;

use Config\Conexion;
use Controllers\Autorizable;

class PerfilController {
    use Autorizable;

    private $db;
    private const UPLOAD_DIR = 'Content/Uploads/fotos/';
    private const MAX_SIZE   = 3 * 1024 * 1024;
    private const TIPOS      = ['image/jpeg', 'image/jpg', 'image/png'];

    public function __construct() {
        $this->db = (new Conexion())->getConexion();
        $this->requireLogin();

    }

    public function index(): array {
        $userId = (int)($_SESSION['system']['UserID'] ?? 0);

        $stmt = $this->db->prepare("
            SELECT e.*, r.nombre AS rol_nombre
            FROM empleados e
            INNER JOIN roles r ON r.id = e.rol_id
            WHERE e.id = ? LIMIT 1
        ");
        $stmt->execute([$userId]);
        $empleado = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $stmt = $this->db->prepare("
            SELECT accion, ip, created_at FROM auditoria_acciones
            WHERE empleado_id = ? AND accion IN ('LOGIN_OK','LOGOUT')
            ORDER BY created_at DESC LIMIT 10
        ");
        $stmt->execute([$userId]);
        $accesos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $stmt = $this->db->prepare("
            SELECT accion, tabla, registro_id, ip, created_at FROM auditoria_acciones
            WHERE empleado_id = ? AND accion NOT IN ('LOGIN_OK','LOGOUT')
            ORDER BY created_at DESC LIMIT 15
        ");
        $stmt->execute([$userId]);
        $actividad = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['ActualizarPerfil'])) $this->actualizarPerfil($userId);
            if (isset($_POST['CambiarPassword']))  $this->cambiarPassword($userId, $empleado['password_hash']);
            if (isset($_POST['ActualizarFoto']))   $this->actualizarFoto($userId, $empleado['foto'] ?? null);
        }

        $error   = $_SESSION['flash_error']   ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        return compact('empleado', 'accesos', 'actividad', 'error', 'success');
    }

    private function actualizarPerfil(int $userId): void {
        $nombre   = trim(htmlspecialchars($_POST['nombre']   ?? ''));
        $email    = trim(strtolower($_POST['email']          ?? ''));
        $username = trim(strtolower($_POST['username']       ?? ''));
        $telefono = trim($_POST['telefono']                  ?? '');

        if (empty($nombre) || empty($email) || empty($username)) {
            $_SESSION['flash_error'] = 'Nombre, email y usuario son obligatorios.';
            header('Location: /Perfil'); exit();
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'El correo no es válido.';
            header('Location: /Perfil'); exit();
        }

        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM empleados WHERE (email=? OR username=?) AND id!=?
            ");
            $stmt->execute([$email, $username, $userId]);
            if ($stmt->fetchColumn() > 0) {
                $_SESSION['flash_error'] = 'El email o usuario ya está en uso.';
                header('Location: /Perfil'); exit();
            }
            $this->db->prepare("UPDATE empleados SET nombre=?,email=?,username=?,telefono=? WHERE id=?")
                     ->execute([$nombre, $email, $username, $telefono, $userId]);
            $_SESSION['system']['UserName'] = $nombre;
            $_SESSION['system']['Email']    = $email;
            $this->auditar('PERFIL_ACTUALIZADO', $userId);
            $_SESSION['flash_success'] = 'Perfil actualizado correctamente.';
        } catch (\PDOException $e) {
            error_log("[DeskCod] Perfil: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Error al actualizar el perfil.';
        }
        header('Location: /Perfil'); exit();
    }

    private function cambiarPassword(int $userId, string $hashActual): void {
        $actual  = $_POST['password_actual']  ?? '';
        $nueva   = $_POST['password_nueva']   ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if (!password_verify($actual, $hashActual)) {
            $_SESSION['flash_error'] = 'La contraseña actual no es correcta.';
            header('Location: /Perfil'); exit();
        }
        if (strlen($nueva) < 8) {
            $_SESSION['flash_error'] = 'La nueva contraseña debe tener al menos 8 caracteres.';
            header('Location: /Perfil'); exit();
        }
        if ($nueva !== $confirm) {
            $_SESSION['flash_error'] = 'Las contraseñas no coinciden.';
            header('Location: /Perfil'); exit();
        }

        try {
            $hash = password_hash($nueva, PASSWORD_BCRYPT, ['cost' => 10]);
            $this->db->prepare("UPDATE empleados SET password_hash=? WHERE id=?")
                     ->execute([$hash, $userId]);
            $this->auditar('PASSWORD_CAMBIADO', $userId);
            $_SESSION['flash_success'] = 'Contraseña actualizada correctamente.';
        } catch (\PDOException $e) {
            error_log("[DeskCod] Password: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Error al cambiar la contraseña.';
        }
        header('Location: /Perfil'); exit();
    }

    private function actualizarFoto(int $userId, ?string $fotoActual): void {
        if (empty($_FILES['foto']['name'])) {
            $_SESSION['flash_error'] = 'Selecciona una imagen.';
            header('Location: /Perfil'); exit();
        }

        $result = $this->procesarImagen($_FILES['foto'], 'foto_' . $userId);
        if ($result['error']) {
            $_SESSION['flash_error'] = $result['error'];
            header('Location: /Perfil'); exit();
        }

        try {
            if ($fotoActual && file_exists(ROOT . $fotoActual)) unlink(ROOT . $fotoActual);
            $this->db->prepare("UPDATE empleados SET foto=? WHERE id=?")
                     ->execute([$result['ruta'], $userId]);
            $_SESSION['system']['Foto'] = $result['ruta'];
            $this->auditar('FOTO_ACTUALIZADA', $userId);
            $_SESSION['flash_success'] = 'Foto de perfil actualizada.';
        } catch (\PDOException $e) {
            if (file_exists(ROOT . $result['ruta'])) unlink(ROOT . $result['ruta']);
            error_log("[DeskCod] Foto: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Error al guardar la foto.';
        }
        header('Location: /Perfil'); exit();
    }

    private function procesarImagen(array $file, string $prefijo): array {
        if ($file['error'] !== UPLOAD_ERR_OK)
            return ['error' => 'Error al subir la imagen.', 'ruta' => null];
        if ($file['size'] > self::MAX_SIZE)
            return ['error' => 'La imagen no puede superar 3MB.', 'ruta' => null];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, self::TIPOS))
            return ['error' => 'Solo se aceptan imágenes JPG o PNG.', 'ruta' => null];
        $ext    = $mime === 'image/png' ? 'png' : 'jpg';
        $nombre = $prefijo . '_' . uniqid() . '.' . $ext;
        $dirAbs = ROOT . self::UPLOAD_DIR;
        if (!is_dir($dirAbs)) mkdir($dirAbs, 0755, true);
        if (!move_uploaded_file($file['tmp_name'], $dirAbs . $nombre))
            return ['error' => 'No se pudo guardar la imagen.', 'ruta' => null];
        return ['error' => null, 'ruta' => self::UPLOAD_DIR . $nombre];
    }

    private function auditar(string $accion, int $registroId): void {
        try {
            $this->db->prepare("INSERT INTO auditoria_acciones (empleado_id,accion,tabla,registro_id,ip) VALUES (?,?,?,?,?)")
                ->execute([$_SESSION['system']['UserID'] ?? 1, $accion, 'empleados', $registroId, $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0']);
        } catch (\PDOException $e) {
            error_log("[DeskCod] Auditoría: " . $e->getMessage());
        }
    }
}
?>