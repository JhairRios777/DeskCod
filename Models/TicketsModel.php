<?php
namespace Models;

use Config\Conexion;

class TicketsModel {

    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    public function obtenerTodos(): array {
        $stmt = $this->db->query("CALL sp_tickets_obtener_todos()");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function obtenerPorId(int $id): ?array {
        $stmt = $this->db->prepare("CALL sp_tickets_obtener_por_id(?)");
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $row ?: null;
    }

    public function crear(array $datos): int {
    $stmt = $this->db->prepare("CALL sp_tickets_crear(?,?,?,?,?,?,?,?,?)");
    $stmt->execute([
        $datos['cliente_id'],
        $datos['suscripcion_id'],  // ← nuevo parámetro
        $datos['empleado_id'],
        $datos['titulo'],
        $datos['descripcion'],
        $datos['tipo'],
        $datos['prioridad'],
        $datos['categoria'],
        $datos['fecha_limite'] ?: null,
    ]);
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return (int)($result['id'] ?? 0);
}

    public function cambiarEstado(int $id, string $estado, int $empleadoId): void {
        $stmt = $this->db->prepare("CALL sp_tickets_cambiar_estado(?,?,?)");
        $stmt->execute([$id, $estado, $empleadoId]);
        $stmt->closeCursor();
    }

    public function asignar(int $ticketId, int $empleadoId): void {
        $stmt = $this->db->prepare("CALL sp_tickets_asignar(?,?)");
        $stmt->execute([$ticketId, $empleadoId]);
        $stmt->closeCursor();
    }

    public function agregarComentario(int $ticketId, int $empleadoId, string $tipo, string $contenido): int {
        $stmt = $this->db->prepare("CALL sp_ticket_comentarios_agregar(?,?,?,?)");
        $stmt->execute([$ticketId, $empleadoId, $tipo, $contenido]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return (int)($result['id'] ?? 0);
    }

    public function obtenerComentarios(int $ticketId): array {
        $stmt = $this->db->prepare("CALL sp_ticket_comentarios_obtener(?)");
        $stmt->execute([$ticketId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function obtenerHistorial(int $ticketId): array {
        $stmt = $this->db->prepare("CALL sp_ticket_historial_obtener(?)");
        $stmt->execute([$ticketId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function obtenerClientes(): array {
        $stmt = $this->db->query("SELECT id, nombre, email, empresa_nombre FROM clientes WHERE activo = 1 ORDER BY nombre ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerEmpleados(): array {
        $stmt = $this->db->query("SELECT id, nombre FROM empleados WHERE activo = 1 ORDER BY nombre ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>