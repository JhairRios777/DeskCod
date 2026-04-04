<?php
// ============================================
// APIR/Tickets.php — Endpoints de tickets
// Todos los métodos operan SOLO sobre los
// tickets del cliente autenticado
// ============================================

class ApiTickets {

    private $db;
    private array $cliente; // datos del cliente autenticado

    public function __construct(array $cliente) {
        $this->cliente = $cliente;
        $this->db      = (new Config\Conexion())->getConexion();
    }

    // ============================================
    // GET /API/tickets
    // Lista todos los tickets del cliente
    // Parámetros opcionales:
    //   ?estado=abierto|en_proceso|resuelto|cerrado
    //   ?prioridad=baja|media|alta|critica
    //   ?limit=20 (máx 100)
    // ============================================
    public function listar(): void {
        $clienteId = (int)$this->cliente['cliente_id'];
        $estado    = $_GET['estado']    ?? null;
        $prioridad = $_GET['prioridad'] ?? null;
        $limit     = min((int)($_GET['limit'] ?? 20), 100);

        try {
            $stmt = $this->db->prepare("CALL sp_api_tickets_listar(?)");
            $stmt->execute([$clienteId]);
            $tickets = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            // Filtros opcionales en PHP (evita SPs adicionales)
            if ($estado) {
                $tickets = array_filter($tickets, fn($t) => $t['estado'] === $estado);
            }
            if ($prioridad) {
                $tickets = array_filter($tickets, fn($t) => $t['prioridad'] === $prioridad);
            }

            $tickets = array_slice(array_values($tickets), 0, $limit);

            echo json_encode([
                'success' => true,
                'total'   => count($tickets),
                'tickets' => $tickets,
            ]);

        } catch (\PDOException $e) {
            error_log("[DeskCod API Tickets::listar] " . $e->getMessage());
            responderError(500, 'Error al obtener los tickets.');
        }
    }

