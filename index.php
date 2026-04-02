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
$methodActual     = $request->getMethod();

if (!in_array($controllerActual, $rutasPublicas)) {
    if (!isset($_SESSION["system"]["UserName"])) {
        header("Location: /Login");
        exit();
    }
}

// Métodos que siempre responden JSON — sin template
$metodosJson = [
    'suscripciones', 'suspender', 'reactivar', 'cambiarPlan',
    'desactivar', 'cambiarEstado', 'asignar', 'comentar',
];

$esRutaPublica = in_array($controllerActual, $rutasPublicas);
$esPost        = $_SERVER['REQUEST_METHOD'] === 'POST';
$esMetodoJson  = in_array($methodActual, $metodosJson);

if (!$esRutaPublica && ($esPost || $esMetodoJson)) {
    Config\JRouter::run($request);
    exit();
}

if (!$esRutaPublica) {
    include_once "Template" . DIRECTORY_SEPARATOR . "index.php";
}

Config\JRouter::run($request);
?>