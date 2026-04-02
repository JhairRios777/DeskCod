<?php
namespace Config;

// ============================================
// JRouter.php — Despacha el Controller y Vista
// correspondientes a la URL parseada por JRequest
// ============================================

class JRouter {

    // ============================================
    // Mapa de vistas personalizadas
    // Permite cargar vistas desde subcarpetas
    // cuando la convención estándar no aplica
    // Formato: 'Controller/Metodo' => 'Ruta/Relativa/Vista.php'
    // ============================================
    private static array $viewMap = [
        'Empleados/Roles'         => 'Empleados/Roles/index.php',
        'Empleados/RolesRegistry' => 'Empleados/Roles/Registry.php',
    ];

    public static function run(JRequest $request): void {

        // ── Sanitización del controller ──────────
        $controllerName = preg_replace('/[^a-zA-Z0-9]/', '', $request->getController());

        if (empty($controllerName)) {
            self::error404();
            return;
        }

        $controllerName .= "Controller";
        $method          = $request->getMethod();
        $argument        = $request->getArgument();

        // ── Sanitización del método ──────────────
        $method = preg_replace('/[^a-zA-Z0-9_-]/', '', $method);
        if (empty($method)) {
            $method = "index";
        }

        $path = ROOT . "Controllers" . DS . $controllerName . ".php";

        if (!is_readable($path)) {
            self::error404();
            return;
        }

        require $path;

        $className  = "Controllers\\" . $controllerName;
        $controller = new $className();

        if (!method_exists($controller, $method)) {
            self::error404();
            return;
        }

        // Llama al método con o sin argumentos
        if ($argument && $argument !== [""]) {
            $JData = call_user_func_array([$controller, $method], $argument);
        } else {
            $JData = call_user_func([$controller, $method]);
        }

        // Extrae variables para la vista
        if (is_array($JData)) {
            extract($JData);
        }

        // ============================================
        // Resuelve la ruta de la vista
        // 1. Busca en el mapa de rutas personalizadas
        // 2. Si no existe usa la convención estándar:
        //    Views/Controller/method.php
        // ============================================
        $mapKey = $request->getController() . '/' . $method;

        $viewPath = isset(self::$viewMap[$mapKey])
            ? ROOT . "Views" . DS . str_replace('/', DS, self::$viewMap[$mapKey])
            : ROOT . "Views" . DS . $request->getController() . DS . $method . ".php";

        if (is_readable($viewPath)) {
            require $viewPath;
        }
    }

    private static function error404(): void {
        http_response_code(404);
        $errorView = ROOT . "Views" . DS . "errors" . DS . "404.php";
        if (is_readable($errorView)) {
            require $errorView;
        } else {
            echo "<h1>404 — Página no encontrada</h1>";
        }
        exit();
    }
}
?>