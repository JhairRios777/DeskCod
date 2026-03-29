<?php
namespace Config;

class Conexion {

    private $conn = null;

    public function __construct() {
        $host     = $_ENV['DB_HOST'] ?? 'localhost';
        $port     = $_ENV['DB_PORT'] ?? '3306';
        $db_name  = $_ENV['DB_NAME'] ?? 'deskcod';
        $user     = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASS'] ?? '';

        try {
            $this->conn = new \PDO(
                "mysql:host={$host};port={$port};dbname={$db_name};charset=utf8mb4",
                $user,
                $password,
                [
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES   => true,
                    \PDO::ATTR_STRINGIFY_FETCHES  => false,
                ]
            );
        } catch (\PDOException $e) {
            // Loguea el error real
            error_log("[DeskCod] Error de conexion BD: " . $e->getMessage());

            // Si es una petición AJAX responde JSON
            // Si es una petición normal muestra texto
            $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
                      str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') ||
                      str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/x-www-form-urlencoded');

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Error de conexión con la base de datos.'
                ]);
                exit();
            }

            die("Error de conexion. Por favor contacte al administrador.");
        }
    }

    public function getConexion(): \PDO {
        return $this->conn;
    }
}
?>