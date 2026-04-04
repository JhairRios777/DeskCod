<!-- Views/Clientes/index.php -->

<style>
tr.plan-basico   td:first-child { border-left: 4px solid #6c757d !important; }
tr.plan-estandar td:first-child { border-left: 4px solid #0d6efd !important; }
tr.plan-premium  td:first-child { border-left: 4px solid #ffc107 !important; }
tr.plan-basico   { background-color: rgba(108,117,125,0.05) !important; }
tr.plan-estandar { background-color: rgba(13,110,253,0.05)  !important; }
tr.plan-premium  { background-color: rgba(255,193,7,0.07)   !important; }
html.dark-mode tr.plan-basico   { background-color: rgba(108,117,125,0.08) !important; }
html.dark-mode tr.plan-estandar { background-color: rgba(13,110,253,0.08)  !important; }
html.dark-mode tr.plan-premium  { background-color: rgba(255,193,7,0.08)   !important; }

/* Token */
.token-preview {
    font-family: 'Courier New', monospace;
    font-size: .72rem; letter-spacing: .02em;
    color: #005C3E; cursor: pointer;
    white-space: nowrap;
}
html.dark-mode .token-preview { color: #00E676; }
.btn-copiar-token {
    border: none; background: transparent;
    color: #6c757d; padding: 0 4px; font-size: .75rem;
    cursor: pointer; vertical-align: middle;
}
.btn-copiar-token:hover { color: #005C3E; }
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="fas fa-users me-2 text-success"></i>Clientes</h4>
        <small class="text-muted">Gestión de clientes y suscripciones</small>
    </div>
    <a href="/Clientes/Registry" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nuevo Cliente
    </a>
</div>

<?php if (!empty($flash_success)): ?>
<div class="alert alert-success d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-check-circle fa-lg"></i><span><?= htmlspecialchars($flash_success) ?></span>
</div>
<?php endif; ?>
<?php if (!empty($flash_error)): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-times-circle fa-lg"></i><span><?= htmlspecialchars($flash_error) ?></span>
</div>
<?php endif; ?>

<!-- Leyenda de planes -->
<div class="d-flex gap-3 mb-3 flex-wrap">
    <small class="d-flex align-items-center gap-1">
        <span style="width:14px;height:14px;border-radius:3px;background:#6c757d;display:inline-block;"></span>Plan Básico
    </small>
    <small class="d-flex align-items-center gap-1">
        <span style="width:14px;height:14px;border-radius:3px;background:#0d6efd;display:inline-block;"></span>Plan Estándar
    </small>
    <small class="d-flex align-items-center gap-1">
        <span style="width:14px;height:14px;border-radius:3px;background:#ffc107;display:inline-block;"></span>Plan Premium
    </small>
</div>

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
            <div class="col-md-3">
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
                        <th>Token API</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-users fa-3x mb-3 d-block opacity-25"></i>
                            No hay clientes registrados.<br>
                            <a href="/Clientes/Registry" class="btn btn-primary btn-sm mt-3">
                                <i class="fas fa-plus me-1"></i>Agregar primer cliente
                            </a>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($clientes as $i => $c):
                        $p         = explode(' ', $c['nombre']);
                        $ini       = strtoupper(substr($p[0],0,1).(isset($p[1])?substr($p[1],0,1):''));
                        $planNombre = strtolower($c['plan_nombre'] ?? '');
                        if (str_contains($planNombre,'premium'))                                          $planClase = 'plan-premium';
                        elseif (str_contains($planNombre,'estándar') || str_contains($planNombre,'estandar')) $planClase = 'plan-estandar';
                        else $planClase = $planNombre ? 'plan-basico' : '';
                        $tienelogo  = !empty($c['logo']) && file_exists(ROOT . $c['logo']);
                        $token      = $tokens[$c['id']] ?? null;
                        $tokenCorto = $token ? substr($token, 0, 8) . '...' . substr($token, -4) : null;
                    ?>
                    <tr class="<?= $planClase ?>"
                        data-estado="<?= htmlspecialchars($c['suscripcion_estado'] ?? '') ?>"
                        data-plan="<?= $planClase ?>"
                        data-buscar="<?= strtolower(htmlspecialchars($c['nombre'].' '.$c['email'].' '.($c['empresa_nombre']??''))) ?>">

                        <td><?= str_pad($i+1,3,'0',STR_PAD_LEFT) ?></td>

                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle">
                                    <?php if ($tienelogo): ?>
                                        <img src="/<?= htmlspecialchars($c['logo']) ?>" alt="logo">
                                    <?php else: ?>
                                        <?= $ini ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($c['nombre']) ?></p>
                                    <small class="text-muted"><?= htmlspecialchars($c['email']) ?></small>
                                </div>
                            </div>
                        </td>

                        <td><?= htmlspecialchars($c['empresa_nombre'] ?? '—') ?></td>

                        <td>
                            <?php if ($c['plan_nombre']): ?>
                            <?php $bc = match(true) {
                                str_contains($planNombre,'premium')                                          => 'warning text-dark',
                                str_contains($planNombre,'estándar') || str_contains($planNombre,'estandar') => 'primary',
                                default => 'secondary',
                            }; ?>
                            <span class="badge bg-<?= $bc ?>"><?= htmlspecialchars($c['plan_nombre']) ?></span>
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
                            $b = [
                                'activa'     => '<span class="badge-activa"><span class="status-active-pulse me-1"></span>Activa</span>',
                                'por_vencer' => '<span class="badge-por-vencer"><i class="fas fa-clock me-1"></i>Por vencer</span>',
                                'vencida'    => '<span class="badge-vencida"><i class="fas fa-times-circle me-1"></i>Vencida</span>',
                                'suspendida' => '<span class="badge-suspendida"><i class="fas fa-pause-circle me-1"></i>Suspendida</span>',
                            ];
                            echo $b[$est] ?? '<span class="text-muted">Sin suscripción</span>';
                            ?>
                        </td>

                        <!-- Columna Token API -->
                        <td>
                            <?php if ($token): ?>
                            <div class="d-flex align-items-center gap-1">
                                <span class="token-preview" title="Clic para ver token completo"
                                      onclick="verToken('<?= htmlspecialchars($token, ENT_QUOTES) ?>', '<?= htmlspecialchars($c['nombre'], ENT_QUOTES) ?>')">
                                    <i class="fas fa-key me-1"></i><?= $tokenCorto ?>
                                </span>
                                <button class="btn-copiar-token"
                                        onclick="copiarToken('<?= htmlspecialchars($token, ENT_QUOTES) ?>', this)"
                                        title="Copiar token">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <?php else: ?>
                            <span class="text-muted small">
                                <i class="fas fa-exclamation-circle me-1"></i>Sin token
                            </span>
                            <?php endif; ?>
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
// ── Filtros ──
const buscador   = document.getElementById('buscador');
const filtro     = document.getElementById('filtroEstado');
const filtroPlan = document.getElementById('filtroPlan');

function filtrar() {
    const txt = buscador.value.toLowerCase();
    const est = filtro.value;
    const pl  = filtroPlan.value;
    let v = 0;
    document.querySelectorAll('#tablaClientes tbody tr[data-buscar]').forEach(f => {
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
    document.querySelectorAll('#tablaClientes tbody tr').forEach(r => r.style.display = '');
    document.getElementById('contador').textContent =
        document.querySelectorAll('#tablaClientes tbody tr[data-buscar]').length + ' registros';
}

buscador.addEventListener('input', filtrar);
filtro.addEventListener('change', filtrar);
filtroPlan.addEventListener('change', filtrar);

// ── Token API ──
function copiarToken(token, btn) {
    navigator.clipboard.writeText(token).then(() => {
        const icon = btn.querySelector('i');
        icon.classList.replace('fa-copy', 'fa-check');
        btn.style.color = '#005C3E';
        setTimeout(() => {
            icon.classList.replace('fa-check', 'fa-copy');
            btn.style.color = '';
        }, 2000);
    });
}

function verToken(token, nombre) {
    Swal.fire({
        title: `Token API — ${nombre}`,
        html: `
            <p class="text-muted small mb-3">
                Usa este token en el header <code>Authorization: Bearer {token}</code>
            </p>
            <div class="input-group">
                <input type="text" class="form-control form-control-sm font-monospace"
                       id="swalToken" value="${token}" readonly
                       style="font-size:.72rem;">
                <button class="btn btn-outline-secondary btn-sm" onclick="copiarDesdeModal()">
                    <i class="fas fa-copy" id="swalCopyIcon"></i>
                </button>
            </div>
        `,
        confirmButtonColor: '#005C3E',
        confirmButtonText: 'Cerrar',
        width: 560,
    });
}

function copiarDesdeModal() {
    const input = document.getElementById('swalToken');
    navigator.clipboard.writeText(input.value).then(() => {
        const icon = document.getElementById('swalCopyIcon');
        icon.classList.replace('fa-copy', 'fa-check');
        setTimeout(() => icon.classList.replace('fa-check', 'fa-copy'), 2000);
    });
}

// ── Desactivar ──
function eliminar(id, nombre) {
    Swal.fire({
        title: '¿Desactivar cliente?',
        html: `<strong>${nombre}</strong> será desactivado.<br>Su historial se conservará.`,
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#005C3E', cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, desactivar', cancelButtonText: 'Cancelar'
    }).then(r => {
        if (!r.isConfirmed) return;
        const fd = new FormData();
        fd.append('id', id);
        fetch('/Clientes/desactivar', { method:'POST', body:fd, credentials:'same-origin' })
            .then(r => r.json())
            .then(d => {
                Swal.fire({
                    icon: d.success ? 'success' : 'error',
                    title: d.success ? '¡Desactivado!' : 'Error',
                    text: d.message, confirmButtonColor: '#005C3E'
                }).then(() => { if (d.success) location.reload(); });
            });
    });
}
</script>