<?php
session_start();

require_once "Define.php";
require_once "Config" . DIRECTORY_SEPARATOR . "JRequest.php";
require_once "Config" . DIRECTORY_SEPARATOR . "JRouter.php";
require_once "Config" . DIRECTORY_SEPARATOR . "AutoLoad.php";

Config\AutoLoad::run();

$request          = new Config\JRequest();
$rutasPublicas    = ['Auth'];
$controllerActual = $request->getController();

if (!in_array($controllerActual, $rutasPublicas)) {
    if (!isset($_SESSION["system"]["UserName"])) {
        header("Location: /Login");
        exit();
    }
}

$esRutaPublica = in_array($controllerActual, $rutasPublicas);

// POST sin template — permite redirects limpios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$esRutaPublica) {
    Config\JRouter::run($request);
    exit();
}

// GET — carga template normalmente
if (!$esRutaPublica) {
    include_once "Template" . DIRECTORY_SEPARATOR . "index.php";
}

Config\JRouter::run($request);
?>