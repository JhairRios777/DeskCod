<?php
namespace Models;

use Config\Conexion;

class SuscripcionesModel {

    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    public function obtenerTodas(): array {
        $stmt = $this->db->query("
            SELECT
                s.id,
                s.estado,
                s.fecha_inicio,
                s.fecha_vencimiento,
                s.renovacion_plan_id,
                s.created_at,
                c.id            AS cliente_id,
                c.nombre        AS cliente_nombre,
                c.email         AS cliente_email,
                c.empresa_nombre,
                p.id            AS plan_id,
                p.nombre        AS plan_nombre,
                p.precio        AS plan_precio,
                pr.nombre       AS renovacion_plan_nombre
            FROM suscripciones s
            INNER JOIN clientes c  ON c.id  = s.cliente_id
            INNER JOIN planes   p  ON p.id  = s.plan_id
            LEFT  JOIN planes   pr ON pr.id = s.renovacion_plan_id
            WHERE c.activo = 1
            ORDER BY s.created_at DESC
        ");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function obtenerPorId(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT
                s.*,
                c.nombre        AS cliente_nombre,
                c.email         AS cliente_email,
                c.empresa_nombre,
                p.nombre        AS plan_nombre,
                p.precio        AS plan_precio,
                pr.nombre       AS renovacion_plan_nombre
            FROM suscripciones s
            INNER JOIN clientes c  ON c.id  = s.cliente_id
            INNER JOIN planes   p  ON p.id  = s.plan_id
            LEFT  JOIN planes   pr ON pr.id = s.renovacion_plan_id
            WHERE s.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $row ?: null;
    }

    public function obtenerPorCliente(int $clienteId): array {
        $stmt = $this->db->prepare("CALL sp_suscripciones_por_cliente(:cid)");
        $stmt->bindParam(':cid', $clienteId, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function crear(array $datos): int {
        $stmt = $this->db->prepare("CALL sp_suscripciones_crear(?,?,?,?,?)");
        $stmt->execute([
            $datos['cliente_id'],
            $datos['plan_id'],
            $datos['fecha_inicio'],
            $datos['fecha_vencimiento'],
            $datos['notas'] ?? '',
        ]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return (int)($result['id'] ?? 0);
    }

    public function suspender(int $clienteId): void {
        $stmt = $this->db->prepare("CALL sp_suscripciones_suspender(?)");
        $stmt->execute([$clienteId]);
        $stmt->closeCursor();
    }

    public function reactivar(int $clienteId): void {
        $stmt = $this->db->prepare("CALL sp_suscripciones_reactivar(?)");
        $stmt->execute([$clienteId]);
        $stmt->closeCursor();
    }

    public function cambiarPlan(int $clienteId, int $planId): void {
        $stmt = $this->db->prepare("CALL sp_suscripciones_cambiar_plan(?,?)");
        $stmt->execute([$clienteId, $planId]);
        $stmt->closeCursor();
    }

    public function obtenerClientes(): array {
        $stmt = $this->db->query("
            SELECT id, nombre, email, empresa_nombre
            FROM clientes
            WHERE activo = 1
            ORDER BY nombre ASC
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
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