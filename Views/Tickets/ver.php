<!-- Views/Tickets/ver.php -->

<style>
.input-group-text { background:#005C3E; color:#fff; border-color:#005C3E; }
body.dark-mode .input-group-text { background:#1a3329; border-color:#1e3329; color:#e0e0e0; }
body.dark-mode .form-control, body.dark-mode .form-select {
    background:#111f18; border-color:#1e3329; color:#e0e0e0;
}
body.dark-mode .form-control:focus {
    background:#0f1a15; border-color:#00E676; box-shadow:0 0 0 3px rgba(0,230,118,0.1);
}
body.dark-mode .form-label { color:#c8e6d5; }

/* Comentarios */
.comentario {
    border-radius:10px; padding:1rem;
    margin-bottom:0.75rem;
    border-left:4px solid #005C3E;
    background: rgba(0,92,62,0.05);
}
.comentario.nota-interna {
    border-left-color: #ffc107;
    background: rgba(255,193,7,0.06);
}
body.dark-mode .comentario { background: rgba(0,92,62,0.1); }
body.dark-mode .comentario.nota-interna { background: rgba(255,193,7,0.08); }

/* Historial */
.historial-item {
    padding:0.5rem 0;
    border-bottom:1px solid var(--border-color);
    font-size:0.85rem;
}
.historial-item:last-child { border-bottom:none; }

/* Badge prioridad */
.badge-critica  { background:#dc3545; color:#fff; padding:0.3em 0.75em; border-radius:20px; font-size:0.78rem; }
.badge-alta     { background:#fd7e14; color:#fff; padding:0.3em 0.75em; border-radius:20px; font-size:0.78rem; }
.badge-media    { background:#ffc107; color:#000; padding:0.3em 0.75em; border-radius:20px; font-size:0.78rem; }
.badge-baja     { background:#6c757d; color:#fff; padding:0.3em 0.75em; border-radius:20px; font-size:0.78rem; }
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-ticket-alt me-2 text-success"></i>
            Ticket #<?= str_pad($ticket['id'],4,'0',STR_PAD_LEFT) ?>
        </h4>
        <small class="text-muted"><?= htmlspecialchars($ticket['titulo']) ?></small>
    </div>
    <a href="/Tickets" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Regresar
    </a>
</div>

<?php if (!empty($flash_success)): ?>
<div class="alert alert-success d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-check-circle fa-lg"></i>
    <span><?= htmlspecialchars($flash_success) ?></span>
</div>
<?php endif; ?>

<?php if (!empty($flash_error)): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-times-circle fa-lg"></i>
    <span><?= htmlspecialchars($flash_error) ?></span>
</div>
<?php endif; ?>

<div class="row g-4">

    <!-- Columna principal -->
    <div class="col-lg-8">

        <!-- Descripción -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary d-flex align-items-center justify-content-between">
                <span><i class="fas fa-file-alt me-2"></i>Descripción</span>
                <?php
                $prioLabels = [
                    'critica' => 'Crítica', 'alta' => 'Alta',
                    'media'   => 'Media',   'baja' => 'Baja',
                ];
                ?>
                <span class="badge-<?= $ticket['prioridad'] ?>">
                    <?= $prioLabels[$ticket['prioridad']] ?? $ticket['prioridad'] ?>
                </span>
            </div>
            <div class="card-body">
                <p style="white-space:pre-wrap;"><?= htmlspecialchars($ticket['descripcion']) ?></p>
            </div>
        </div>

        <!-- Comentarios -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary">
                <i class="fas fa-comments me-2"></i>Comentarios
                <span class="badge bg-light text-dark ms-2"><?= count($comentarios) ?></span>
            </div>
            <div class="card-body" id="listaComentarios">
                <?php if (empty($comentarios)): ?>
                <p class="text-muted text-center py-3">Sin comentarios aún.</p>
                <?php else: ?>
                <?php foreach ($comentarios as $c): ?>
                <div class="comentario <?= $c['tipo'] === 'nota_interna' ? 'nota-interna' : '' ?>">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold">
                            <i class="fas fa-user me-1"></i>
                            <?= htmlspecialchars($c['empleado_nombre'] ?? 'Sistema') ?>
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <?php if ($c['tipo'] === 'nota_interna'): ?>
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-lock me-1"></i>Nota interna
                            </span>
                            <?php endif; ?>
                            <small class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                    <p class="mb-0" style="white-space:pre-wrap;"><?= htmlspecialchars($c['contenido']) ?></p>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Agregar comentario -->
            <?php if (!in_array($ticket['estado'], ['cerrado'])): ?>
            <div class="card-footer">
                <div class="mb-2 d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary active" id="btnRespuesta"
                            onclick="setTipo('respuesta')">
                        <i class="fas fa-reply me-1"></i>Respuesta
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning" id="btnNota"
                            onclick="setTipo('nota_interna')">
                        <i class="fas fa-lock me-1"></i>Nota interna
                    </button>
                </div>
                <input type="hidden" id="tipoComentario" value="respuesta">
                <textarea class="form-control mb-2" id="textoComentario" rows="3"
                          placeholder="Escribe tu comentario..."></textarea>
                <button class="btn btn-primary btn-sm" onclick="enviarComentario()">
                    <i class="fas fa-paper-plane me-1"></i>Enviar
                </button>
            </div>
            <?php endif; ?>
        </div>

        <!-- Historial -->
        <?php if (!empty($historial)): ?>
        <div class="card shadow-sm">
            <div class="card-header bg-primary">
                <i class="fas fa-history me-2"></i>Historial de cambios
            </div>
            <div class="card-body">
                <?php foreach ($historial as $h): ?>
                <div class="historial-item">
                    <span class="text-muted"><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></span>
                    — <strong><?= htmlspecialchars($h['empleado_nombre'] ?? 'Sistema') ?></strong>
                    cambió <strong><?= htmlspecialchars($h['campo_modificado']) ?></strong>
                    de <span class="badge bg-secondary"><?= htmlspecialchars($h['valor_anterior'] ?? '—') ?></span>
                    a <span class="badge bg-primary"><?= htmlspecialchars($h['valor_nuevo']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- Columna lateral -->
    <div class="col-lg-4">

        <!-- Info del ticket -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary">
                <i class="fas fa-info-circle me-2"></i>Información
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted fw-semibold">Estado</td>
                        <td>
                            <?php
                            $estadoLabels = [
                                'abierto'           => ['label'=>'Abierto',           'class'=>'badge-activa'],
                                'en_proceso'        => ['label'=>'En proceso',        'class'=>'badge-por-vencer'],
                                'esperando_cliente' => ['label'=>'Esperando cliente', 'class'=>'badge-suspendida'],
                                'resuelto'          => ['label'=>'Resuelto',          'class'=>'badge-activa'],
                                'cerrado'           => ['label'=>'Cerrado',           'class'=>'badge-vencida'],
                            ];
                            $el = $estadoLabels[$ticket['estado']] ?? ['label'=>$ticket['estado'],'class'=>''];
                            echo "<span class='{$el['class']}'>{$el['label']}</span>";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Cliente</td>
                        <td><?= htmlspecialchars($ticket['cliente_nombre']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Tipo</td>
                        <td><?= htmlspecialchars($ticket['tipo']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Categoría</td>
                        <td><?= htmlspecialchars($ticket['categoria'] ?? '—') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Asignado</td>
                        <td><?= htmlspecialchars($ticket['empleado_nombre'] ?? 'Sin asignar') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Creado</td>
                        <td><small><?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></small></td>
                    </tr>
                    <?php if ($ticket['fecha_limite']): ?>
                    <tr>
                        <td class="text-muted fw-semibold">Límite</td>
                        <td><small class="text-warning"><?= date('d/m/Y H:i', strtotime($ticket['fecha_limite'])) ?></small></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($ticket['fecha_resolucion']): ?>
                    <tr>
                        <td class="text-muted fw-semibold">Resuelto</td>
                        <td><small class="text-success"><?= date('d/m/Y H:i', strtotime($ticket['fecha_resolucion'])) ?></small></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Cambiar estado -->
        <?php if ($ticket['estado'] !== 'cerrado'): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary">
                <i class="fas fa-exchange-alt me-2"></i>Cambiar Estado
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <?php
                $transiciones = [
                    'abierto'           => ['en_proceso' => 'Tomar ticket', 'cerrado' => 'Cerrar'],
                    'en_proceso'        => ['esperando_cliente' => 'Esperando cliente', 'resuelto' => 'Marcar resuelto', 'cerrado' => 'Cerrar'],
                    'esperando_cliente' => ['en_proceso' => 'Retomar', 'resuelto' => 'Marcar resuelto'],
                    'resuelto'          => ['cerrado' => 'Cerrar ticket'],
                ];
                $btns = $transiciones[$ticket['estado']] ?? [];
                $btnClases = [
                    'en_proceso'        => 'btn-warning text-dark',
                    'esperando_cliente' => 'btn-secondary',
                    'resuelto'          => 'btn-success',
                    'cerrado'           => 'btn-danger',
                ];
                foreach ($btns as $estado => $label):
                ?>
                <button class="btn <?= $btnClases[$estado] ?? 'btn-primary' ?> btn-sm"
                        onclick="cambiarEstado(<?= $ticket['id'] ?>, '<?= $estado ?>')">
                    <?= htmlspecialchars($label) ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Asignar -->
        <?php if ($ticket['estado'] !== 'cerrado'): ?>
        <div class="card shadow-sm">
            <div class="card-header bg-primary">
                <i class="fas fa-user-tie me-2"></i>Asignar
            </div>
            <div class="card-body">
                <select class="form-select mb-2" id="selectAsignar">
                    <option value="">Selecciona un empleado</option>
                    <?php foreach ($empleados as $emp): ?>
                    <option value="<?= $emp['id'] ?>"
                        <?= $ticket['empleado_id'] == $emp['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($emp['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-primary btn-sm w-100" onclick="asignar(<?= $ticket['id'] ?>)">
                    <i class="fas fa-user-check me-1"></i>Asignar
                </button>
            </div>
        </div>
        <?php endif; ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const ticketId = <?= $ticket['id'] ?>;

function setTipo(tipo) {
    document.getElementById('tipoComentario').value = tipo;
    document.getElementById('btnRespuesta').classList.toggle('active', tipo === 'respuesta');
    document.getElementById('btnNota').classList.toggle('active', tipo === 'nota_interna');
}

function enviarComentario() {
    const contenido = document.getElementById('textoComentario').value.trim();
    const tipo      = document.getElementById('tipoComentario').value;

    if (!contenido) {
        Swal.fire({ icon:'warning', title:'Campo vacío',
            text:'Escribe un comentario antes de enviar.', confirmButtonColor:'#005C3E' });
        return;
    }

    const fd = new FormData();
    fd.append('ticket_id', ticketId);
    fd.append('tipo', tipo);
    fd.append('contenido', contenido);

    fetch('/Tickets/comentar', { method:'POST', body:fd, credentials:'same-origin' })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('textoComentario').value = '';
                const div = document.createElement('div');
                div.className = 'comentario' + (tipo === 'nota_interna' ? ' nota-interna' : '');
                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold"><i class="fas fa-user me-1"></i>${data.empleado}</span>
                        <div class="d-flex align-items-center gap-2">
                            ${tipo === 'nota_interna' ? '<span class="badge bg-warning text-dark"><i class="fas fa-lock me-1"></i>Nota interna</span>' : ''}
                            <small class="text-muted">${data.fecha}</small>
                        </div>
                    </div>
                    <p class="mb-0">${data.contenido}</p>`;
                document.getElementById('listaComentarios').appendChild(div);
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        });
}

function cambiarEstado(id, estado) {
    const etiquetas = {
        en_proceso:'tomar este ticket',
        esperando_cliente:'marcarlo como esperando cliente',
        resuelto:'marcarlo como resuelto',
        cerrado:'cerrar este ticket'
    };
    Swal.fire({
        title:'¿Confirmar cambio?',
        text:`Vas a ${etiquetas[estado] || 'cambiar el estado'}.`,
        icon:'question',
        showCancelButton:true,
        confirmButtonColor:'#005C3E',
        cancelButtonColor:'#6c757d',
        confirmButtonText:'Sí, continuar',
        cancelButtonText:'Cancelar'
    }).then(r => {
        if (!r.isConfirmed) return;
        const fd = new FormData();
        fd.append('id', id);
        fd.append('estado', estado);
        fetch('/Tickets/cambiarEstado', { method:'POST', body:fd, credentials:'same-origin' })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon:'success', title:'¡Actualizado!',
                        text:data.message, confirmButtonColor:'#005C3E'
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
    });
}

function asignar(ticketId) {
    const empleadoId = document.getElementById('selectAsignar').value;
    if (!empleadoId) {
        Swal.fire({ icon:'warning', title:'Selecciona un empleado', confirmButtonColor:'#005C3E' });
        return;
    }
    const fd = new FormData();
    fd.append('ticket_id', ticketId);
    fd.append('empleado_id', empleadoId);
    fetch('/Tickets/asignar', { method:'POST', body:fd, credentials:'same-origin' })
        .then(r => r.json())
        .then(data => {
            Swal.fire({
                icon: data.success ? 'success' : 'error',
                title: data.success ? '¡Asignado!' : 'Error',
                text: data.message, confirmButtonColor:'#005C3E'
            }).then(() => { if (data.success) location.reload(); });
        });
}
</script>