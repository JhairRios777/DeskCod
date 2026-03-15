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
            echo $this->body[1];
        }
    }
?>