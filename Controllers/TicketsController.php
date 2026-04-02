<?php
namespace Controllers;

use Models\TicketsModel;

class TicketsController {

    private $model;

    public function __construct() {
        $this->model = new TicketsModel();
    }

        // ============================================
        // GET /Tickets/suscripciones?cliente_id=1
        // Retorna suscripciones activas del cliente — JSON
        // ============================================
        public function suscripciones(): void {
            while (ob_get_level() > 0) ob_end_clean();
            header('Content-Type: application/json');

            $clienteId = (int)($_GET['cliente_id'] ?? 0);
            if (!$clienteId) {
                echo json_encode([]);
                exit();
            }

            $db   = (new \Config\Conexion())->getConexion();
            $stmt = $db->prepare("
                SELECT s.id, s.estado, s.fecha_vencimiento,
                    p.nombre AS plan_nombre
                FROM suscripciones s
                INNER JOIN planes p ON p.id = s.plan_id
                WHERE s.cliente_id = ?
                AND s.estado IN ('activa','por_vencer')
                ORDER BY s.created_at DESC
            ");
            $stmt->execute([$clienteId]);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            echo json_encode($rows);
            exit();
}


    // ============================================
    // GET /Tickets — lista de tickets
    // ============================================
    public function index(): array {
        $tickets       = $this->model->obtenerTodos();
        $flash_success = $_SESSION['flash_success'] ?? null;
        $flash_error   = $_SESSION['flash_error']   ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
        return compact('tickets', 'flash_success', 'flash_error');
    }

    // ============================================
    // GET  /Tickets/Registry   → crear
    // POST /Tickets/Registry   → guardar
    // ============================================
    public function Registry(): array {
        $clientes  = $this->model->obtenerClientes();
        $empleados = $this->model->obtenerEmpleados();
        

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Registrar'])) {
            $datos   = $this->sanitizar();
            $errores = $this->validar($datos);

            if (!empty($errores)) {
                $_SESSION['flash_error'] = implode(' ', $errores);
                header('Location: /Tickets/Registry');
                exit();
            }
                
            try {
                $nuevoId = $this->model->crear($datos);
                $this->auditar('TICKET_CREADO', $nuevoId);
                $_SESSION['flash_success'] = 'Ticket creado correctamente.';
                header("Location: /Tickets/ver/{$nuevoId}");
                exit();
            } catch (\PDOException $e) {
                error_log("[DeskCod] Ticket crear: " . $e->getMessage());
                $_SESSION['flash_error'] = 'Error al crear el ticket.';
                header('Location: /Tickets/Registry');
                exit();
            }
        }

        $error   = $_SESSION['flash_error']   ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        return compact('clientes', 'empleados', 'error', 'success');
    }

    // ============================================
    // GET /Tickets/ver/1 — detalle del ticket
    // ============================================
    public function ver(int $id = 0): array {
        if (!$id) {
            header('Location: /Tickets');
            exit();
        }

        $ticket     = $this->model->obtenerPorId($id);
        if (!$ticket) {
            header('Location: /Tickets');
            exit();
        }

        $comentarios = $this->model->obtenerComentarios($id);
        $historial   = $this->model->obtenerHistorial($id);
        $empleados   = $this->model->obtenerEmpleados();

        $flash_success = $_SESSION['flash_success'] ?? null;
        $flash_error   = $_SESSION['flash_error']   ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return compact('ticket', 'comentarios', 'historial', 'empleados', 'flash_success', 'flash_error');
    }

    // ============================================
    // POST /Tickets/cambiarEstado — JSON
    // ============================================
    public function cambiarEstado(): void {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        $id         = (int)($_POST['id']     ?? 0);
        $estado     = trim($_POST['estado']  ?? '');
        $empleadoId = (int)($_SESSION['system']['UserID'] ?? 0);

        $estadosValidos = ['abierto','en_proceso','esperando_cliente','resuelto','cerrado'];
        if (!$id || !in_array($estado, $estadosValidos)) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
            exit();
        }

        try {
            $this->model->cambiarEstado($id, $estado, $empleadoId);
            $this->auditar('TICKET_ESTADO_CAMBIADO', $id);
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente.', 'estado' => $estado]);
        } catch (\PDOException $e) {
            error_log("[DeskCod] CambiarEstado ticket: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado.']);
        }
        exit();
    }

