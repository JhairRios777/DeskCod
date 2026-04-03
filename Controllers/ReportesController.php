<?php
namespace Controllers;

use Config\Conexion;

class ReportesController {

    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    // ============================================
    // GET /Reportes — dashboard de reportes
    // ============================================
    public function index(): array {
        $anio = (int)($_GET['anio'] ?? date('Y'));
        $dias = (int)($_GET['dias'] ?? 30);

        // Ingresos por mes
        $stmt = $this->db->prepare("CALL sp_reporte_ingresos_mes(?)");
        $stmt->execute([$anio]);
        $ingresosMes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        // Saldos pendientes
        $stmt = $this->db->query("CALL sp_reporte_saldos_pendientes()");
        $saldosPendientes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        // Suscripciones por vencer
        $stmt = $this->db->prepare("CALL sp_reporte_suscripciones_por_vencer(?)");
        $stmt->execute([$dias]);
        $suscripcionesPorVencer = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        // Tickets resumen
        $stmt = $this->db->query("CALL sp_reporte_tickets_resumen()");
        $ticketsResumen = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        // Empleados activos
        $stmt = $this->db->prepare("CALL sp_reporte_empleados_activos(?)");
        $stmt->execute([$dias]);
        $empleadosActivos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        // Totales rápidos
        $totalIngresosAnio  = array_sum(array_column($ingresosMes, 'total_ingresos'));
        $totalPendiente     = array_sum(array_column($saldosPendientes, 'saldo_pendiente'));
        $totalPorVencer     = count($suscripcionesPorVencer);

        return compact(
            'ingresosMes', 'saldosPendientes', 'suscripcionesPorVencer',
            'ticketsResumen', 'empleadosActivos',
            'totalIngresosAnio', 'totalPendiente', 'totalPorVencer',
            'anio', 'dias'
        );
    }
}
?>