<?php
session_start();

require_once "Define.php";
require_once "Config" . DIRECTORY_SEPARATOR . "JRequest.php";
require_once "Config" . DIRECTORY_SEPARATOR . "JRouter.php";
require_once "Config" . DIRECTORY_SEPARATOR . "AutoLoad.php";

Config\AutoLoad::run();

// ============================================
// Parsea la URL ANTES del control de sesión
// para permitir que Auth/login pase sin sesión
// ============================================
$request = new Config\JRequest();

// Rutas públicas — no requieren sesión activa
$rutasPublicas = ['Auth'];

$controllerActual = $request->getController();

if (!in_array($controllerActual, $rutasPublicas)) {
    // Cualquier otra ruta requiere sesión
    if (!isset($_SESSION["system"]["UserName"])) {
        header("Location: /Login");
        exit();
    }
}

// Carga el template solo si NO es una petición AJAX/API
// Auth/login responde JSON — no necesita el template
$esApiRequest = in_array($controllerActual, $rutasPublicas);

if (!$esApiRequest) {
    include_once "Template" . DIRECTORY_SEPARATOR . "index.php";
}

Config\JRouter::run($request);
?>