    // ============================================
    // POST /Tickets/asignar — JSON
    // ============================================
    public function asignar(): void {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        $ticketId   = (int)($_POST['ticket_id']   ?? 0);
        $empleadoId = (int)($_POST['empleado_id'] ?? 0);

        if (!$ticketId || !$empleadoId) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
            exit();
        }

        try {
            $this->model->asignar($ticketId, $empleadoId);
            $this->auditar('TICKET_ASIGNADO', $ticketId);
            echo json_encode(['success' => true, 'message' => 'Ticket asignado correctamente.']);
        } catch (\PDOException $e) {
            error_log("[DeskCod] Asignar ticket: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al asignar el ticket.']);
        }
        exit();
    }

    // ============================================
    // POST /Tickets/comentar — JSON
    // ============================================
    public function comentar(): void {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        $ticketId   = (int)($_POST['ticket_id']  ?? 0);
        $tipo       = trim($_POST['tipo']         ?? 'respuesta');
        $contenido  = trim($_POST['contenido']    ?? '');
        $empleadoId = (int)($_SESSION['system']['UserID'] ?? 0);

        if (!$ticketId || empty($contenido)) {
            echo json_encode(['success' => false, 'message' => 'El comentario no puede estar vacío.']);
            exit();
        }

        $tiposValidos = ['respuesta', 'nota_interna'];
        if (!in_array($tipo, $tiposValidos)) $tipo = 'respuesta';

        try {
            $nuevoId = $this->model->agregarComentario($ticketId, $empleadoId, $tipo, $contenido);
            $this->auditar('TICKET_COMENTARIO', $ticketId);
            echo json_encode([
                'success'    => true,
                'message'    => 'Comentario agregado.',
                'id'         => $nuevoId,
                'empleado'   => $_SESSION['system']['UserName'] ?? '',
                'tipo'       => $tipo,
                'contenido'  => htmlspecialchars($contenido),
                'fecha'      => date('d/m/Y H:i'),
            ]);
        } catch (\PDOException $e) {
            error_log("[DeskCod] Comentar ticket: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al agregar el comentario.']);
        }
        exit();
    }

    // ── Helpers ───────────────────────────────

   private function sanitizar(): array {
    return [
        'cliente_id'     => (int)($_POST['cliente_id']     ?? 0),
        'suscripcion_id' => (int)($_POST['suscripcion_id'] ?? 0), // ← nuevo
        'empleado_id'    => (int)($_POST['empleado_id']    ?? $_SESSION['system']['UserID'] ?? 0),
        'titulo'         => trim(htmlspecialchars($_POST['titulo']      ?? '')),
        'descripcion'    => trim($_POST['descripcion']                  ?? ''),
        'tipo'           => trim($_POST['tipo']                         ?? 'consulta'),
        'prioridad'      => trim($_POST['prioridad']                    ?? 'media'),
        'categoria'      => trim(htmlspecialchars($_POST['categoria']   ?? '')),
        'fecha_limite'   => trim($_POST['fecha_limite']                 ?? ''),
    ];
}

    private function validar(array $d): array {
    $e = [];
    if (empty($d['cliente_id']))     $e[] = 'Selecciona un cliente.';
    if (empty($d['suscripcion_id'])) $e[] = 'Selecciona una suscripción.'; // ← nuevo
    if (empty($d['titulo']))         $e[] = 'El título es obligatorio.';
    if (empty($d['descripcion']))    $e[] = 'La descripción es obligatoria.';
    return $e;
}

    private function auditar(string $accion, int $registroId): void {
        try {
            $db = (new \Config\Conexion())->getConexion();
            $db->prepare("INSERT INTO auditoria_acciones (empleado_id,accion,tabla,registro_id,ip) VALUES (?,?,?,?,?)")
               ->execute([
                   $_SESSION['system']['UserID'] ?? 1,
                   $accion,
                   'tickets',
                   $registroId,
                   $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
               ]);
        } catch (\PDOException $e) {
            error_log("[DeskCod] Auditoría: " . $e->getMessage());
        }
    }
}
?>