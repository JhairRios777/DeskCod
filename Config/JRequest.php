<?php
namespace Config;

// ============================================
// JRequest.php — Parsea la URL en 3 partes:
// Controller, Method y Argument
// ============================================

class JRequest {

    private $Controller;
    private $Method;
    private $Argument;

    public function __construct() {
        if (isset($_GET["url"])) {

            // Sanitiza la URL eliminando caracteres peligrosos
            $Path = filter_input(INPUT_GET, "url", FILTER_SANITIZE_URL);
            $Path = explode("/", $Path);

            // array_filter elimina elementos vacíos
            // array_values REINICIA los índices — fix del bug $Path[0] undefined
            $Path = array_values(array_filter($Path));

            // Si la URL empieza con index.php carga el Home
            if (isset($Path[0]) && $Path[0] == "index.php") {
                $this->Controller = "Home";
            } else {
                // array_shift extrae y elimina el primer elemento
                // Primer elemento = nombre del Controller
                $this->Controller = array_shift($Path) ?? "Home";
            }

            // Segundo elemento = nombre del método a ejecutar
            $this->Method = array_shift($Path);
            if (!$this->Method) {
                $this->Method = "index";
            }

            // El resto son argumentos (ej: /Clientes/ver/15 → Argument = ["15"])
            $this->Argument = $Path ?: [""];

        } else {
            // Sin parámetro url → carga Home/index por defecto
            $this->Controller = "Home";
            $this->Method     = "index";
            $this->Argument   = [""];
        }
    }

    public function getController(): string {
        return $this->Controller;
    }

    public function getMethod(): string {
        return $this->Method;
    }

    public function getArgument(): array {
        return $this->Argument;
    }
}
?>