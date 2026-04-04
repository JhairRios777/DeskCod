<?php
namespace Controllers;

use Config\Conexion;
use Controllers\Autorizable;

class ReportesController {
use Autorizable;
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
        $this->requireLogin();
        $this->requirePermiso('reportes', 'ver');
        $this->requirePermiso('reportes', 'exportar');
    }

    // ============================================
    // GET /Reportes — dashboard principal
    // ============================================
    public function index(): array {
        $anio = (int)($_GET['anio'] ?? date('Y'));
        $dias = (int)($_GET['dias'] ?? 30);
        $datos = $this->obtenerDatos($anio, $dias);
        return array_merge($datos, compact('anio', 'dias'));
    }

    // ============================================
    // GET /Reportes/exportarPdf?tab=X&anio=Y&dias=Z
    // ============================================
    public function exportarPdf(): void {
        $tab  = $_GET['tab']  ?? 'ingresos';
        $anio = (int)($_GET['anio'] ?? date('Y'));
        $dias = (int)($_GET['dias'] ?? 30);
        $datos = $this->obtenerDatos($anio, $dias);
        $html  = $this->generarHtmlPdf($tab, $datos, $anio, $dias);
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: text/html; charset=UTF-8');
        echo $html;
        exit();
    }

    // ============================================
    // GET /Reportes/exportarExcel?tab=X&anio=Y&dias=Z
    // ============================================
    public function exportarExcel(): void {
        $tab  = $_GET['tab']  ?? 'ingresos';
        $anio = (int)($_GET['anio'] ?? date('Y'));
        $dias = (int)($_GET['dias'] ?? 30);
        $datos  = $this->obtenerDatos($anio, $dias);
        $nombre = 'reporte_' . $tab . '_' . date('Ymd_His') . '.csv';
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Cache-Control: no-cache, must-revalidate');
        echo "\xEF\xBB\xBF"; // BOM UTF-8 para Excel
        $out = fopen('php://output', 'w');
        $this->generarCsv($out, $tab, $datos, $anio, $dias);
        fclose($out);
        exit();
    }

    // ── Datos compartidos ─────────────────────
    private function obtenerDatos(int $anio, int $dias): array {
        $stmt = $this->db->prepare("CALL sp_reporte_ingresos_mes(?)");
        $stmt->execute([$anio]);
        $ingresosMes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $stmt = $this->db->query("CALL sp_reporte_saldos_pendientes()");
        $saldosPendientes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $stmt = $this->db->prepare("CALL sp_reporte_suscripciones_por_vencer(?)");
        $stmt->execute([$dias]);
        $suscripcionesPorVencer = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $stmt = $this->db->query("CALL sp_reporte_tickets_resumen()");
        $ticketsResumen = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $stmt = $this->db->prepare("CALL sp_reporte_empleados_activos(?)");
        $stmt->execute([$dias]);
        $empleadosActivos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return [
            'ingresosMes'           => $ingresosMes,
            'saldosPendientes'      => $saldosPendientes,
            'suscripcionesPorVencer'=> $suscripcionesPorVencer,
            'ticketsResumen'        => $ticketsResumen,
            'empleadosActivos'      => $empleadosActivos,
            'totalIngresosAnio'     => array_sum(array_column($ingresosMes, 'total_ingresos')),
            'totalPendiente'        => array_sum(array_column($saldosPendientes, 'saldo_pendiente')),
            'totalPorVencer'        => count($suscripcionesPorVencer),
        ];
    }

    // ── HTML para PDF (se imprime con Ctrl+P) ─
    private function generarHtmlPdf(string $tab, array $d, int $anio, int $dias): string {
        $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
                  'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        $fecha = date('d/m/Y H:i');
        $titulos = [
            'ingresos'      => "Ingresos por Mes — {$anio}",
            'saldos'        => 'Saldos Pendientes',
            'suscripciones' => "Suscripciones por Vencer — próximos {$dias} días",
            'tickets'       => 'Resumen de Tickets',
            'empleados'     => "Empleados más Activos — últimos {$dias} días",
        ];
        $titulo = $titulos[$tab] ?? 'Reporte';
        $cuerpo = '';

        switch ($tab) {
            case 'ingresos':
                $ingresosPorMes = array_column($d['ingresosMes'], null, 'mes');
                $cuerpo .= '<table><thead><tr><th>Mes</th><th>Ingresos (USD)</th><th>N° Pagos</th></tr></thead><tbody>';
                for ($m = 1; $m <= 12; $m++) {
                    $row   = $ingresosPorMes[$m] ?? null;
                    $monto = $row ? number_format((float)$row['total_ingresos'], 2) : '0.00';
                    $pagos = $row ? (int)$row['total_pagos'] : 0;
                    $cls   = ($m === (int)date('m') && $anio === (int)date('Y')) ? ' class="actual"' : '';
                    $cuerpo .= "<tr{$cls}><td>{$meses[$m]}</td><td>\${$monto}</td><td>{$pagos}</td></tr>";
                }
                $total = number_format($d['totalIngresosAnio'], 2);
                $cuerpo .= "<tr class='total'><td>TOTAL</td><td>\${$total}</td><td></td></tr></tbody></table>";
                break;

            case 'saldos':
                $cuerpo .= '<table><thead><tr>
                    <th>Cliente</th><th>Empresa</th><th>Cuentas</th>
                    <th>Total Deuda</th><th>Pagado</th><th>Pendiente</th><th>%</th>
                </tr></thead><tbody>';
                foreach ($d['saldosPendientes'] as $s) {
                    $pct = $s['total_deuda'] > 0 ? round(($s['total_pagado']/$s['total_deuda'])*100) : 0;
                    $cuerpo .= '<tr>
                        <td>'.htmlspecialchars($s['cliente_nombre']).'</td>
                        <td>'.htmlspecialchars($s['empresa_nombre'] ?? '').'</td>
                        <td>'.(int)$s['total_cuentas'].'</td>
                        <td>$'.number_format($s['total_deuda'],2).'</td>
                        <td>$'.number_format($s['total_pagado'],2).'</td>
                        <td class="pendiente">$'.number_format($s['saldo_pendiente'],2).'</td>
                        <td>'.$pct.'%</td></tr>';
                }
                $tot = number_format($d['totalPendiente'], 2);
                $cuerpo .= "<tr class='total'><td colspan='5'>TOTAL PENDIENTE</td>
                    <td class='pendiente'>\${$tot}</td><td></td></tr></tbody></table>";
                break;

            case 'suscripciones':
                $cuerpo .= '<table><thead><tr>
                    <th>Cliente</th><th>Empresa</th><th>Plan</th>
                    <th>Precio/mes</th><th>Vence</th><th>Días restantes</th>
                </tr></thead><tbody>';
                foreach ($d['suscripcionesPorVencer'] as $s) {
                    $dr  = (int)$s['dias_restantes'];
                    $cls = $dr <= 7 ? ' class="urgente"' : ($dr <= 15 ? ' class="advertencia"' : '');
                    $cuerpo .= "<tr{$cls}>
                        <td>".htmlspecialchars($s['cliente_nombre'])."</td>
                        <td>".htmlspecialchars($s['empresa_nombre'] ?? '')."</td>
                        <td>".htmlspecialchars($s['plan_nombre'])."</td>
                        <td>\$".number_format($s['plan_precio'],2)."</td>
                        <td>".date('d/m/Y', strtotime($s['fecha_vencimiento']))."</td>
                        <td>".($dr === 0 ? 'HOY' : $dr.' días')."</td></tr>";
                }
                $cuerpo .= '</tbody></table>';
                break;

            case 'tickets':
                $porEstado = []; $porPrioridad = [];
                foreach ($d['ticketsResumen'] as $t) {
                    $porEstado[$t['estado']]       = ($porEstado[$t['estado']] ?? 0) + $t['total'];
                    $porPrioridad[$t['prioridad']] = ($porPrioridad[$t['prioridad']] ?? 0) + $t['total'];
                }
                $totalT = array_sum($porEstado);
                $eMap = ['abierto'=>'Abierto','en_proceso'=>'En proceso',
                         'esperando_cliente'=>'Esperando cliente','resuelto'=>'Resuelto','cerrado'=>'Cerrado'];
                $cuerpo .= '<h3>Por Estado</h3><table><thead><tr><th>Estado</th><th>Total</th><th>%</th></tr></thead><tbody>';
                foreach ($eMap as $k => $l) {
                    $t = $porEstado[$k] ?? 0;
                    $pct = $totalT > 0 ? round(($t/$totalT)*100) : 0;
                    $cuerpo .= "<tr><td>{$l}</td><td>{$t}</td><td>{$pct}%</td></tr>";
                }
                $cuerpo .= "<tr class='total'><td>TOTAL</td><td>{$totalT}</td><td>100%</td></tr></tbody></table>";
                $pMap = ['critica'=>'Crítica','alta'=>'Alta','media'=>'Media','baja'=>'Baja'];
                $cuerpo .= '<h3 style="margin-top:20px;">Por Prioridad</h3><table><thead><tr><th>Prioridad</th><th>Total</th><th>%</th></tr></thead><tbody>';
                foreach ($pMap as $k => $l) {
                    $t = $porPrioridad[$k] ?? 0;
                    $pct = $totalT > 0 ? round(($t/$totalT)*100) : 0;
                    $cuerpo .= "<tr><td>{$l}</td><td>{$t}</td><td>{$pct}%</td></tr>";
                }
                $cuerpo .= '</tbody></table>';
                break;

            case 'empleados':
                $cuerpo .= '<table><thead><tr>
                    <th>#</th><th>Empleado</th><th>Email</th>
                    <th>Rol</th><th>Acciones</th><th>Última actividad</th>
                </tr></thead><tbody>';
                foreach ($d['empleadosActivos'] as $i => $e) {
                    $ultima = $e['ultima_accion']
                        ? date('d/m/Y H:i', strtotime($e['ultima_accion'])) : 'Sin actividad';
                    $cuerpo .= '<tr>
                        <td>'.($i+1).'</td>
                        <td>'.htmlspecialchars($e['nombre']).'</td>
                        <td>'.htmlspecialchars($e['email']).'</td>
                        <td>'.htmlspecialchars($e['rol_nombre']).'</td>
                        <td><strong>'.(int)$e['total_acciones'].'</strong></td>
                        <td>'.$ultima.'</td></tr>';
                }
                $cuerpo .= '</tbody></table>';
                break;
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>DeskCod — {$titulo}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Arial,sans-serif; font-size:12px; color:#212529; padding:20px; }
.header { border-bottom:3px solid #005C3E; padding-bottom:12px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:flex-end; }
.logo { font-size:22px; font-weight:900; color:#005C3E; }
.logo span { color:#00E676; }
.header h1 { font-size:17px; color:#005C3E; margin-top:4px; }
.meta { color:#6c757d; font-size:10px; margin-top:3px; }
table { width:100%; border-collapse:collapse; margin-top:10px; }
thead th { background:#005C3E; color:#fff; padding:8px 10px; text-align:left; font-size:11px; text-transform:uppercase; }
tbody td { padding:7px 10px; border-bottom:1px solid #dee2e6; }
tbody tr:nth-child(even) { background:#f8f9fa; }
tr.total td { background:#005C3E !important; color:#fff !important; font-weight:bold; }
tr.urgente td { background:rgba(220,53,69,0.12) !important; color:#dc3545; font-weight:bold; }
tr.advertencia td { background:rgba(255,193,7,0.12) !important; }
tr.actual td { background:rgba(0,230,118,0.12) !important; font-weight:bold; }
td.pendiente { color:#dc3545; font-weight:bold; }
h3 { color:#005C3E; font-size:13px; margin:15px 0 8px; }
.footer { margin-top:30px; border-top:1px solid #dee2e6; padding-top:8px; color:#6c757d; font-size:10px; text-align:right; }
@page { margin:15mm; }
@media print { .no-print { display:none !important; } }
</style>
</head>
<body>
<div class="header">
    <div>
        <div class="logo">Desk<span>Cod</span></div>
        <h1>{$titulo}</h1>
        <div class="meta">Generado el {$fecha}</div>
    </div>
    <div class="no-print">
        <button onclick="window.print()" style="background:#005C3E;color:#fff;border:none;padding:8px 16px;border-radius:6px;cursor:pointer;font-size:13px;">
            🖨️ Imprimir / Guardar PDF
        </button>
    </div>
</div>

{$cuerpo}

<div class="footer">DeskCod &copy; {$anio} — Documento generado automáticamente</div>
<script>
    // Auto abre el diálogo de impresión después de 500ms
    setTimeout(function(){ window.print(); }, 500);
</script>
</body>
</html>
HTML;
    }

    // ── CSV compatible con Excel ──────────────
    private function generarCsv($handle, string $tab, array $d, int $anio, int $dias): void {
        $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
                  'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        $sep = ';'; // ; es más compatible con Excel en español

        switch ($tab) {
            case 'ingresos':
                fputcsv($handle, ["REPORTE DE INGRESOS POR MES — {$anio}"], $sep);
                fputcsv($handle, ['Mes','Total Ingresos (USD)','N° Pagos'], $sep);
                $im = array_column($d['ingresosMes'], null, 'mes');
                for ($m = 1; $m <= 12; $m++) {
                    $row = $im[$m] ?? null;
                    fputcsv($handle, [
                        $meses[$m],
                        $row ? number_format((float)$row['total_ingresos'], 2, '.', '') : '0.00',
                        $row ? (int)$row['total_pagos'] : 0,
                    ], $sep);
                }
                fputcsv($handle, ['TOTAL', number_format($d['totalIngresosAnio'], 2, '.', ''), ''], $sep);
                break;

            case 'saldos':
                fputcsv($handle, ['REPORTE DE SALDOS PENDIENTES'], $sep);
                fputcsv($handle, ['Cliente','Empresa','N° Cuentas','Total Deuda','Pagado','Pendiente','% Pagado'], $sep);
                foreach ($d['saldosPendientes'] as $s) {
                    $pct = $s['total_deuda'] > 0 ? round(($s['total_pagado']/$s['total_deuda'])*100).'%' : '0%';
                    fputcsv($handle, [
                        $s['cliente_nombre'], $s['empresa_nombre'] ?? '',
                        $s['total_cuentas'],
                        number_format($s['total_deuda'], 2, '.', ''),
                        number_format($s['total_pagado'], 2, '.', ''),
                        number_format($s['saldo_pendiente'], 2, '.', ''),
                        $pct,
                    ], $sep);
                }
                fputcsv($handle, ['TOTAL','','','','', number_format($d['totalPendiente'], 2, '.', ''),''], $sep);
                break;

            case 'suscripciones':
                fputcsv($handle, ["SUSCRIPCIONES POR VENCER — próximos {$dias} días"], $sep);
                fputcsv($handle, ['Cliente','Empresa','Plan','Precio/mes','Vencimiento','Días restantes'], $sep);
                foreach ($d['suscripcionesPorVencer'] as $s) {
                    $dr = (int)$s['dias_restantes'];
                    fputcsv($handle, [
                        $s['cliente_nombre'], $s['empresa_nombre'] ?? '',
                        $s['plan_nombre'],
                        number_format($s['plan_precio'], 2, '.', ''),
                        date('d/m/Y', strtotime($s['fecha_vencimiento'])),
                        $dr === 0 ? 'HOY' : $dr.' días',
                    ], $sep);
                }
                break;

            case 'tickets':
                $porEstado = []; $porPrioridad = [];
                foreach ($d['ticketsResumen'] as $t) {
                    $porEstado[$t['estado']]       = ($porEstado[$t['estado']] ?? 0) + $t['total'];
                    $porPrioridad[$t['prioridad']] = ($porPrioridad[$t['prioridad']] ?? 0) + $t['total'];
                }
                $total = array_sum($porEstado);
                fputcsv($handle, ['RESUMEN DE TICKETS'], $sep);
                fputcsv($handle, [''], $sep);
                fputcsv($handle, ['POR ESTADO'], $sep);
                fputcsv($handle, ['Estado','Total','%'], $sep);
                foreach (['abierto'=>'Abierto','en_proceso'=>'En proceso',
                          'esperando_cliente'=>'Esperando cliente','resuelto'=>'Resuelto','cerrado'=>'Cerrado'] as $k=>$l) {
                    $t = $porEstado[$k] ?? 0;
                    fputcsv($handle, [$l, $t, $total > 0 ? round(($t/$total)*100).'%' : '0%'], $sep);
                }
                fputcsv($handle, ['TOTAL', $total, '100%'], $sep);
                fputcsv($handle, [''], $sep);
                fputcsv($handle, ['POR PRIORIDAD'], $sep);
                fputcsv($handle, ['Prioridad','Total','%'], $sep);
                foreach (['critica'=>'Crítica','alta'=>'Alta','media'=>'Media','baja'=>'Baja'] as $k=>$l) {
                    $t = $porPrioridad[$k] ?? 0;
                    fputcsv($handle, [$l, $t, $total > 0 ? round(($t/$total)*100).'%' : '0%'], $sep);
                }
                break;

            case 'empleados':
                fputcsv($handle, ["EMPLEADOS MÁS ACTIVOS — últimos {$dias} días"], $sep);
                fputcsv($handle, ['#','Nombre','Email','Rol','N° Acciones','Última actividad'], $sep);
                foreach ($d['empleadosActivos'] as $i => $e) {
                    $ultima = $e['ultima_accion']
                        ? date('d/m/Y H:i', strtotime($e['ultima_accion'])) : 'Sin actividad';
                    fputcsv($handle, [
                        $i+1, $e['nombre'], $e['email'],
                        $e['rol_nombre'], $e['total_acciones'], $ultima,
                    ], $sep);
                }
                break;
        }

        fputcsv($handle, [''], $sep);
        fputcsv($handle, ['Generado por DeskCod el '.date('d/m/Y H:i')], $sep);
    }
}
?>