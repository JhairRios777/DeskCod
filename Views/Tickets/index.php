<!-- Views/Tickets/index.php -->

<style>
/* Tabla dark mode */
.table-hover > tbody > tr:hover > :not(caption) > * > *,
.table-hover > tbody > tr:hover > * {
    background-color: rgba(0,92,62,0.08) !important;
    --bs-table-bg-state: rgba(0,92,62,0.08) !important;
    --bs-table-bg-type:  rgba(0,92,62,0.08) !important;
}
body.dark-mode .table {
    --bs-table-color: #e0e0e0 !important; --bs-table-bg: transparent !important;
    --bs-table-border-color: #1e3329 !important; color: #e0e0e0 !important;
}
body.dark-mode .table > :not(caption) > * > * {
    background-color: transparent !important; color: #e0e0e0 !important;
    border-bottom-color: #1e3329 !important;
    --bs-table-bg-state: transparent !important; --bs-table-bg-type: transparent !important;
}
body.dark-mode .table thead > tr > * {
    background-color: #1a3329 !important; color: #fff !important; border-color: #1e3329 !important;
}
body.dark-mode .table p, body.dark-mode .table .fw-semibold { color: #fff !important; }
body.dark-mode .table small, body.dark-mode .table .text-muted { color: #adb5bd !important; }
body.dark-mode .table-hover > tbody > tr:hover > :not(caption) > * > *,
body.dark-mode .table-hover > tbody > tr:hover > * {
    background-color: rgba(0,230,118,0.12) !important; color: #e0e0e0 !important;
    --bs-table-bg-state: rgba(0,230,118,0.12) !important;
    --bs-table-bg-type:  rgba(0,230,118,0.12) !important;
}
body.dark-mode .card-footer { background: #111f18 !important; border-color: #1e3329 !important; }
body.dark-mode .form-select { background:#111f18; border-color:#1e3329; color:#e0e0e0; }

/* Prioridad */
.badge-critica   { background: #dc3545; color:#fff; }
.badge-alta      { background: #fd7e14; color:#fff; }
.badge-media     { background: #ffc107; color:#000; }
.badge-baja      { background: #6c757d; color:#fff; }

/* Filas por prioridad */
tr.prioridad-critica td:first-child { border-left: 4px solid #dc3545 !important; }
tr.prioridad-alta    td:first-child { border-left: 4px solid #fd7e14 !important; }
tr.prioridad-media   td:first-child { border-left: 4px solid #ffc107 !important; }
tr.prioridad-baja    td:first-child { border-left: 4px solid #6c757d !important; }
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-ticket-alt me-2 text-success"></i>Tickets
        </h4>
        <small class="text-muted">Gestión de soporte y solicitudes</small>
    </div>
    <a href="/Tickets/Registry" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nuevo Ticket
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

<!-- Filtros -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:#005C3E;color:#fff;border-color:#005C3E;">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="buscador"
                           placeholder="Título, cliente...">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1">Estado</label>
                <select class="form-select" id="filtroEstado">
                    <option value="">Todos</option>
                    <option value="abierto">Abierto</option>
                    <option value="en_proceso">En proceso</option>
                    <option value="esperando_cliente">Esperando cliente</option>
                    <option value="resuelto">Resuelto</option>
                    <option value="cerrado">Cerrado</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1">Prioridad</label>
                <select class="form-select" id="filtroPrioridad">
                    <option value="">Todas</option>
                    <option value="critica">Crítica</option>
                    <option value="alta">Alta</option>
                    <option value="media">Media</option>
                    <option value="baja">Baja</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1">Tipo</label>
                <select class="form-select" id="filtroTipo">
                    <option value="">Todos</option>
                    <option value="error">Error</option>
                    <option value="modificacion">Modificación</option>
                    <option value="nueva_funcion">Nueva función</option>
                    <option value="consulta">Consulta</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                    <i class="fas fa-times me-1"></i>Limpiar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tabla -->
<div class="card shadow-sm">
    <div class="card-header bg-primary d-flex align-items-center justify-content-between">
        <span><i class="fas fa-table me-2"></i>Lista de Tickets</span>
        <span class="badge bg-light text-dark" id="contador"><?= count($tickets) ?> registros</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tablaTickets">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>Título</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Asignado a</th>
                        <th>Creado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-ticket-alt fa-3x mb-3 d-block opacity-25"></i>
                            No hay tickets registrados.
                            <br>
                            <a href="/Tickets/Registry" class="btn btn-primary btn-sm mt-3">
                                <i class="fas fa-plus me-1"></i>Crear primer ticket
                            </a>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($tickets as $i => $t): ?>
                    <tr class="prioridad-<?= $t['prioridad'] ?>"
                        data-estado="<?= $t['estado'] ?>"
                        data-prioridad="<?= $t['prioridad'] ?>"
                        data-tipo="<?= $t['tipo'] ?>"
                        data-buscar="<?= strtolower(htmlspecialchars($t['titulo'].' '.$t['cliente_nombre'])) ?>">
                        <td class="text-muted fw-bold">#<?= str_pad($t['id'],4,'0',STR_PAD_LEFT) ?></td>
                        <td>
                            <p class="mb-0 fw-semibold"><?= htmlspecialchars($t['titulo']) ?></p>
                            <?php if ($t['categoria']): ?>
                            <small class="text-muted"><?= htmlspecialchars($t['categoria']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small><?= htmlspecialchars($t['cliente_nombre']) ?></small>
                        </td>
                        <td>
                            <?php
                            $tipoLabels = [
                                'error'         => ['label'=>'Error',         'class'=>'danger'],
                                'modificacion'  => ['label'=>'Modificación',  'class'=>'warning text-dark'],
                                'nueva_funcion' => ['label'=>'Nueva función', 'class'=>'info text-dark'],
                                'consulta'      => ['label'=>'Consulta',      'class'=>'secondary'],
                            ];
                            $tl = $tipoLabels[$t['tipo']] ?? ['label'=>$t['tipo'],'class'=>'secondary'];
                            ?>
                            <span class="badge bg-<?= $tl['class'] ?>"><?= $tl['label'] ?></span>
                        </td>
                        <td>
                            <?php
                            $prioLabels = [
                                'critica' => ['label'=>'Crítica', 'class'=>'critica'],
                                'alta'    => ['label'=>'Alta',    'class'=>'alta'],
                                'media'   => ['label'=>'Media',   'class'=>'media'],
                                'baja'    => ['label'=>'Baja',    'class'=>'baja'],
                            ];
                            $pl = $prioLabels[$t['prioridad']] ?? ['label'=>$t['prioridad'],'class'=>'baja'];
                            ?>
                            <span class="badge badge-<?= $pl['class'] ?>"><?= $pl['label'] ?></span>
                        </td>
                        <td>
                            <?php
                            $estadoLabels = [
                                'abierto'            => ['label'=>'Abierto',             'class'=>'badge-activa'],
                                'en_proceso'         => ['label'=>'En proceso',          'class'=>'badge-por-vencer'],
                                'esperando_cliente'  => ['label'=>'Esperando cliente',   'class'=>'badge-suspendida'],
                                'resuelto'           => ['label'=>'Resuelto',            'class'=>'badge-activa'],
                                'cerrado'            => ['label'=>'Cerrado',             'class'=>'badge-vencida'],
                            ];
                            $el = $estadoLabels[$t['estado']] ?? ['label'=>$t['estado'],'class'=>'badge bg-secondary'];
                            ?>
                            <span class="<?= $el['class'] ?>"><?= $el['label'] ?></span>
                        </td>
                        <td>
                            <small><?= $t['empleado_nombre'] ? htmlspecialchars($t['empleado_nombre']) : '<span class="text-muted">Sin asignar</span>' ?></small>
                        </td>
                        <td>
                            <small class="text-muted"><?= date('d/m/Y', strtotime($t['created_at'])) ?></small>
                        </td>
                        <td class="text-center">
                            <a href="/Tickets/ver/<?= $t['id'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Ver ticket">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($tickets)): ?>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Total: <strong><?= count($tickets) ?></strong> tickets</small>
        <small class="text-muted">DeskCod</small>
    </div>
    <?php endif; ?>
</div>

<script>
const buscador       = document.getElementById('buscador');
const filtroEstado   = document.getElementById('filtroEstado');
const filtroPrioridad = document.getElementById('filtroPrioridad');
const filtroTipo     = document.getElementById('filtroTipo');

function filtrar() {
    const txt  = buscador.value.toLowerCase();
    const est  = filtroEstado.value;
    const prio = filtroPrioridad.value;
    const tipo = filtroTipo.value;
    let visibles = 0;

    document.querySelectorAll('#tablaTickets tbody tr[data-buscar]').forEach(fila => {
        const ok = fila.dataset.buscar.includes(txt)
            && (!est  || fila.dataset.estado    === est)
            && (!prio || fila.dataset.prioridad === prio)
            && (!tipo || fila.dataset.tipo      === tipo);
        fila.style.display = ok ? '' : 'none';
        if (ok) visibles++;
    });
    document.getElementById('contador').textContent = visibles + ' registros';
}

function limpiarFiltros() {
    buscador.value = filtroEstado.value = filtroPrioridad.value = filtroTipo.value = '';
    document.querySelectorAll('#tablaTickets tbody tr').forEach(r => r.style.display = '');
    document.getElementById('contador').textContent =
        document.querySelectorAll('#tablaTickets tbody tr[data-buscar]').length + ' registros';
}

buscador.addEventListener('input', filtrar);
filtroEstado.addEventListener('change', filtrar);
filtroPrioridad.addEventListener('change', filtrar);
filtroTipo.addEventListener('change', filtrar);
</script>