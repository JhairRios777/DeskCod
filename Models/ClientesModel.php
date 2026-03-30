<?php
namespace Models;

use Config\Conexion;

// ============================================
// ClientesModel.php
// Responsabilidad única: datos de clientes
// ============================================

class ClientesModel {

    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    public function obtenerTodos(): array {
        $stmt = $this->db->query("CALL sp_clientes_obtener_todos()");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function obtenerPorId(int $id): ?array {
        $stmt = $this->db->prepare("CALL sp_clientes_obtener_por_id(:id)");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $row ?: null;
    }

    public function crear(array $datos): int {
        $stmt = $this->db->prepare("
            CALL sp_clientes_crear(
                :nombre, :email, :telefono,
                :empresa_nombre, :nit_ruc, :direccion
            )
        ");
        $stmt->bindParam(':nombre',         $datos['nombre'],         \PDO::PARAM_STR);
        $stmt->bindParam(':email',          $datos['email'],          \PDO::PARAM_STR);
        $stmt->bindParam(':telefono',       $datos['telefono'],       \PDO::PARAM_STR);
        $stmt->bindParam(':empresa_nombre', $datos['empresa_nombre'], \PDO::PARAM_STR);
        $stmt->bindParam(':nit_ruc',        $datos['nit_ruc'],        \PDO::PARAM_STR);
        $stmt->bindParam(':direccion',      $datos['direccion'],      \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return (int)($result['id'] ?? 0);
    }

    public function actualizar(int $id, array $datos): void {
        $stmt = $this->db->prepare("
            CALL sp_clientes_actualizar(
                :id, :nombre, :email, :telefono,
                :empresa_nombre, :nit_ruc, :direccion
            )
        ");
        $stmt->bindParam(':id',             $id,                      \PDO::PARAM_INT);
        $stmt->bindParam(':nombre',         $datos['nombre'],         \PDO::PARAM_STR);
        $stmt->bindParam(':email',          $datos['email'],          \PDO::PARAM_STR);
        $stmt->bindParam(':telefono',       $datos['telefono'],       \PDO::PARAM_STR);
        $stmt->bindParam(':empresa_nombre', $datos['empresa_nombre'], \PDO::PARAM_STR);
        $stmt->bindParam(':nit_ruc',        $datos['nit_ruc'],        \PDO::PARAM_STR);
        $stmt->bindParam(':direccion',      $datos['direccion'],      \PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }

    public function desactivar(int $id): void {
        $stmt = $this->db->prepare("CALL sp_clientes_desactivar(:id)");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }

    public function obtenerPlanes(): array {
        $stmt = $this->db->query("
            SELECT id, nombre, precio, duracion_dias
            FROM planes
            WHERE activo = 1
            ORDER BY precio ASC
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>