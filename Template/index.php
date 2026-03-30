<?php
$template = new Template();

class Template {
    private $body;

    function __construct() {
        ob_start();
        include ROOT."Template".DS."Default".DS."index.php";
        $file = ob_get_clean();
        $this->body = explode("{JBODY}", $file);
        echo $this->body[0];
    }

    function __destruct() {
        // ============================================
        // No imprime el footer si ya se envió JSON
        // Detecta por el Content-Type header enviado
        // o si headers_sent() con JSON en el buffer
        // Esto evita que el HTML del footer se mezcle
        // con la respuesta JSON del controller
        // ============================================
        $headers = headers_list();
        foreach ($headers as $header) {
            if (stripos($header, 'Content-Type: application/json') !== false) {
                return; // Ya se respondió JSON — no imprimir footer
            }
        }

        if (isset($this->body[1])) {
            echo $this->body[1];
        }
    }
}
?>