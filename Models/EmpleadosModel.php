<?php
namespace Models;

use Config\Conexion;

class EmpleadosModel {

    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    // ── Empleados ─────────────────────────────

    public function obtenerTodos(): array {
        $stmt = $this->db->query("CALL sp_empleados_obtener_todos()");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function obtenerPorId(int $id): ?array {
        $stmt = $this->db->prepare("CALL sp_empleados_obtener_por_id(?)");
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $row ?: null;
    }

    public function crear(array $datos): int {
        $hash = password_hash($datos['password'], PASSWORD_BCRYPT, ['cost' => 10]);
        $stmt = $this->db->prepare("CALL sp_empleados_crear(?,?,?,?,?,?)");
        $stmt->execute([
            $datos['nombre'],
            $datos['email'],
            $datos['username'],
            $hash,
            $datos['rol_id'],
            $datos['telefono'] ?? '',
        ]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return (int)($result['id'] ?? 0);
    }

    public function actualizar(int $id, array $datos): void {
        $stmt = $this->db->prepare("CALL sp_empleados_actualizar(?,?,?,?,?,?)");
        $stmt->execute([
            $id,
            $datos['nombre'],
            $datos['email'],
            $datos['username'],
            $datos['rol_id'],
            $datos['telefono'] ?? '',
        ]);
        $stmt->closeCursor();
    }

    public function cambiarPassword(int $id, string $password): void {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        $stmt = $this->db->prepare("CALL sp_empleados_cambiar_password(?,?)");
        $stmt->execute([$id, $hash]);
        $stmt->closeCursor();
    }

    public function desactivar(int $id): void {
        $stmt = $this->db->prepare("CALL sp_empleados_desactivar(?)");
        $stmt->execute([$id]);
        $stmt->closeCursor();
    }

    public function obtenerRoles(): array {
        $stmt = $this->db->query("SELECT id, nombre, descripcion, es_admin FROM roles WHERE activo = 1 ORDER BY id ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // ── Roles ─────────────────────────────────

    public function obtenerRolesTodos(): array {
        $stmt = $this->db->query("CALL sp_roles_obtener_todos()");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function obtenerRolPorId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT id, nombre, descripcion, es_admin FROM roles WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $row ?: null;
    }

    public function crearRol(array $datos): int {
        $stmt = $this->db->prepare("CALL sp_roles_crear(?,?,?)");
        $stmt->execute([
            $datos['nombre'],
            $datos['descripcion'],
            $datos['es_admin'] ? 1 : 0,
        ]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return (int)($result['id'] ?? 0);
    }

    public function actualizarRol(int $id, array $datos): void {
        $stmt = $this->db->prepare("CALL sp_roles_actualizar(?,?,?,?)");
        $stmt->execute([
            $id,
            $datos['nombre'],
            $datos['descripcion'],
            $datos['es_admin'] ? 1 : 0,
        ]);
        $stmt->closeCursor();
    }

    // ── Permisos ──────────────────────────────

    public function obtenerPermisosPorRol(int $rolId): array {
        $stmt = $this->db->prepare("CALL sp_rol_permisos_obtener(?)");
        $stmt->execute([$rolId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function guardarPermiso(int $rolId, int $moduloId, int $accionId, int $permitido): void {
        $stmt = $this->db->prepare("CALL sp_rol_permisos_guardar(?,?,?,?)");
        $stmt->execute([$rolId, $moduloId, $accionId, $permitido]);
        $stmt->closeCursor();
    }

    public function obtenerModulos(): array {
        $stmt = $this->db->query("SELECT id, nombre, label, icono FROM modulos WHERE activo = 1 ORDER BY orden ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerAcciones(): array {
        $stmt = $this->db->query("SELECT id, nombre, label FROM acciones ORDER BY id ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>