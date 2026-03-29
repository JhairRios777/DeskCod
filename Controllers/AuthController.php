<?php
namespace Controllers;

use Models\AuthModel;

class AuthController {

    private $model;

    public function __construct() {
        $this->model = new AuthModel();
    }

    public function login(): void {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responder(false, 'Método no permitido.');
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->responder(false, 'Usuario y contraseña son obligatorios.');
            return;
        }

        if (strlen($username) > 80 || strlen($password) > 100) {
            $this->responder(false, 'Datos inválidos.');
            return;
        }

        $empleado = $this->model->buscarPorUsername($username);


        // CORRECCIÓN: cast a int para manejar "1" string vs 1 entero
        if (
            !$empleado ||
            (int)$empleado['activo'] !== 1 ||
            !password_verify($password, $empleado['hash'])
        ) {
            $this->responder(false, 'Usuario o contraseña incorrectos.');
            return;
        }

        session_regenerate_id(true);

        if ($empleado['es_admin']) {
            $permisos = $this->todosLosPermisos();
        } else {
            $permisos = $this->model->obtenerPermisos($empleado['rol_id']);
        }

        $_SESSION['system'] = [
            'UserID'   => $empleado['id'],
            'UserName' => $empleado['nombre'],
            'Email'    => $empleado['email'],
            'RolID'    => $empleado['rol_id'],
            'EsAdmin'  => $empleado['es_admin'],
            'Permisos' => $permisos,
        ];

        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $this->model->registrarLogin($empleado['id'], $ip);

        $this->responder(true, 'Acceso correcto.', ['redirect' => '/Home']);
    }

    public function logout(): void {

        if (isset($_SESSION['system']['UserID'])) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $this->model->registrarLogout(
                $_SESSION['system']['UserID'],
                $ip
            );
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params["path"],   $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        header('Location: /Login');
        exit();
    }

    private function todosLosPermisos(): array {
        $modulos  = ['dashboard','clientes','suscripciones','tickets',
                     'empleados','planes','pagos','reportes'];
        $acciones = ['ver','crear','editar','eliminar','exportar'];

        $permisos = [];
        foreach ($modulos as $mod) {
            foreach ($acciones as $acc) {
                $permisos[$mod][$acc] = true;
            }
        }
        return $permisos;
    }

    private function responder(bool $success, string $message, array $extra = []): void {
        header('Content-Type: application/json');
        echo json_encode(array_merge(
            ['success' => $success, 'message' => $message],
            $extra
        ));
        exit();
    }
}
?>