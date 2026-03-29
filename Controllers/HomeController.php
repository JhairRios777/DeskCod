<?php
namespace Controllers;

use Config\Conexion;

// ============================================
// HomeController.php — Dashboard principal
// Conecta con sp_dashboard_metricas para
// mostrar KPIs reales desde la BD
// ============================================

class HomeController {

    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    // ============================================
    // index()
    // Carga las métricas del dashboard desde el SP
    // Retorna array que JRouter extrae como
    // variables disponibles en la vista
    // ============================================
    public function index(): array {

        try {
            // Llama al SP que calcula todas las métricas
            // en una sola consulta optimizada
            $stmt = $this->db->query("CALL sp_dashboard_metricas()");
            $metricas = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();

        } catch (\PDOException $e) {
            error_log("[DeskCod] Error dashboard: " . $e->getMessage());
            // Si falla la BD devuelve ceros — no rompe la vista
            $metricas = [
                'total_clientes'          => 0,
                'suscripciones_activas'   => 0,
                'suscripciones_por_vencer'=> 0,
                'tickets_abiertos'        => 0,
                'ingresos_mes'            => 0,
            ];
        }

        // El array retornado es extraído por JRouter con extract()
        // Cada clave se convierte en variable PHP en la vista:
        // $metricas → disponible en Views/Home/index.php
        return [
            'metricas' => $metricas,
            'userName' => $_SESSION['system']['UserName'] ?? 'Usuario',
            'esAdmin'  => $_SESSION['system']['EsAdmin']  ?? 0,
        ];
    }
}
?>