    // ============================================
    // POST /API/tickets
    // Crea un ticket nuevo
    // Body (JSON o form-data):
    //   titulo*       string  max 200
    //   descripcion*  string
    //   tipo          error|modificacion|nueva_funcion|consulta (default: consulta)
    //   prioridad     baja|media|alta|critica (default: media)
    //   suscripcion_id int (opcional)
    // ============================================
    public function crear(): void {
        $clienteId = (int)$this->cliente['cliente_id'];
        $body      = $this->obtenerBody();

        // ── Validación ──
        $titulo      = trim($body['titulo']      ?? '');
        $descripcion = trim($body['descripcion'] ?? '');

        if (empty($titulo)) {
            responderError(422, 'El campo titulo es obligatorio.');
        }
        if (empty($descripcion)) {
            responderError(422, 'El campo descripcion es obligatorio.');
        }
        if (strlen($titulo) > 200) {
            responderError(422, 'El titulo no puede superar 200 caracteres.');
        }

        $tiposValidos     = ['error','modificacion','nueva_funcion','consulta'];
        $prioridadesValidas = ['baja','media','alta','critica'];

        $tipo          = in_array($body['tipo'] ?? '', $tiposValidos)
            ? $body['tipo'] : 'consulta';
        $prioridad     = in_array($body['prioridad'] ?? '', $prioridadesValidas)
            ? $body['prioridad'] : 'media';
        $suscripcionId = !empty($body['suscripcion_id'])
            ? (int)$body['suscripcion_id'] : null;

        // Verifica que la suscripcion_id pertenece al cliente
        if ($suscripcionId) {
            $stmt = $this->db->prepare("
                SELECT id FROM suscripciones
                WHERE id = ? AND cliente_id = ?
                LIMIT 1
            ");
            $stmt->execute([$suscripcionId, $clienteId]);
            if (!$stmt->fetch()) {
                responderError(403, 'La suscripcion_id no pertenece a este cliente.');
            }
            $stmt->closeCursor();
        }

        try {
            $stmt = $this->db->prepare("CALL sp_api_tickets_crear(?,?,?,?,?,?)");
            $stmt->execute([
                $clienteId,
                $suscripcionId,
                $titulo,
                $descripcion,
                $tipo,
                $prioridad,
            ]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $ticketId = (int)($result['id'] ?? 0);

            http_response_code(201);
            echo json_encode([
                'success'   => true,
                'message'   => 'Ticket creado correctamente.',
                'ticket_id' => $ticketId,
                'ticket'    => [
                    'id'          => $ticketId,
                    'titulo'      => $titulo,
                    'tipo'        => $tipo,
                    'prioridad'   => $prioridad,
                    'estado'      => 'abierto',
                    'created_at'  => date('Y-m-d H:i:s'),
                ],
            ]);

        } catch (\PDOException $e) {
            error_log("[DeskCod API Tickets::crear] " . $e->getMessage());
            responderError(500, 'Error al crear el ticket.');
        }
    }

    // ============================================
    // GET /API/tickets/{id}
    // Detalle de un ticket
    // Solo retorna tickets del cliente autenticado
    // ============================================
    public function detalle(int $id): void {
        $clienteId = (int)$this->cliente['cliente_id'];

        try {
            $stmt = $this->db->prepare("CALL sp_api_tickets_detalle(?,?)");
            $stmt->execute([$id, $clienteId]);
            $ticket = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if (!$ticket) {
                responderError(404, 'Ticket no encontrado.');
            }

            echo json_encode([
                'success' => true,
                'ticket'  => $ticket,
            ]);

        } catch (\PDOException $e) {
            error_log("[DeskCod API Tickets::detalle] " . $e->getMessage());
            responderError(500, 'Error al obtener el ticket.');
        }
    }

    // ============================================
    // GET /API/tickets/{id}/comentarios
    // Lista los comentarios públicos del ticket
    // No retorna notas internas (solo_interna)
    // ============================================
    public function comentarios(int $id): void {
        $clienteId = (int)$this->cliente['cliente_id'];

        try {
            $stmt = $this->db->prepare("CALL sp_api_comentarios_listar(?,?)");
            $stmt->execute([$id, $clienteId]);
            $comentarios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            echo json_encode([
                'success'     => true,
                'ticket_id'   => $id,
                'total'       => count($comentarios),
                'comentarios' => $comentarios,
            ]);

        } catch (\PDOException $e) {
            error_log("[DeskCod API Tickets::comentarios] " . $e->getMessage());
            responderError(500, 'Error al obtener los comentarios.');
        }
    }

    // ============================================
    // POST /API/tickets/{id}/comentar
    // Agrega un comentario al ticket
    // Body:
    //   contenido* string
    // ============================================
    public function comentar(int $id): void {
        $clienteId = (int)$this->cliente['cliente_id'];
        $body      = $this->obtenerBody();
        $contenido = trim($body['contenido'] ?? '');

        if (empty($contenido)) {
            responderError(422, 'El campo contenido es obligatorio.');
        }

        // Verifica que el ticket pertenece al cliente
        try {
            $stmt = $this->db->prepare("
                SELECT id FROM tickets
                WHERE id = ? AND cliente_id = ?
                LIMIT 1
            ");
            $stmt->execute([$id, $clienteId]);
            if (!$stmt->fetch()) {
                responderError(404, 'Ticket no encontrado.');
            }
            $stmt->closeCursor();

            // Busca el empleado_id del ticket para asignarlo al comentario
            // Si no hay empleado, usa el primer empleado activo
            $stmt = $this->db->prepare("
                SELECT COALESCE(empleado_id, (
                    SELECT id FROM empleados WHERE activo = 1 LIMIT 1
                )) AS emp_id
                FROM tickets WHERE id = ? LIMIT 1
            ");
            $stmt->execute([$id]);
            $row      = $stmt->fetch(\PDO::FETCH_ASSOC);
            $empId    = (int)($row['emp_id'] ?? 1);
            $stmt->closeCursor();

            // Inserta el comentario como tipo 'respuesta' (visible al cliente)
            $stmt = $this->db->prepare("CALL sp_ticket_comentarios_agregar(?,?,?,?)");
            $stmt->execute([$id, $empId, 'respuesta', $contenido]);
            $result      = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $comentarioId = (int)($result['id'] ?? 0);

            http_response_code(201);
            echo json_encode([
                'success'      => true,
                'message'      => 'Comentario agregado correctamente.',
                'comentario_id'=> $comentarioId,
                'ticket_id'    => $id,
                'contenido'    => $contenido,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);

        } catch (\PDOException $e) {
            error_log("[DeskCod API Tickets::comentar] " . $e->getMessage());
            responderError(500, 'Error al agregar el comentario.');
        }
    }

    // ── Helper — lee el body JSON o form-data ──
    private function obtenerBody(): array {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        // Si viene como JSON
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            return json_decode($raw, true) ?? [];
        }

        // Si viene como form-data o x-www-form-urlencoded
        return $_POST;
    }
}
?>