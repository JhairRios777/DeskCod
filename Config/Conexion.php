<?php
namespace config;

//$conn = new Conexion("ProyectoPHP");

class Conexion {
    private $host="localhost";
    private $db_name="proyectophp";
    private $user="root";
    private $password="";
    private $port="3307";
    private $conn=null;

    public function __construct() {
        try {
            $this->conn = new \PDO("mysql:host=".$this->host.";port=".$this->port.";dbname=".$this->db_name, $this->user, $this->password);
            //echo "Conexión Exitosa...";
        } catch (\Throwable $th) {
            die("Conexión Fallida... ".$th->getMessage());
        }
    }

    public function getConexion() {
        return $this->conn;
    }
}

?>