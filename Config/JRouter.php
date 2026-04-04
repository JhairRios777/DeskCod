<?php
namespace Config;

class JRouter {

    private static array $viewMap = [
        'Empleados/Roles'         => 'Empleados/Roles/index.php',
        'Empleados/RolesRegistry' => 'Empleados/Roles/Registry.php',
        'Tickets/ver'             => 'Tickets/ver.php',
        'Pagos/Cuenta'            => 'Pagos/Cuenta.php',
        'Perfil/index'            => 'Perfil/index.php',
    ];

    public static function run(JRequest $request): void {

        $controllerName = preg_replace('/[^a-zA-Z0-9]/', '', $request->getController());

        if (empty($controllerName)) {
            self::error404();
            return;
        }

        $controllerName .= "Controller";
        $method          = $request->getMethod();
        $argument        = $request->getArgument();
error_log("[JRouter] controller=$controllerName method=$method argument=" . json_encode($argument));
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

        if ($argument && $argument !== [""]) {
            $JData = call_user_func_array([$controller, $method], $argument);
        } else {
            $JData = call_user_func([$controller, $method]);
        }

        if (is_array($JData)) {
            extract($JData);
        }

        $mapKey = $request->getController() . '/' . $method;

        $viewPath = isset(self::$viewMap[$mapKey])
            ? ROOT . "Views" . DS . str_replace('/', DS, self::$viewMap[$mapKey])
            : ROOT . "Views" . DS . $request->getController() . DS . $method . ".php";
error_log("[JRouter] viewPath: " . $viewPath);
error_log("[JRouter] mapKey: " . $mapKey);
error_log("[JRouter] method: " . $method);
        if (is_readable($viewPath)) {
            // DEBUG TEMPORAL

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