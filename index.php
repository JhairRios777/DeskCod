<?php
    session_start();

    require_once "Define.php";
    require_once "Config\JRequest.php";
    require_once "Config\JRouter.php";
    require_once "Config\AutoLoad.php";
    
    /*Control de sesión para evitar acceso a la página principal sin iniciar sesión*/
   /* if(!isset($_SESSION["system"]["UserName"])){
        header("Location: /Login");
        exit();
    }*/

    Config\AutoLoad::run();
    include_once "Template\index.php";
    Config\JRouter::run(new Config\JRequest());
?>