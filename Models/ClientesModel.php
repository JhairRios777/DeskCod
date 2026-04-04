<?php
namespace Models;

use Config\Conexion;

class ClientesModel {

    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    // ── Clientes ──────────────────────────────

    public function obtenerTodos(): array {
        $stmt = $this->db->query("CALL sp_clientes_obtener_todos()");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function obtenerPorId(int $id): ?array {
        $stmt = $this->db->prepare("CALL sp_clientes_obtener_por_id(?)");
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $row ?: null;
    }

    public function crear(array $datos): int {
        $stmt = $this->db->prepare("CALL sp_clientes_crear(?,?,?,?,?,?)");
        $stmt->execute([
            $datos['nombre'],
            $datos['email'],
            $datos['telefono'],
            $datos['empresa_nombre'],
            $datos['nit_ruc'],
            $datos['direccion'],
        ]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return (int)($result['id'] ?? 0);
    }

    public function actualizar(int $id, array $datos): void {
        $stmt = $this->db->prepare("CALL sp_clientes_actualizar(?,?,?,?,?,?,?)");
        $stmt->execute([
            $id,
            $datos['nombre'],
            $datos['email'],
            $datos['telefono'],
            $datos['empresa_nombre'],
            $datos['nit_ruc'],
            $datos['direccion'],
        ]);
        $stmt->closeCursor();
    }

    public function desactivar(int $id): void {
        $stmt = $this->db->prepare("CALL sp_clientes_desactivar(?)");
        $stmt->execute([$id]);
        $stmt->closeCursor();
    }

    public function obtenerPlanes(): array {
        $stmt = $this->db->query("
            SELECT id, nombre, precio, duracion_dias
            FROM planes WHERE activo = 1
            ORDER BY precio ASC
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // ── API Token ─────────────────────────────

    // Retorna mapa [cliente_id => token] para mostrar en la lista
    public function obtenerTokensTodos(): array {
        $stmt = $this->db->query("
            SELECT cliente_id, token FROM api_tokens WHERE activo = 1
        ");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $map  = [];
        foreach ($rows as $r) {
            $map[$r['cliente_id']] = $r['token'];
        }
        return $map;
    }

    // Retorna el token activo del cliente o null si no tiene
    public function obtenerToken(int $clienteId): ?string {
        $stmt = $this->db->prepare("
            SELECT token FROM api_tokens
            WHERE cliente_id = ? AND activo = 1
            LIMIT 1
        ");
        $stmt->execute([$clienteId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $row['token'] ?? null;
    }

    // Crea token al registrar un cliente nuevo
    // Si ya existe uno lo actualiza
    public function crearToken(int $clienteId, string $nombre): string {
        $token = hash('sha256', $clienteId . $nombre . time() . random_bytes(16));

        $stmt = $this->db->prepare("SELECT id FROM api_tokens WHERE cliente_id = ? LIMIT 1");
        $stmt->execute([$clienteId]);
        $existe = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if ($existe) {
            $stmt = $this->db->prepare("
                UPDATE api_tokens
                SET token = ?, activo = 1, ultimo_uso = NULL
                WHERE cliente_id = ?
            ");
            $stmt->execute([$token, $clienteId]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO api_tokens (cliente_id, nombre, token)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$clienteId, $nombre, $token]);
        }

        $stmt->closeCursor();
        return $token;
    }

    // Genera un token nuevo — invalida el anterior inmediatamente
    public function regenerarToken(int $clienteId): string {
        $token = hash('sha256', $clienteId . time() . random_bytes(16));
        $stmt  = $this->db->prepare("
            UPDATE api_tokens
            SET token = ?, ultimo_uso = NULL
            WHERE cliente_id = ? AND activo = 1
        ");
        $stmt->execute([$token, $clienteId]);
        $stmt->closeCursor();
        return $token;
    }
}
?>
