<?php
namespace Models;

use Config\Conexion;

class AuthModel {

    private $db;

    public function __construct() {
        $this->db = (new Conexion())->getConexion();
    }

    public function buscarPorUsername(string $username): ?array {
        $stmt = $this->db->prepare("CALL sp_auth_login(?)");
        $stmt->execute([$username]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $row ?: null;
    }

    public function incrementarIntento(string $username): void {
        $stmt = $this->db->prepare("CALL sp_auth_incrementar_intento(?)");
        $stmt->execute([$username]);
        $stmt->closeCursor();
    }

    public function resetIntentos(string $username): void {
        $stmt = $this->db->prepare("CALL sp_auth_reset_intentos(?)");
        $stmt->execute([$username]);
        $stmt->closeCursor();
    }

    public function actualizarLogin(int $id): void {
        $stmt = $this->db->prepare("UPDATE empleados SET ultimo_login = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function obtenerPermisos(int $rolId): array {
        $stmt = $this->db->prepare("CALL sp_auth_permisos(?)");
        $stmt->execute([$rolId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $rows;
    }
}
?>