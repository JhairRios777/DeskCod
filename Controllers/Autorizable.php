<?php
namespace Controllers;

// ============================================
// Trait Autorizable
// Uso en cualquier controller:
//
//   use Controllers\Autorizable;
//   class MiController {
//       use Autorizable;
//       public function __construct() {
//           $this->requireLogin();
//           $this->requirePermiso('clientes', 'ver');
//       }
//   }
//
// Módulos disponibles (deben coincidir con tabla `modulos`):
//   clientes, suscripciones, tickets, empleados,
//   planes, pagos, reportes, perfil
//
// Acciones disponibles (deben coincidir con tabla `acciones`):
//   ver, crear, editar, eliminar
// ============================================

trait Autorizable {

    // ── Verifica que el usuario esté logueado ─
    protected function requireLogin(): void {
        if (empty($_SESSION['system']['UserID'])) {
            header('Location: /Login');
            exit();
        }
    }

    // ── Verifica permiso granular ─────────────
    // $modulo: nombre del módulo (ej: 'clientes')
    // $accion: nombre de la acción (ej: 'ver')
    // Si el usuario es admin, siempre tiene acceso
    protected function requirePermiso(string $modulo, string $accion): void {
        $this->requireLogin();

        // Admin tiene acceso total
        if (!empty($_SESSION['system']['EsAdmin'])) {
            return;
        }

        // Verifica en el cache de sesión primero
        $cacheKey = "perm_{$modulo}_{$accion}";
        if (isset($_SESSION['system']['permisos'][$cacheKey])) {
            if (!$_SESSION['system']['permisos'][$cacheKey]) {
                $this->denegarAcceso();
            }
            return;
        }

        // Consulta la BD
        $permitido = $this->consultarPermiso($modulo, $accion);
        $_SESSION['system']['permisos'][$cacheKey] = $permitido;

        if (!$permitido) {
            $this->denegarAcceso();
        }
    }

    // ── Solo admins ───────────────────────────
    protected function requireAdmin(): void {
        $this->requireLogin();
        if (empty($_SESSION['system']['EsAdmin'])) {
            $this->denegarAcceso();
        }
    }

    // ── Verifica sin redirigir (retorna bool) ─
    protected function tienePermiso(string $modulo, string $accion): bool {
        if (empty($_SESSION['system']['UserID'])) return false;
        if (!empty($_SESSION['system']['EsAdmin'])) return true;

        $cacheKey = "perm_{$modulo}_{$accion}";
        if (isset($_SESSION['system']['permisos'][$cacheKey])) {
            return (bool)$_SESSION['system']['permisos'][$cacheKey];
        }

        $resultado = $this->consultarPermiso($modulo, $accion);
        $_SESSION['system']['permisos'][$cacheKey] = $resultado;
        return $resultado;
    }

    // ── Consulta BD ───────────────────────────
    private function consultarPermiso(string $modulo, string $accion): bool {
        try {
            $rolId = (int)($_SESSION['system']['RolID'] ?? 0);
            if (!$rolId) return false;

            $db = (new \Config\Conexion())->getConexion();
            $stmt = $db->prepare("
                SELECT rp.permitido
                FROM rol_permisos rp
                INNER JOIN modulos  m ON m.id = rp.modulo_id
                INNER JOIN acciones a ON a.id = rp.accion_id
                WHERE rp.rol_id   = ?
                  AND m.nombre    = ?
                  AND a.nombre    = ?
                LIMIT 1
            ");
            $stmt->execute([$rolId, $modulo, $accion]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row ? (bool)$row['permitido'] : false;

        } catch (\PDOException $e) {
            error_log("[DeskCod] Autorizable::consultarPermiso: " . $e->getMessage());
            return false;
        }
    }

    // ── Respuesta de acceso denegado ──────────
    private function denegarAcceso(): void {
        // Si es una petición AJAX/JSON responde JSON
        $isJson = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
            || (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST');

        if ($isJson) {
            while (ob_get_level() > 0) ob_end_clean();
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.'
            ]);
            exit();
        }

        // Redirige con mensaje flash
        $_SESSION['flash_error'] = 'No tienes permisos para acceder a esta sección.';
        header('Location: /Home');
        exit();
    }
}
?>