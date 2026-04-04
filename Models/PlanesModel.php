<?php
namespace Models;

use Config\Conexion;

class PlanesModel {

    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    public function obtenerTodos(): array {
        $stmt = $this->db->query("CALL sp_planes_obtener_todos()");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function obtenerPorId(int $id): ?array {
        $stmt = $this->db->prepare("CALL sp_planes_obtener_por_id(?)");
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $row ?: null;
    }

    public function crear(array $datos): int {
        $stmt = $this->db->prepare("CALL sp_planes_crear(?,?,?,?,?,?)");
        $stmt->execute([
            $datos['nombre'],
            $datos['descripcion'],
            $datos['precio'],
            $datos['duracion_dias'],
            $datos['max_tickets'] ?: null,
            $datos['descuento_anual'] ?? 0,
        ]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return (int)($result['id'] ?? 0);
    }

    public function actualizar(int $id, array $datos): void {
        $stmt = $this->db->prepare("CALL sp_planes_actualizar(?,?,?,?,?,?,?)");
        $stmt->execute([
            $id,
            $datos['nombre'],
            $datos['descripcion'],
            $datos['precio'],
            $datos['duracion_dias'],
            $datos['max_tickets'] ?: null,
            $datos['descuento_anual'] ?? 0,
        ]);
        $stmt->closeCursor();
    }

    public function desactivar(int $id): void {
        $stmt = $this->db->prepare("CALL sp_planes_desactivar(?)");
        $stmt->execute([$id]);
        $stmt->closeCursor();
    }
}
?>