<?php
namespace Config;

// ============================================
// AutoLoad.php — Cargador automático de clases
// Registra un autoloader PSR-4 manual
// que convierte namespaces en rutas de archivo
// ============================================

class AutoLoad {

    public static function run(): void {

        spl_autoload_register(function(string $class) {

            // Convierte el namespace a ruta de archivo
            // ej: Controllers\HomeController
            //   → Controllers/HomeController.php
            $path     = str_replace('\\', '/', $class) . '.php';
            $pathFile = ROOT . $path;

            if (file_exists($pathFile)) {
                include_once $pathFile;
            } else {
                // Solo muestra el error en modo desarrollo
                // En producción falla silenciosamente y
                // JRouter mostrará el 404 correspondiente
                if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                    error_log("[DeskCod AutoLoad] Clase no encontrada: {$pathFile}");
                }
            }
        });
    }
}
?>