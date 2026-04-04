<?php
// ============================================
// APIR/Auth.php — Middleware de autenticación
// ============================================

class ApiAuth {

    private $db;

    public function __construct() {
        $this->db = (new Config\Conexion())->getConexion();
    }

    public function validar(): array {
        $token = $this->extraerToken();

        if (!$token) {
            $this->denegarAcceso('Token no proporcionado. Usa el header: Authorization: Bearer {token}');
        }

        try {
            $stmt = $this->db->prepare("CALL sp_api_validar_token(?)");
            $stmt->execute([$token]);
            $cliente = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        } catch (\PDOException $e) {
            error_log("[DeskCod API Auth] " . $e->getMessage());
            $this->denegarAcceso('Error al validar el token.');
        }

        if (!$cliente) {
            $this->denegarAcceso('Token inválido o inactivo.');
        }

        if (!$cliente['cliente_activo']) {
            $this->denegarAcceso('La cuenta del cliente está desactivada.');
        }

        $this->registrarLog(
            (int)$cliente['id'],
            $_SERVER['REQUEST_URI'] ?? '',
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            200
        );

        return $cliente;
    }

    // ── Extrae el token — compatible con XAMPP/Apache ──
    private function extraerToken(): ?string {
    // DEBUG TEMPORAL
    file_put_contents('C:/xampp/htdocs/DeskCod/api_debug.txt',
        print_r($_SERVER, true)
    );
        // 1. Fuente estándar
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $header = $_SERVER['HTTP_AUTHORIZATION'];
        }
        // 2. Apache mod_rewrite a veces usa REDIRECT_
        elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        // 3. apache_request_headers() — función de Apache
        elseif (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            // Case-insensitive search
            foreach ($headers as $key => $value) {
                if (strtolower($key) === 'authorization') {
                    $header = $value;
                    break;
                }
            }
        }
        // 4. getallheaders() — alternativa a apache_request_headers
        elseif (function_exists('getallheaders')) {
            $headers = getallheaders();
            foreach ($headers as $key => $value) {
                if (strtolower($key) === 'authorization') {
                    $header = $value;
                    break;
                }
            }
        }

        if (!$header) return null;

        // Elimina el prefijo "Bearer "
        if (stripos($header, 'Bearer ') === 0) {
            return trim(substr($header, 7));
        }

        return trim($header);
    }

    private function registrarLog(int $tokenId, string $endpoint, string $metodo, string $ip, int $status): void {
        try {
            $stmt = $this->db->prepare("CALL sp_api_log(?,?,?,?,?)");
            $stmt->execute([$tokenId, $endpoint, $metodo, $ip, $status]);
            $stmt->closeCursor();
        } catch (\PDOException $e) {
            error_log("[DeskCod API Log] " . $e->getMessage());
        }
    }

    private function denegarAcceso(string $mensaje): void {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => $mensaje, 'code' => 401]);
        exit();
    }
}
?>