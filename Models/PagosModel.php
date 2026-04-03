<?php
namespace Models;

use Config\Conexion;

class PagosModel {

    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    // ── Pagos ─────────────────────────────────

    public function obtenerTodos(): array {
        $stmt = $this->db->query("CALL sp_pagos_obtener_todos()");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function obtenerPorCliente(int $clienteId): array {
        $stmt = $this->db->prepare("CALL sp_pagos_obtener_por_cliente(?)");
        $stmt->execute([$clienteId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function registrar(array $datos): array {
        $stmt = $this->db->prepare("CALL sp_pagos_registrar_con_cuenta(?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $datos['cliente_id'],
            $datos['cuenta_id'] ?: null,
            $datos['suscripcion_id'] ?: null,
            $datos['metodo_pago_id'],
            $datos['concepto'],
            $datos['monto'],
            $datos['referencia'] ?: null,
            $datos['notas'] ?: null,
            $datos['fecha_pago'] ?: null,
        ]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (!empty($datos['comprobante_imagen']) && !empty($result['pago_id'])) {
            $upd = $this->db->prepare("UPDATE pagos SET comprobante_imagen = ? WHERE id = ?");
            $upd->execute([$datos['comprobante_imagen'], $result['pago_id']]);
        }

        return $result ?: [];
    }

    // ── Cuentas por cobrar ────────────────────

    public function obtenerCuentasTodas(): array {
        $stmt = $this->db->query("CALL sp_cuentas_obtener_todas()");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function obtenerCuentasPorCliente(int $clienteId): array {
        $stmt = $this->db->prepare("CALL sp_cuentas_por_cliente(?)");
        $stmt->execute([$clienteId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function crearCuenta(array $datos): int {
        $stmt = $this->db->prepare("CALL sp_cuentas_crear(?,?,?,?,?,?)");
        $stmt->execute([
            $datos['cliente_id'],
            $datos['tipo'],
            $datos['descripcion'],
            $datos['monto_total'],
            $datos['suscripcion_id'] ?: null,
            $datos['notas'] ?: null,
        ]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return (int)($result['id'] ?? 0);
    }

    // ── Helpers ───────────────────────────────

    public function obtenerClientes(): array {
        $stmt = $this->db->query("
            SELECT id, nombre, email, empresa_nombre
            FROM clientes WHERE activo = 1 ORDER BY nombre ASC
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerMetodosPago(): array {
        $stmt = $this->db->query("SELECT id, nombre FROM metodos_pago ORDER BY id ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerSuscripcionesCliente(int $clienteId): array {
        $stmt = $this->db->prepare("
            SELECT s.id, s.estado, s.fecha_vencimiento,
                   p.nombre AS plan_nombre, p.precio
            FROM suscripciones s
            INNER JOIN planes p ON p.id = s.plan_id
            WHERE s.cliente_id = ?
              AND s.estado IN ('activa','por_vencer','vencida')
            ORDER BY s.created_at DESC
        ");
        $stmt->execute([$clienteId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }

    public function obtenerCuentasCliente(int $clienteId): array {
        $stmt = $this->db->prepare("
            SELECT id, tipo, descripcion, monto_total, monto_pagado,
                   monto_total - monto_pagado AS saldo_pendiente, estado
            FROM cuentas_por_cobrar
            WHERE cliente_id = ? AND estado != 'pagado'
            ORDER BY created_at DESC
        ");
        $stmt->execute([$clienteId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }
}
?>