<!-- Views/Suscripciones/index.php -->

<style>
.avatar-circle {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg,#005C3E,#00E676);
    color:#fff; display:flex; align-items:center;
    justify-content:center; font-weight:700; font-size:.75rem; flex-shrink:0;
}

/* Colores de fila según plan */
tr.plan-basico   td:first-child { border-left: 4px solid #6c757d !important; }
tr.plan-estandar td:first-child { border-left: 4px solid #0d6efd !important; }
tr.plan-premium  td:first-child { border-left: 4px solid #ffc107 !important; }
tr.plan-basico   { background-color: rgba(108,117,125,0.05) !important; }
tr.plan-estandar { background-color: rgba(13,110,253,0.05)  !important; }
tr.plan-premium  { background-color: rgba(255,193,7,0.07)   !important; }

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
    background-color: #1a3329 !important; color: #fff !important;
    border-color: #1e3329 !important;
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
body.dark-mode tr.plan-basico   { background-color: rgba(108,117,125,0.08) !important; }
body.dark-mode tr.plan-estandar { background-color: rgba(13,110,253,0.08)  !important; }
body.dark-mode tr.plan-premium  { background-color: rgba(255,193,7,0.08)   !important; }
body.dark-mode .form-select { background:#111f18; border-color:#1e3329; color:#e0e0e0; }
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-sync-alt me-2 text-success"></i>Suscripciones
        </h4>
        <small class="text-muted">Gestión de planes y renovaciones</small>
    </div>
    <a href="/Suscripciones/Registry<?= $clienteFiltro ? "?cliente={$clienteFiltro}" : '' ?>"
       class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nueva Suscripción
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

<?php if ($clienteFiltro): ?>
<div class="alert alert-info d-flex align-items-center justify-content-between mb-4">
    <span><i class="fas fa-filter me-2"></i>Mostrando suscripciones de un cliente específico</span>
    <a href="/Suscripciones" class="btn btn-sm btn-outline-primary">Ver todas</a>
</div>
<?php endif; ?>

<!-- Filtros -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold mb-1">Buscar cliente</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:#005C3E;color:#fff;border-color:#005C3E;">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="buscador"
                           placeholder="Nombre, email o empresa...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold mb-1">Estado</label>
                <select class="form-select" id="filtroEstado">
                    <option value="">Todos</option>
                    <option value="activa">Activa</option>
                    <option value="por_vencer">Por vencer</option>
                    <option value="vencida">Vencida</option>
                    <option value="suspendida">Suspendida</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1">Plan</label>
                <select class="form-select" id="filtroPlan">
                    <option value="">Todos</option>
                    <option value="plan-basico">Básico</option>
                    <option value="plan-estandar">Estándar</option>
                    <option value="plan-premium">Premium</option>
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
        <span><i class="fas fa-table me-2"></i>Lista de Suscripciones</span>
        <span class="badge bg-light text-dark" id="contador"><?= count($suscripciones) ?> registros</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tablaSuscripciones">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Cliente</th>
                        <th>Plan</th>
                        <th>Inicio</th>
                        <th>Vencimiento</th>
                        <th>Estado</th>
                        <th>Próximo plan</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($suscripciones)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-sync-alt fa-3x mb-3 d-block opacity-25"></i>
                            No hay suscripciones registradas.
                            <br>
                            <a href="/Suscripciones/Registry" class="btn btn-primary btn-sm mt-3">
                                <i class="fas fa-plus me-1"></i>Crear suscripción
                            </a>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($suscripciones as $i => $s):
                        $p   = explode(' ', $s['cliente_nombre']);
                        $ini = strtoupper(substr($p[0],0,1).(isset($p[1])?substr($p[1],0,1):''));
                        $pN  = strtolower($s['plan_nombre'] ?? '');
                        $planClase = str_contains($pN,'premium') ? 'plan-premium'
                            : (str_contains($pN,'estándar') || str_contains($pN,'estandar') ? 'plan-estandar' : 'plan-basico');
                        $dias = (int)((strtotime($s['fecha_vencimiento']) - time()) / 86400);
                        $claseD = $dias > 7 ? 'text-success' : ($dias > 0 ? 'text-warning fw-bold' : 'text-danger fw-bold');
                    ?>
                    <tr class="<?= $planClase ?>"
                        data-estado="<?= htmlspecialchars($s['estado']) ?>"
                        data-plan="<?= $planClase ?>"
                        data-buscar="<?= strtolower(htmlspecialchars($s['cliente_nombre'].' '.$s['cliente_email'].' '.($s['empresa_nombre']??''))) ?>">
                        <td class="text-muted"><?= str_pad($i+1,3,'0',STR_PAD_LEFT) ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle"><?= $ini ?></div>
                                <div>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($s['cliente_nombre']) ?></p>
                                    <small class="text-muted"><?= htmlspecialchars($s['empresa_nombre'] ?? $s['cliente_email']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                            $badgeColor = str_contains($pN,'premium') ? 'warning text-dark'
                                : (str_contains($pN,'estándar') || str_contains($pN,'estandar') ? 'primary' : 'secondary');
                            ?>
                            <span class="badge bg-<?= $badgeColor ?>">
                                <?= htmlspecialchars($s['plan_nombre']) ?>
                            </span>
                            <br><small class="text-muted">$<?= number_format($s['plan_precio'],2) ?>/mes</small>
                        </td>
                        <td><small><?= date('d/m/Y', strtotime($s['fecha_inicio'])) ?></small></td>
                        <td>
                            <small class="<?= $claseD ?>">
                                <?= date('d/m/Y', strtotime($s['fecha_vencimiento'])) ?>
                            </small>
                            <br>
                            <small class="text-muted">
                                <?= $dias >= 0 ? $dias.' días' : 'Vencida' ?>
                            </small>
                        </td>
                        <td>
                            <?php
                            $b = [
                                'activa'     => '<span class="badge-activa"><span class="status-active-pulse me-1"></span>Activa</span>',
                                'por_vencer' => '<span class="badge-por-vencer"><i class="fas fa-clock me-1"></i>Por vencer</span>',
                                'vencida'    => '<span class="badge-vencida"><i class="fas fa-times-circle me-1"></i>Vencida</span>',
                                'suspendida' => '<span class="badge-suspendida"><i class="fas fa-pause-circle me-1"></i>Suspendida</span>',
                                'cancelada'  => '<span class="badge bg-dark">Cancelada</span>',
                            ];
                            echo $b[$s['estado']] ?? $s['estado'];
                            ?>
                        </td>
                        <td>
                            <?php if ($s['renovacion_plan_nombre']): ?>
                            <span class="badge bg-info text-dark">
                                <i class="fas fa-arrow-right me-1"></i>
                                <?= htmlspecialchars($s['renovacion_plan_nombre']) ?>
                            </span>
                            <?php else: ?>
                            <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($s['estado'] === 'activa' || $s['estado'] === 'por_vencer'): ?>
                            <button class="btn btn-sm btn-outline-warning me-1"
                                    onclick="abrirCambiarPlan(<?= $s['cliente_id'] ?>, '<?= htmlspecialchars($s['cliente_nombre'], ENT_QUOTES) ?>')"
                                    title="Cambiar plan al vencer">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary me-1"
                                    onclick="confirmarSuspender(<?= $s['cliente_id'] ?>, '<?= htmlspecialchars($s['cliente_nombre'], ENT_QUOTES) ?>')"
                                    title="Suspender">
                                <i class="fas fa-pause"></i>
                            </button>
                            <?php elseif ($s['estado'] === 'suspendida'): ?>
                            <button class="btn btn-sm btn-outline-success me-1"
                                    onclick="confirmarReactivar(<?= $s['cliente_id'] ?>, '<?= htmlspecialchars($s['cliente_nombre'], ENT_QUOTES) ?>')"
                                    title="Reactivar">
                                <i class="fas fa-play"></i>
                            </button>
                            <?php endif; ?>
                            <a href="/Suscripciones/Registry?cliente=<?= $s['cliente_id'] ?>"
                               class="btn btn-sm btn-outline-primary"
                               title="Nueva suscripción para este cliente">
                                <i class="fas fa-plus"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($suscripciones)): ?>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Total: <strong><?= count($suscripciones) ?></strong> suscripciones</small>
        <small class="text-muted">DeskCod</small>
    </div>
    <?php endif; ?>
</div>

<!-- Modal cambiar plan -->
<div class="modal fade" id="modalCambiarPlan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt me-2"></i>Cambiar Plan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">
                    El nuevo plan se aplicará automáticamente cuando venza la suscripción actual.
                    El cliente no perderá los días restantes.
                </p>
                <p><strong id="nombreClienteCambio"></strong></p>
                <input type="hidden" id="clienteIdCambio">
                <label class="form-label fw-semibold">Nuevo plan</label>
                <div class="d-flex flex-column gap-2" id="planOptionsCambio">
                    <!-- Se llena con JS -->
                </div>
                <input type="hidden" id="planIdCambio">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarCambioPlan()">
                    <i class="fas fa-save me-2"></i>Programar cambio
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Datos de planes para el modal (generados por PHP)
const planesData = <?= json_encode(
    array_map(function($s) {
        return [
            'id'     => $s['plan_id'],
            'nombre' => $s['plan_nombre'],
            'precio' => $s['plan_precio'],
        ];
    }, array_unique(
        array_filter($suscripciones, fn($s) => !empty($s['plan_id'])),
        SORT_REGULAR
    ))
) ?>;

// ── Filtros ──
const buscador   = document.getElementById('buscador');
const filtro     = document.getElementById('filtroEstado');
const filtroPlan = document.getElementById('filtroPlan');

function filtrar() {
    const txt    = buscador.value.toLowerCase();
    const estado = filtro.value;
    const plan   = filtroPlan.value;
    let visibles = 0;
    document.querySelectorAll('#tablaSuscripciones tbody tr[data-buscar]').forEach(fila => {
        const ok = fila.dataset.buscar.includes(txt)
            && (!estado || fila.dataset.estado === estado)
            && (!plan   || fila.dataset.plan   === plan);
        fila.style.display = ok ? '' : 'none';
        if (ok) visibles++;
    });
    document.getElementById('contador').textContent = visibles + ' registros';
}

function limpiarFiltros() {
    buscador.value = filtro.value = filtroPlan.value = '';
    document.querySelectorAll('#tablaSuscripciones tbody tr').forEach(r => r.style.display = '');
    document.getElementById('contador').textContent =
        document.querySelectorAll('#tablaSuscripciones tbody tr[data-buscar]').length + ' registros';
}

buscador.addEventListener('input', filtrar);
filtro.addEventListener('change', filtrar);
filtroPlan.addEventListener('change', filtrar);

// ── Suspender ──
function confirmarSuspender(clienteId, nombre) {
    Swal.fire({
        title: '¿Suspender suscripción?',
        html: `<strong>${nombre}</strong><br><small>Los días restantes quedan congelados y se recuperan al reactivar.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#005C3E',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, suspender',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (!r.isConfirmed) return;
        const fd = new FormData();
        fd.append('cliente_id', clienteId);
        fetch('/Suscripciones/suspender', { method:'POST', body:fd, credentials:'same-origin' })
            .then(r => r.json())
            .then(data => {
                Swal.fire({ icon: data.success ? 'success' : 'error',
                    title: data.success ? '¡Suspendida!' : 'Error',
                    text: data.message, confirmButtonColor:'#005C3E'
                }).then(() => { if (data.success) location.reload(); });
            });
    });
}

// ── Reactivar ──
function confirmarReactivar(clienteId, nombre) {
    Swal.fire({
        title: '¿Reactivar suscripción?',
        html: `<strong>${nombre}</strong><br><small>La fecha de vencimiento se extenderá por los días que estuvo suspendida.</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#005C3E',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, reactivar',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (!r.isConfirmed) return;
        const fd = new FormData();
        fd.append('cliente_id', clienteId);
        fetch('/Suscripciones/reactivar', { method:'POST', body:fd, credentials:'same-origin' })
            .then(r => r.json())
            .then(data => {
                Swal.fire({ icon: data.success ? 'success' : 'error',
                    title: data.success ? '¡Reactivada!' : 'Error',
                    text: data.message, confirmButtonColor:'#005C3E'
                }).then(() => { if (data.success) location.reload(); });
            });
    });
}

// ── Cambiar plan ──
function abrirCambiarPlan(clienteId, nombre) {
    document.getElementById('clienteIdCambio').value = clienteId;
    document.getElementById('nombreClienteCambio').textContent = nombre;
    document.getElementById('planIdCambio').value = '';

    const container = document.getElementById('planOptionsCambio');
    container.innerHTML = '';

    // Obtiene planes únicos del sistema
    fetch('/Suscripciones?planes=1', { credentials:'same-origin' })
        .catch(() => {});

    // Genera botones de plan desde los datos disponibles
    const planes = [
        {id:1, nombre:'Básico',   precio: 29.00},
        {id:2, nombre:'Estándar', precio: 59.00},
        {id:3, nombre:'Premium',  precio: 99.00},
    ];

    planes.forEach(plan => {
        const div = document.createElement('div');
        div.className = 'form-check border rounded p-3 cursor-pointer';
        div.style.cursor = 'pointer';
        div.innerHTML = `
            <input class="form-check-input" type="radio" name="planCambio"
                   id="plan_${plan.id}" value="${plan.id}"
                   onchange="document.getElementById('planIdCambio').value=this.value">
            <label class="form-check-label fw-semibold w-100" for="plan_${plan.id}" style="cursor:pointer;">
                ${plan.nombre}
                <span class="text-muted fw-normal ms-2">$${plan.precio.toFixed(2)}/mes</span>
            </label>`;
        container.appendChild(div);
    });

    new bootstrap.Modal(document.getElementById('modalCambiarPlan')).show();
}

function guardarCambioPlan() {
    const clienteId = document.getElementById('clienteIdCambio').value;
    const planId    = document.getElementById('planIdCambio').value;

    if (!planId) {
        Swal.fire({ icon:'warning', title:'Selecciona un plan',
            confirmButtonColor:'#005C3E' });
        return;
    }

    const fd = new FormData();
    fd.append('cliente_id', clienteId);
    fd.append('plan_id', planId);

    fetch('/Suscripciones/cambiarPlan', { method:'POST', body:fd, credentials:'same-origin' })
        .then(r => r.json())
        .then(data => {
            bootstrap.Modal.getInstance(document.getElementById('modalCambiarPlan')).hide();
            Swal.fire({ icon: data.success ? 'success' : 'error',
                title: data.success ? '¡Programado!' : 'Error',
                text: data.message, confirmButtonColor:'#005C3E'
            }).then(() => { if (data.success) location.reload(); });
        });
}
</script>