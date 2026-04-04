<?php
namespace Controllers;

use Config\Conexion;
use Controllers\Autorizable;

class HomeController {
    use Autorizable;

    private $db;

    public function __construct() {
        $this->db = (new Conexion())->getConexion();
        $this->requireLogin();
        $this->requirePermiso('dashboard', 'ver');
    }

    public function index(): array {
        // ── Métricas KPI ─────────────────────────
        try {
            $stmt = $this->db->query("CALL sp_dashboard_metricas()");
            $metricas = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        } catch (\PDOException $e) {
            error_log("[DeskCod] Dashboard metricas: " . $e->getMessage());
            $metricas = [
                'total_clientes'           => 0,
                'suscripciones_activas'    => 0,
                'suscripciones_por_vencer' => 0,
                'tickets_abiertos'         => 0,
                'ingresos_mes'             => 0,
                'saldo_pendiente'          => 0,
            ];
        }

        // ── Ingresos por mes para la gráfica ─────
        try {
            $stmt = $this->db->query("CALL sp_dashboard_ingresos_mes()");
            $rawIngresos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        } catch (\PDOException $e) {
            error_log("[DeskCod] Dashboard ingresos mes: " . $e->getMessage());
            $rawIngresos = [];
        }

        // Convierte a array indexado por mes [1..12] → [0..11] para Chart.js
        $ingresosMes = array_fill(0, 12, 0);
        foreach ($rawIngresos as $row) {
            $idx = (int)$row['mes'] - 1;
            if ($idx >= 0 && $idx < 12) {
                $ingresosMes[$idx] = (float)$row['total'];
            }
        }

        // ── Actividad reciente — últimas 8 acciones ──
        try {
            $userId = (int)($_SESSION['system']['UserID'] ?? 0);
            $stmt = $this->db->prepare("
                SELECT a.accion, a.tabla, a.created_at,
                       e.nombre AS empleado_nombre
                FROM auditoria_acciones a
                INNER JOIN empleados e ON e.id = a.empleado_id
                ORDER BY a.created_at DESC
                LIMIT 8
            ");
            $stmt->execute();
            $actividad = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        } catch (\PDOException $e) {
            error_log("[DeskCod] Dashboard actividad: " . $e->getMessage());
            $actividad = [];
        }

        // ── Suscripciones próximas a vencer (top 5) ──
        try {
            $stmt = $this->db->query("
                SELECT c.nombre AS cliente_nombre, p.nombre AS plan_nombre,
                       s.fecha_vencimiento,
                       DATEDIFF(s.fecha_vencimiento, CURDATE()) AS dias_restantes
                FROM suscripciones s
                INNER JOIN clientes c ON c.id = s.cliente_id
                INNER JOIN planes p   ON p.id = s.plan_id
                WHERE s.estado IN ('activa','por_vencer')
                  AND s.fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                ORDER BY s.fecha_vencimiento ASC
                LIMIT 5
            ");
            $proximasAVencer = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        } catch (\PDOException $e) {
            error_log("[DeskCod] Dashboard proximas: " . $e->getMessage());
            $proximasAVencer = [];
        }

        return [
            'metricas'        => $metricas,
            'ingresosMes'     => $ingresosMes,      // array[12] para Chart.js
            'actividad'       => $actividad,
            'proximasAVencer' => $proximasAVencer,
            'userName'        => $_SESSION['system']['UserName'] ?? 'Usuario',
            'esAdmin'         => $_SESSION['system']['EsAdmin']  ?? 0,
        ];
    }
}
?>