<?php
namespace Models;

use Config\Conexion;

// ============================================
// AuthModel.php — Modelo de autenticación
// Interactúa con la BD usando procedimientos
// almacenados. Nunca construye SQL dinámico.
// ============================================

class AuthModel {

    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    // ============================================
    // Busca el empleado por username usando el SP
    // Devuelve los datos para que el controller
    // verifique el hash con password_verify()
    // ============================================
    public function buscarPorUsername(string $username): ?array {

    // PASO 1 — Ejecuta el SP con el username
    $stmt = $this->db->prepare("
        CALL sp_auth_login(
            :username,
            @p_id, @p_nombre, @p_email,
            @p_hash, @p_rol_id, @p_es_admin, @p_activo
        )
    ");
    $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
    $stmt->execute();
    $stmt->closeCursor();

    // PASO 2 — Lee las variables de salida del SP
    $result = $this->db
        ->query("SELECT @p_id, @p_nombre, @p_email,
                        @p_hash, @p_rol_id, @p_es_admin, @p_activo")
        ->fetch(\PDO::FETCH_ASSOC);

    // Si no encontró el usuario devuelve null
    if (!$result || $result['@p_id'] === null) {
        return null;
    }

    return [
        'id'       => (int) $result['@p_id'],
        'nombre'   => $result['@p_nombre'],
        'email'    => $result['@p_email'],
        'hash'     => $result['@p_hash'],
        'rol_id'   => (int) $result['@p_rol_id'],
        'es_admin' => (int) $result['@p_es_admin'],
        'activo'   => (int) $result['@p_activo'],
    ];
}

    // ============================================
    // Registra el login exitoso en auditoría
    // y actualiza el campo ultimo_login
    // ============================================
    public function registrarLogin(int $empleadoId, string $ip): void {
        $stmt = $this->db->prepare("
            CALL sp_auth_actualizar_login(:empleado_id, :ip)
        ");
        $stmt->bindParam(':empleado_id', $empleadoId, \PDO::PARAM_INT);
        $stmt->bindParam(':ip',          $ip,         \PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }

    // ============================================
    // Registra el logout en auditoría
    // ============================================
    public function registrarLogout(int $empleadoId, string $ip): void {
        $stmt = $this->db->prepare("
            CALL sp_auth_logout(:empleado_id, :ip)
        ");
        $stmt->bindParam(':empleado_id', $empleadoId, \PDO::PARAM_INT);
        $stmt->bindParam(':ip',          $ip,         \PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }

    // ============================================
    // Obtiene los permisos del rol del empleado
    // Devuelve array ['modulo']['accion'] = true
    // ============================================
    public function obtenerPermisos(int $rolId): array {
        $stmt = $this->db->prepare("CALL sp_auth_permisos(:rol_id)");
        $stmt->bindParam(':rol_id', $rolId, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $permisos = [];
        foreach ($rows as $row) {
            $permisos[$row['modulo']][$row['accion']] = true;
        }

        return $permisos;
    }
}
?>