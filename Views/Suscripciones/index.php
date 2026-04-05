<!-- Views/Suscripciones/index.php -->

<style>
.avatar-circle {
    width:36px; height:36px; border-radius:50%;
    background:linear-gradient(135deg,#005C3E,#00E676);
    color:#fff; display:flex; align-items:center;
    justify-content:center; font-weight:700; font-size:.75rem; flex-shrink:0;
}
tr.plan-basico   td:first-child { border-left:4px solid #6c757d !important; }
tr.plan-estandar td:first-child { border-left:4px solid #0d6efd !important; }
tr.plan-premium  td:first-child { border-left:4px solid #ffc107 !important; }
tr.plan-basico   { background-color:rgba(108,117,125,0.05) !important; }
tr.plan-estandar { background-color:rgba(13,110,253,0.05)  !important; }
tr.plan-premium  { background-color:rgba(255,193,7,0.07)   !important; }
html.dark-mode tr.plan-basico   { background-color:rgba(108,117,125,0.08) !important; }
html.dark-mode tr.plan-estandar { background-color:rgba(13,110,253,0.08)  !important; }
html.dark-mode tr.plan-premium  { background-color:rgba(255,193,7,0.08)   !important; }

.table-hover > tbody > tr:hover > :not(caption) > * > *,
.table-hover > tbody > tr:hover > * {
    background-color:rgba(0,92,62,0.08) !important;
    --bs-table-bg-state:rgba(0,92,62,0.08) !important;
}
html.dark-mode .table {
    --bs-table-color:#e0e0e0 !important; --bs-table-bg:transparent !important;
    --bs-table-border-color:#1e3329 !important; color:#e0e0e0 !important;
}
html.dark-mode .table > :not(caption) > * > * {
    background-color:transparent !important; color:#e0e0e0 !important;
    border-bottom-color:#1e3329 !important;
}
html.dark-mode .table thead > tr > * {
    background-color:#1a3329 !important; color:#fff !important; border-color:#1e3329 !important;
}
html.dark-mode .table p, html.dark-mode .table .fw-semibold { color:#fff !important; }
html.dark-mode .table small, html.dark-mode .table .text-muted { color:#adb5bd !important; }
html.dark-mode .table-hover > tbody > tr:hover > :not(caption) > * > *,
html.dark-mode .table-hover > tbody > tr:hover > * {
    background-color:rgba(0,230,118,0.12) !important; color:#e0e0e0 !important;
}
html.dark-mode .card-footer { background:#111f18 !important; border-color:#1e3329 !important; }
html.dark-mode .form-select { background:#111f18; border-color:#1e3329; color:#e0e0e0; }

/* ── MODAL DARK MODE ── */
html.dark-mode .modal-content {
    background:#1a2e24 !important;
    border-color:#1e3329 !important;
    color:#e0e0e0 !important;
}
html.dark-mode .modal-body { color:#e0e0e0 !important; }
html.dark-mode .modal-footer {
    background:#111f18 !important;
    border-color:#1e3329 !important;
}
html.dark-mode .modal-body .text-muted { color:#adb5bd !important; }

/* Plan cards del modal */
.plan-modal-card {
    border-radius:10px; padding:.85rem 1rem;
    cursor:pointer; transition:all 0.2s;
    border:2px solid #dee2e6;
    background:#fff;
}
.plan-modal-card:hover { border-color:#005C3E; background:rgba(0,92,62,0.04); }
.plan-modal-card.selected {
    border-color:#005C3E !important;
    background:rgba(0,92,62,0.08) !important;
    box-shadow:0 3px 12px rgba(0,92,62,0.2);
}
html.dark-mode .plan-modal-card {
    background:#0f1a15 !important;
    border-color:#2a4535 !important;
    color:#e0e0e0 !important;
}
html.dark-mode .plan-modal-card:hover {
    border-color:#00E676 !important;
    background:rgba(0,230,118,0.06) !important;
}
html.dark-mode .plan-modal-card.selected {
    border-color:#00E676 !important;
    background:rgba(0,230,118,0.1) !important;
}
html.dark-mode .plan-modal-card .text-muted { color:#adb5bd !important; }
html.dark-mode .plan-modal-card .fw-bold { color:#fff !important; }
html.dark-mode .plan-modal-card small { color:#adb5bd !important; }

/* Toggle período modal */
.periodo-toggle-modal {
    display:flex; border-radius:8px; overflow:hidden;
    border:2px solid #005C3E; width:100%;
}
.periodo-btn-modal {
    flex:1; padding:.5rem .75rem; text-align:center;
    cursor:pointer; font-weight:600; font-size:.8rem;
    transition:all 0.2s; border:none; background:transparent;
    color:#005C3E;
}
.periodo-btn-modal.activo { background:#005C3E; color:#fff; }
html.dark-mode .periodo-toggle-modal { border-color:#00E676; }
html.dark-mode .periodo-btn-modal { color:#00E676; }
html.dark-mode .periodo-btn-modal.activo { background:#005C3E; color:#fff; }

/* Badge descuento */
.badge-off {
    background:linear-gradient(135deg,#dc3545,#ff6b6b);
    color:#fff; font-size:.65rem; padding:2px 6px;
    border-radius:20px; font-weight:700; vertical-align:middle;
}

/* Resumen precio modal */
.precio-modal-resumen {
    border-radius:8px; padding:.75rem 1rem;
    background:rgba(0,92,62,0.06);
    border:1px solid rgba(0,92,62,0.2);
}
html.dark-mode .precio-modal-resumen {
    background:rgba(0,230,118,0.05) !important;
    border-color:rgba(0,230,118,0.15) !important;
    color:#e0e0e0 !important;
}
html.dark-mode .precio-modal-resumen .text-muted { color:#adb5bd !important; }
html.dark-mode .precio-modal-resumen .text-success { color:#00E676 !important; }
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
                    <input type="text" class="form-control" id="buscador" placeholder="Nombre, email o empresa...">
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
                            : (str_contains($pN,'est') ? 'plan-estandar' : 'plan-basico');
                        $dias   = (int)((strtotime($s['fecha_vencimiento']) - time()) / 86400);
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
                            <?php $bc = str_contains($pN,'premium') ? 'warning text-dark'
                                : (str_contains($pN,'est') ? 'primary' : 'secondary'); ?>
                            <span class="badge bg-<?= $bc ?>"><?= htmlspecialchars($s['plan_nombre']) ?></span>
                            <br><small class="text-muted">L.<?= number_format($s['plan_precio'],2) ?>/mes</small>
                        </td>
                        <td><small><?= date('d/m/Y', strtotime($s['fecha_inicio'])) ?></small></td>
                        <td>
                            <small class="<?= $claseD ?>"><?= date('d/m/Y', strtotime($s['fecha_vencimiento'])) ?></small>
                            <br><small class="text-muted"><?= $dias >= 0 ? $dias.' días' : 'Vencida' ?></small>
                        </td>
                        <td>
                            <?php
                            $b = [
                                'activa'     => '<span class="badge-activa"><span class="status-active-pulse me-1"></span>Activa</span>',
                                'por_vencer' => '<span class="badge-por-vencer"><i class="fas fa-clock me-1"></i>Por vencer</span>',
                                'vencida'    => '<span class="badge-vencida"><i class="fas fa-times-circle me-1"></i>Vencida</span>',
                                'suspendida' => '<span class="badge-suspendida"><i class="fas fa-pause-circle me-1"></i>Suspendida</span>',
                            ];
                            echo $b[$s['estado']] ?? $s['estado'];
                            ?>
                        </td>
                        <td>
                            <?php if (!empty($s['renovacion_plan_nombre'])): ?>
                            <span class="badge bg-info text-dark">
                                <i class="fas fa-arrow-right me-1"></i>
                                <?= htmlspecialchars($s['renovacion_plan_nombre']) ?>
                            </span>
                            <?php else: ?>
                            <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if (in_array($s['estado'], ['activa','por_vencer'])): ?>
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

<!-- ============================================
     MODAL CAMBIAR PLAN — Dark mode corregido
     + Toggle mensual/anual
     ============================================ -->
<div class="modal fade" id="modalCambiarPlan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:linear-gradient(135deg,#005C3E,#00895a);">
                <h5 class="modal-title"><i class="fas fa-exchange-alt me-2"></i>Cambiar Plan al Vencer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1" style="color:inherit;">
                    El nuevo plan y período se aplicarán cuando venza la suscripción actual.
                </p>
                <p class="fw-bold mb-3" id="nombreClienteCambio" style="color:inherit;"></p>

                <!-- Toggle mensual/anual -->
                <div class="mb-3">
                    <label class="form-label fw-semibold mb-2" style="color:inherit;">Período del próximo ciclo</label>
                    <div class="periodo-toggle-modal">
                        <button type="button" class="periodo-btn-modal activo" id="btnModalMensual"
                                onclick="seleccionarPeriodoModal('mensual')">
                            <i class="fas fa-calendar me-1"></i>Mensual
                        </button>
                        <button type="button" class="periodo-btn-modal" id="btnModalAnual"
                                onclick="seleccionarPeriodoModal('anual')">
                            <i class="fas fa-calendar-alt me-1"></i>Anual
                        </button>
                    </div>
                    <small id="textoDescuentoModal" class="mt-1 d-block text-muted"></small>
                </div>

                <!-- Selección de plan -->
                <label class="form-label fw-semibold mb-2" style="color:inherit;">Selecciona el nuevo plan</label>
                <div class="d-flex flex-column gap-2" id="contenedorPlanesModal">
                    <?php foreach ($planes as $plan):
                        $pN   = strtolower($plan['nombre']);
                        $pCls = str_contains($pN,'premium') ? 'warning text-dark'
                            : (str_contains($pN,'est') ? 'primary' : 'secondary');
                        $pIcon = str_contains($pN,'premium') ? 'fas fa-crown'
                            : (str_contains($pN,'est') ? 'fas fa-star' : 'fas fa-leaf');
                        $tieneDescuento = (float)$plan['descuento_anual'] > 0;
                    ?>
                    <div class="plan-modal-card"
                         data-id="<?= $plan['id'] ?>"
                         data-nombre="<?= htmlspecialchars($plan['nombre'], ENT_QUOTES) ?>"
                         data-precio="<?= $plan['precio'] ?>"
                         data-precio-anual="<?= $plan['precio_anual'] ?>"
                         data-ahorro="<?= $plan['ahorro_anual'] ?>"
                         data-descuento="<?= $plan['descuento_anual'] ?>"
                         onclick="seleccionarPlanModal(this)">
                        <div class="d-flex align-items-center gap-3">
                            <i class="<?= $pIcon ?> fa-lg" style="color:var(--accent);"></i>
                            <div class="flex-grow-1">
                                <div class="fw-bold"><?= htmlspecialchars($plan['nombre']) ?></div>
                                <div class="precio-mensual-modal small text-muted">
                                    L.<?= number_format($plan['precio'],2) ?>/mes
                                </div>
                                <?php if ($tieneDescuento): ?>
                                <div class="precio-anual-modal small text-muted" style="display:none;">
                                    L.<?= number_format($plan['precio_anual'],2) ?>/año
                                    <span class="badge-off"><?= number_format($plan['descuento_anual'],0) ?>% OFF</span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <i class="fas fa-check-circle text-success d-none check-modal"></i>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Resumen precio -->
                <div class="precio-modal-resumen mt-3 d-none" id="resumenModal">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Próximo período</div>
                            <div class="fw-bold" id="resumenModalPlan" style="color:inherit;"></div>
                            <div class="small text-muted" id="resumenModalPeriodo"></div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Total a cobrar</div>
                            <div class="fs-5 fw-bold text-success" id="resumenModalTotal"></div>
                            <div class="small text-success d-none" id="resumenModalAhorro"></div>
                        </div>
                    </div>
                </div>

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
// ── Filtros ──
const buscador   = document.getElementById('buscador');
const filtro     = document.getElementById('filtroEstado');
const filtroPlan = document.getElementById('filtroPlan');

function filtrar() {
    const txt = buscador.value.toLowerCase();
    const est = filtro.value;
    const pl  = filtroPlan.value;
    let v = 0;
    document.querySelectorAll('#tablaSuscripciones tbody tr[data-buscar]').forEach(f => {
        const ok = f.dataset.buscar.includes(txt)
            && (!est || f.dataset.estado === est)
            && (!pl  || f.dataset.plan   === pl);
        f.style.display = ok ? '' : 'none';
        if (ok) v++;
    });
    document.getElementById('contador').textContent = v + ' registros';
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
        html: `<strong>${nombre}</strong><br><small>Los días restantes quedan congelados.</small>`,
        icon: 'warning', showCancelButton: true,
        confirmButtonColor:'#005C3E', cancelButtonColor:'#6c757d',
        confirmButtonText:'Sí, suspender', cancelButtonText:'Cancelar'
    }).then(r => {
        if (!r.isConfirmed) return;
        const fd = new FormData();
        fd.append('cliente_id', clienteId);
        fetch('/Suscripciones/suspender', { method:'POST', body:fd, credentials:'same-origin' })
            .then(r => r.json())
            .then(d => {
                Swal.fire({ icon:d.success?'success':'error',
                    title:d.success?'¡Suspendida!':'Error',
                    text:d.message, confirmButtonColor:'#005C3E'
                }).then(() => { if (d.success) location.reload(); });
            });
    });
}

// ── Reactivar ──
function confirmarReactivar(clienteId, nombre) {
    Swal.fire({
        title: '¿Reactivar suscripción?',
        html: `<strong>${nombre}</strong><br><small>La fecha de vencimiento se extenderá por los días suspendidos.</small>`,
        icon: 'question', showCancelButton: true,
        confirmButtonColor:'#005C3E', cancelButtonColor:'#6c757d',
        confirmButtonText:'Sí, reactivar', cancelButtonText:'Cancelar'
    }).then(r => {
        if (!r.isConfirmed) return;
        const fd = new FormData();
        fd.append('cliente_id', clienteId);
        fetch('/Suscripciones/reactivar', { method:'POST', body:fd, credentials:'same-origin' })
            .then(r => r.json())
            .then(d => {
                Swal.fire({ icon:d.success?'success':'error',
                    title:d.success?'¡Reactivada!':'Error',
                    text:d.message, confirmButtonColor:'#005C3E'
                }).then(() => { if (d.success) location.reload(); });
            });
    });
}

// ── Modal cambiar plan ──
let periodoModal    = 'mensual';
let planModalSelec  = null;
let clienteIdModal  = null;

function abrirCambiarPlan(clienteId, nombre) {
    clienteIdModal  = clienteId;
    periodoModal    = 'mensual';
    planModalSelec  = null;

    document.getElementById('nombreClienteCambio').textContent = nombre;
    document.getElementById('resumenModal').classList.add('d-none');
    document.getElementById('textoDescuentoModal').textContent = '';

    // Resetea toggle
    document.getElementById('btnModalMensual').classList.add('activo');
    document.getElementById('btnModalAnual').classList.remove('activo');

    // Resetea cards
    document.querySelectorAll('.plan-modal-card').forEach(c => {
        c.classList.remove('selected');
        c.querySelector('.check-modal')?.classList.add('d-none');
    });

    // Muestra precios mensuales
    document.querySelectorAll('.precio-mensual-modal').forEach(el => el.style.display = '');
    document.querySelectorAll('.precio-anual-modal').forEach(el => el.style.display = 'none');

    new bootstrap.Modal(document.getElementById('modalCambiarPlan')).show();
}

function seleccionarPeriodoModal(periodo) {
    periodoModal = periodo;
    document.getElementById('btnModalMensual').classList.toggle('activo', periodo === 'mensual');
    document.getElementById('btnModalAnual').classList.toggle('activo', periodo === 'anual');

    document.querySelectorAll('.precio-mensual-modal').forEach(el => {
        el.style.display = periodo === 'mensual' ? '' : 'none';
    });
    document.querySelectorAll('.precio-anual-modal').forEach(el => {
        el.style.display = periodo === 'anual' ? '' : 'none';
    });

    if (planModalSelec) actualizarResumenModal();
}

function seleccionarPlanModal(el) {
    document.querySelectorAll('.plan-modal-card').forEach(c => {
        c.classList.remove('selected');
        c.querySelector('.check-modal')?.classList.add('d-none');
    });
    el.classList.add('selected');
    el.querySelector('.check-modal')?.classList.remove('d-none');

    planModalSelec = {
        id:          el.dataset.id,
        nombre:      el.dataset.nombre,
        precio:      parseFloat(el.dataset.precio),
        precioAnual: parseFloat(el.dataset.precioAnual),
        ahorro:      parseFloat(el.dataset.ahorro),
        descuento:   parseFloat(el.dataset.descuento),
    };

    // Muestra texto de descuento si aplica
    const texto = document.getElementById('textoDescuentoModal');
    if (planModalSelec.descuento > 0 && periodoModal === 'anual') {
        texto.textContent = `Ahorro vs mensual: L.${planModalSelec.ahorro.toFixed(2)}`;
        texto.style.color = '#00a854';
    } else if (planModalSelec.descuento > 0) {
        texto.textContent = `Cambia a anual y ahorra L.${planModalSelec.ahorro.toFixed(2)}`;
        texto.style.color = '';
    } else {
        texto.textContent = 'Este plan no tiene descuento anual.';
        texto.style.color = '';
    }

    actualizarResumenModal();
}

function actualizarResumenModal() {
    if (!planModalSelec) return;

    const resumen = document.getElementById('resumenModal');
    resumen.classList.remove('d-none');

    const total = periodoModal === 'anual' ? planModalSelec.precioAnual : planModalSelec.precio;
    const periodo = periodoModal === 'anual' ? 'Anual (365 días)' : 'Mensual';

    document.getElementById('resumenModalPlan').textContent    = planModalSelec.nombre;
    document.getElementById('resumenModalPeriodo').textContent = periodo;
    document.getElementById('resumenModalTotal').textContent   = 'L.' + total.toFixed(2);

    const divAhorro = document.getElementById('resumenModalAhorro');
    if (periodoModal === 'anual' && planModalSelec.ahorro > 0) {
        divAhorro.textContent = `Ahorro: L.${planModalSelec.ahorro.toFixed(2)}`;
        divAhorro.classList.remove('d-none');
    } else {
        divAhorro.classList.add('d-none');
    }
}

function guardarCambioPlan() {
    if (!planModalSelec) {
        Swal.fire({ icon:'warning', title:'Selecciona un plan', confirmButtonColor:'#005C3E' });
        return;
    }

    const fd = new FormData();
    fd.append('cliente_id', clienteIdModal);
    fd.append('plan_id', planModalSelec.id);

    fetch('/Suscripciones/cambiarPlan', { method:'POST', body:fd, credentials:'same-origin' })
        .then(r => r.json())
        .then(d => {
            bootstrap.Modal.getInstance(document.getElementById('modalCambiarPlan')).hide();
            Swal.fire({
                icon:  d.success ? 'success' : 'error',
                title: d.success ? '¡Programado!' : 'Error',
                html:  d.success
                    ? `Cambio a <strong>${planModalSelec.nombre}</strong> (${periodoModal}) programado correctamente.`
                    : d.message,
                confirmButtonColor:'#005C3E'
            }).then(() => { if (d.success) location.reload(); });
        });
}
</script>