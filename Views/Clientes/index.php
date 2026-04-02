<!-- Views/Clientes/index.php -->

<style>
/* ── Avatar ── */
.avatar-circle {
    width: 38px; height: 38px; border-radius: 50%;
    background: linear-gradient(135deg,#005C3E,#00E676);
    color:#fff; display:flex; align-items:center;
    justify-content:center; font-weight:700; font-size:.8rem; flex-shrink:0;
}

/* ── Colores de fila según plan ── */
tr.plan-basico   td { border-left: 4px solid #6c757d !important; }
tr.plan-estandar td:first-child,
tr.plan-estandar td { border-left: 4px solid #0d6efd !important; }
tr.plan-premium  td { border-left: 4px solid #ffc107 !important; }

tr.plan-basico   { background-color: rgba(108,117,125,0.05) !important; }
tr.plan-estandar { background-color: rgba(13,110,253,0.05)  !important; }
tr.plan-premium  { background-color: rgba(255,193,7,0.07)   !important; }

/* Solo aplica el borde a la primera celda */
tr.plan-basico   td:not(:first-child),
tr.plan-estandar td:not(:first-child),
tr.plan-premium  td:not(:first-child) { border-left: none !important; }

tr.plan-basico   td:first-child { border-left: 4px solid #6c757d !important; }
tr.plan-estandar td:first-child { border-left: 4px solid #0d6efd !important; }
tr.plan-premium  td:first-child { border-left: 4px solid #ffc107 !important; }

/* ── TABLA — FIX DARK MODE DEFINITIVO ──────
   Pisamos las variables internas de Bootstrap 5
   directamente con !important en el <style> inline
   que tiene mayor especificidad que cualquier CSS externo
   ────────────────────────────────────────────────────── */

/* Tema claro — hover */
.table-hover > tbody > tr:hover > :not(caption) > * > *,
.table-hover > tbody > tr:hover > * {
    background-color: rgba(0,92,62,0.08) !important;
    --bs-table-bg-state: rgba(0,92,62,0.08) !important;
    --bs-table-bg-type:  rgba(0,92,62,0.08) !important;
}

/* ── MODO OSCURO ── */
body.dark-mode .table {
    --bs-table-color:        #e0e0e0 !important;
    --bs-table-bg:           transparent !important;
    --bs-table-border-color: #1e3329 !important;
    --bs-table-striped-bg:   rgba(0,230,118,0.03) !important;
    --bs-table-hover-bg:     rgba(0,230,118,0.09) !important;
    --bs-table-hover-color:  #e0e0e0 !important;
    color: #e0e0e0 !important;
}

body.dark-mode .table > :not(caption) > * > * {
    background-color: transparent !important;
    color: #e0e0e0 !important;
    border-bottom-color: #1e3329 !important;
    --bs-table-bg-state: transparent !important;
    --bs-table-bg-type:  transparent !important;
}

body.dark-mode .table thead > tr > * {
    background-color: #1a3329 !important;
    color: #ffffff !important;
    border-color: #1e3329 !important;
    --bs-table-bg-state: #1a3329 !important;
    --bs-table-bg-type:  #1a3329 !important;
}

body.dark-mode .table p,
body.dark-mode .table .fw-semibold,
body.dark-mode .table .fw-bold { color: #ffffff !important; }
body.dark-mode .table small,
body.dark-mode .table .text-muted { color: #adb5bd !important; }

body.dark-mode .table-hover > tbody > tr:hover > :not(caption) > * > *,
body.dark-mode .table-hover > tbody > tr:hover > * {
    background-color: rgba(0,230,118,0.12) !important;
    color: #e0e0e0 !important;
    --bs-table-bg-state: rgba(0,230,118,0.12) !important;
    --bs-table-bg-type:  rgba(0,230,118,0.12) !important;
}

body.dark-mode .table-hover > tbody > tr:hover small,
body.dark-mode .table-hover > tbody > tr:hover .text-muted { color: #adb5bd !important; }

/* Colores de plan en modo oscuro */
body.dark-mode tr.plan-basico   { background-color: rgba(108,117,125,0.08) !important; }
body.dark-mode tr.plan-estandar { background-color: rgba(13,110,253,0.08)  !important; }
body.dark-mode tr.plan-premium  { background-color: rgba(255,193,7,0.08)   !important; }

body.dark-mode .card-footer {
    background: #111f18 !important;
    border-color: #1e3329 !important;
    color: #adb5bd !important;
}
body.dark-mode .input-group-text { background:#1a3329; border-color:#1e3329; color:#e0e0e0; }
body.dark-mode .form-control, body.dark-mode .form-select {
    background:#111f18; border-color:#1e3329; color:#e0e0e0;
}
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-users me-2 text-success"></i>Clientes
        </h4>
        <small class="text-muted">Gestión de clientes y suscripciones</small>
    </div>
    <a href="/Clientes/Registry" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nuevo Cliente
    </a>
</div>

<!-- Flash messages -->
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

<!-- Leyenda de planes -->
<div class="d-flex gap-3 mb-3 flex-wrap">
    <small class="d-flex align-items-center gap-1">
        <span style="width:14px;height:14px;border-radius:3px;background:#6c757d;display:inline-block;"></span>
        Plan Básico
    </small>
    <small class="d-flex align-items-center gap-1">
        <span style="width:14px;height:14px;border-radius:3px;background:#0d6efd;display:inline-block;"></span>
        Plan Estándar
    </small>
    <small class="d-flex align-items-center gap-1">
        <span style="width:14px;height:14px;border-radius:3px;background:#ffc107;display:inline-block;"></span>
        Plan Premium
    </small>
</div>

<!-- Buscador -->
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
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold mb-1">Plan</label>
                <select class="form-select" id="filtroPlan">
                    <option value="">Todos</option>
                    <option value="plan-basico">Básico</option>
                    <option value="plan-estandar">Estándar</option>
                    <option value="plan-premium">Premium</option>
                </select>
            </div>
            <div class="col-md-2 d-flex justify-content-end">
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
        <span><i class="fas fa-table me-2"></i>Lista de Clientes</span>
        <span class="badge bg-light text-dark" id="contador"><?= count($clientes) ?> registros</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tablaClientes">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Cliente</th>
                        <th>Empresa</th>
                        <th>Plan</th>
                        <th>Vencimiento</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-users fa-3x mb-3 d-block opacity-25"></i>
                            No hay clientes registrados.
                            <br>
                            <a href="/Clientes/Registry" class="btn btn-primary btn-sm mt-3">
                                <i class="fas fa-plus me-1"></i>Agregar primer cliente
                            </a>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($clientes as $i => $c):
                        $p   = explode(' ', $c['nombre']);
                        $ini = strtoupper(substr($p[0],0,1).(isset($p[1])?substr($p[1],0,1):''));

                        // Clase CSS según el plan
                        $planNombre = strtolower($c['plan_nombre'] ?? '');
                        $planClase  = '';
                        if (str_contains($planNombre, 'básico') || str_contains($planNombre, 'basico')) {
                            $planClase = 'plan-basico';
                        } elseif (str_contains($planNombre, 'estándar') || str_contains($planNombre, 'estandar')) {
                            $planClase = 'plan-estandar';
                        } elseif (str_contains($planNombre, 'premium')) {
                            $planClase = 'plan-premium';
                        }
                    ?>
                    <tr class="<?= $planClase ?>"
                        data-estado="<?= htmlspecialchars($c['suscripcion_estado'] ?? '') ?>"
                        data-plan="<?= $planClase ?>"
                        data-buscar="<?= strtolower(htmlspecialchars($c['nombre'].' '.$c['email'].' '.($c['empresa_nombre']??''))) ?>">
                        <td class="text-muted"><?= str_pad($i+1,3,'0',STR_PAD_LEFT) ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle"><?= $ini ?></div>
                                <div>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($c['nombre']) ?></p>
                                    <small class="text-muted"><?= htmlspecialchars($c['email']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($c['empresa_nombre'] ?? '—') ?></td>
                        <td>
                            <?php if ($c['plan_nombre']): ?>
                                <?php
                                $badgeColor = match(true) {
                                    str_contains($planNombre,'premium')  => 'warning text-dark',
                                    str_contains($planNombre,'estándar'),
                                    str_contains($planNombre,'estandar') => 'primary',
                                    default                              => 'secondary',
                                };
                                ?>
                                <span class="badge bg-<?= $badgeColor ?>">
                                    <?= htmlspecialchars($c['plan_nombre']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">Sin plan</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $c['fecha_vencimiento']
                                ? date('d/m/Y', strtotime($c['fecha_vencimiento']))
                                : '<span class="text-muted">—</span>' ?>
                        </td>
                        <td>
                            <?php
                            $est = $c['suscripcion_estado'] ?? '';
                            $b   = [
                                'activa'     => '<span class="badge-activa"><span class="status-active-pulse me-1"></span>Activa</span>',
                                'por_vencer' => '<span class="badge-por-vencer"><i class="fas fa-clock me-1"></i>Por vencer</span>',
                                'vencida'    => '<span class="badge-vencida"><i class="fas fa-times-circle me-1"></i>Vencida</span>',
                                'suspendida' => '<span class="badge-suspendida"><i class="fas fa-pause-circle me-1"></i>Suspendida</span>',
                            ];
                            echo $b[$est] ?? '<span class="text-muted">Sin suscripción</span>';
                            ?>
                        </td>
                        <td class="text-center">
                            <a href="/Clientes/Registry/<?= $c['id'] ?>"
                               class="btn btn-sm btn-outline-warning me-1" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="eliminar(<?= $c['id'] ?>, '<?= htmlspecialchars($c['nombre'], ENT_QUOTES) ?>')"
                                    title="Desactivar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($clientes)): ?>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Total: <strong><?= count($clientes) ?></strong> clientes activos</small>
        <small class="text-muted">DeskCod</small>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const buscador   = document.getElementById('buscador');
const filtro     = document.getElementById('filtroEstado');
const filtroPlan = document.getElementById('filtroPlan');

function filtrar() {
    const txt    = buscador.value.toLowerCase();
    const estado = filtro.value;
    const plan   = filtroPlan.value;
    let visibles = 0;

    document.querySelectorAll('#tablaClientes tbody tr[data-buscar]').forEach(fila => {
        const okTxt    = fila.dataset.buscar.includes(txt);
        const okEstado = !estado || fila.dataset.estado === estado;
        const okPlan   = !plan   || fila.dataset.plan   === plan;
        const ok = okTxt && okEstado && okPlan;
        fila.style.display = ok ? '' : 'none';
        if (ok) visibles++;
    });

    document.getElementById('contador').textContent = visibles + ' registros';
}

function limpiarFiltros() {
    buscador.value   = '';
    filtro.value     = '';
    filtroPlan.value = '';
    document.querySelectorAll('#tablaClientes tbody tr').forEach(r => r.style.display = '');
    document.getElementById('contador').textContent =
        document.querySelectorAll('#tablaClientes tbody tr[data-buscar]').length + ' registros';
}

buscador.addEventListener('input', filtrar);
filtro.addEventListener('change', filtrar);
filtroPlan.addEventListener('change', filtrar);

function eliminar(id, nombre) {
    Swal.fire({
        title: '¿Desactivar cliente?',
        html: `<strong>${nombre}</strong> será desactivado.<br>Su historial se conservará.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#005C3E',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (!result.isConfirmed) return;
        const fd = new FormData();
        fd.append('id', id);
        fetch('/Clientes/desactivar', {
            method: 'POST', body: fd, credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ title:'¡Desactivado!', text:data.message,
                    icon:'success', confirmButtonColor:'#005C3E'
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        });
    });
}
</script>