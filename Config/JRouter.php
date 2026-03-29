<?php
namespace Config;

// ============================================
// JRouter.php — Despacha el Controller y Vista
// correspondientes a la URL parseada por JRequest
// ============================================

class JRouter {

    public static function run(JRequest $request) {

        // ── SANITIZACIÓN CRÍTICA ──────────────────
        // Solo permite letras y números en el nombre del controller
        // Esto previene Path Traversal:
        // ej: /../Config/Conexion → queda vacío → 404
        $controllerName = preg_replace('/[^a-zA-Z0-9]/', '', $request->getController());

        if (empty($controllerName)) {
            self::error404();
            return;
        }

        $controllerName .= "Controller";
        $method          = $request->getMethod();
        $argument        = $request->getArgument();

        // ── SANITIZACIÓN DEL MÉTODO ──────────────
        // Solo permite letras, números y guiones para el método
        $method = preg_replace('/[^a-zA-Z0-9_-]/', '', $method);
        if (empty($method)) {
            $method = "index";
        }

        // Construye la ruta absoluta al archivo del controller
        $path = ROOT . "Controllers" . DS . $controllerName . ".php";

        if (is_readable($path)) {
            require $path;

            // Instancia el controller con su namespace completo
            $className  = "Controllers\\" . $controllerName;
            $controller = new $className();

            // Verifica que el método exista antes de llamarlo
            // Previene llamar métodos privados o inexistentes
            if (!method_exists($controller, $method)) {
                self::error404();
                return;
            }

            // Llama al método con o sin argumentos
            // call_user_func_array pasa el array de argumentos como parámetros individuales
            if ($argument && $argument !== [""]) {
                $JData = call_user_func_array([$controller, $method], $argument);
            } else {
                $JData = call_user_func([$controller, $method]);
            }

            // Extrae el array retornado por el controller
            // como variables disponibles en la vista
            // ej: return ['clientes' => [...]] → $clientes en la vista
            if (is_array($JData)) {
                extract($JData);
            }

        } else {
            self::error404();
            return;
        }

        // Carga la vista correspondiente al controller y método
        $viewPath = ROOT . "Views" . DS . $request->getController() . DS . $method . ".php";

        if (is_readable($viewPath)) {
            require $viewPath;
        }
    }

    // Muestra página 404 con código HTTP correcto
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