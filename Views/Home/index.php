<!-- Views/Home/index.php — Dashboard DeskCod -->

<style>
.metric-icon {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; flex-shrink: 0;
}
.bg-primary-soft { background: rgba(0,92,62,0.1);    color: #005C3E; }
.bg-accent-soft  { background: rgba(0,230,118,0.15); color: #007a52; }
.bg-warning-soft { background: rgba(255,193,7,0.15); color: #b38600; }
.bg-danger-soft  { background: rgba(220,53,69,0.1);  color: #dc3545; }
.bg-info-soft    { background: rgba(13,202,240,0.1); color: #087990; }

.metric-label { font-size:.8rem; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.05em; }
.metric-value { font-size:1.8rem; font-weight:700; color:#005C3E; }

.activity-list { list-style:none; padding:0; margin:0; }
.activity-item {
    display:flex; align-items:flex-start; gap:.75rem;
    padding:.75rem 1rem; border-bottom:1px solid var(--border-color); font-size:.875rem;
}
.activity-item:last-child { border-bottom:none; }
.activity-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; margin-top:5px; }
.dot-success { background:#00E676; }
.dot-warning { background:#ffc107; }
.dot-primary { background:#005C3E; }
.dot-danger  { background:#dc3545; }
.dot-info    { background:#0dcaf0; }

.bg-accent { background-color:#00E676 !important; }

html.dark-mode .metric-label { color:#adb5bd; }
html.dark-mode .metric-value { color:#00E676; }
html.dark-mode .activity-item { border-color:#1e3329; }
html.dark-mode .activity-item p { color:#e0e0e0; }
html.dark-mode .card-footer { background:#111f18 !important; border-color:#1e3329 !important; }
</style>

<?php
// ── Helpers de actividad ──────────────────────
function dashDot(string $accion): string {
    if (str_contains($accion,'LOGIN'))    return 'dot-success';
    if (str_contains($accion,'CREADO'))   return 'dot-primary';
    if (str_contains($accion,'ACTUALIZ')) return 'dot-info';
    if (str_contains($accion,'DESACTIV')) return 'dot-danger';
    if (str_contains($accion,'PAGO'))     return 'dot-warning';
    return 'dot-primary';
}

function dashLabel(string $accion, string $tabla): string {
    $map = [
        'LOGIN_OK'             => 'Inicio de sesión',
        'LOGOUT'               => 'Cierre de sesión',
        'CLIENTE_CREADO'       => 'Cliente creado',
        'CLIENTE_ACTUALIZADO'  => 'Cliente actualizado',
        'CLIENTE_DESACTIVADO'  => 'Cliente desactivado',
        'SUSCRIPCION_CREADA'   => 'Suscripción creada',
        'SUSCRIPCION_SUSPENDIDA'=> 'Suscripción suspendida',
        'SUSCRIPCION_REACTIVADA'=> 'Suscripción reactivada',
        'TICKET_CREADO'        => 'Ticket creado',
        'TICKET_COMENTARIO'    => 'Comentario en ticket',
        'TICKET_ESTADO_CAMBIADO'=> 'Estado de ticket actualizado',
        'PAGO_REGISTRADO'      => 'Pago registrado',
        'CUENTA_CREADA'        => 'Cuenta por cobrar creada',
        'EMPLEADO_CREADO'      => 'Empleado creado',
        'EMPLEADO_ACTUALIZADO' => 'Empleado actualizado',
        'PLAN_CREADO'          => 'Plan creado',
        'PLAN_ACTUALIZADO'     => 'Plan actualizado',
        'PERFIL_ACTUALIZADO'   => 'Perfil actualizado',
        'PASSWORD_CAMBIADO'    => 'Contraseña cambiada',
        'FOTO_ACTUALIZADA'     => 'Foto de perfil actualizada',
    ];
    return $map[$accion] ?? ucfirst(strtolower(str_replace('_', ' ', $accion)));
}

function dashTiempo(string $fecha): string {
    $diff = time() - strtotime($fecha);
    if ($diff < 60)     return 'hace un momento';
    if ($diff < 3600)   return 'hace ' . floor($diff/60) . ' min';
    if ($diff < 86400)  return 'hace ' . floor($diff/3600) . ' h';
    if ($diff < 604800) return 'hace ' . floor($diff/86400) . ' días';
    return date('d/m/Y', strtotime($fecha));
}
?>

<!-- ── KPIs ─────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card card-metric shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="metric-icon bg-primary-soft"><i class="fas fa-users"></i></div>
                <div>
                    <p class="metric-label mb-0">Total Clientes</p>
                    <h3 class="metric-value mb-0"><?= number_format($metricas['total_clientes'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-metric shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="metric-icon bg-accent-soft"><i class="fas fa-sync-alt"></i></div>
                <div>
                    <p class="metric-label mb-0">Suscripciones Activas</p>
                    <h3 class="metric-value mb-0"><?= number_format($metricas['suscripciones_activas'] ?? 0) ?></h3>
                    <?php if(($metricas['suscripciones_por_vencer'] ?? 0) > 0): ?>
                    <small class="text-warning fw-semibold">
                        <i class="fas fa-clock me-1"></i><?= $metricas['suscripciones_por_vencer'] ?> por vencer
                    </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-metric shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="metric-icon bg-warning-soft"><i class="fas fa-ticket-alt"></i></div>
                <div>
                    <p class="metric-label mb-0">Tickets Abiertos</p>
                    <h3 class="metric-value mb-0"><?= number_format($metricas['tickets_abiertos'] ?? 0) ?></h3>
                    <?php if(($metricas['tickets_abiertos'] ?? 0) > 0): ?>
                    <small class="text-warning fw-semibold"><i class="fas fa-exclamation-circle me-1"></i>Requieren atención</small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-metric shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="metric-icon bg-danger-soft"><i class="fas fa-dollar-sign"></i></div>
                <div>
                    <p class="metric-label mb-0">Ingresos del Mes</p>
                    <h3 class="metric-value mb-0">L. <?= number_format($metricas['ingresos_mes'] ?? 0, 2) ?></h3>
                    <?php if(($metricas['saldo_pendiente'] ?? 0) > 0): ?>
                    <small class="text-danger fw-semibold">
                        L. <?= number_format($metricas['saldo_pendiente'], 2) ?> pendiente
                    </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Alerta suscripciones por vencer ───────── -->
<?php if(($metricas['suscripciones_por_vencer'] ?? 0) > 0): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4 shadow-sm">
    <i class="fas fa-exclamation-triangle fa-lg"></i>
    <span>
        <strong>Atención:</strong>
        <?= $metricas['suscripciones_por_vencer'] ?> suscripción(es) vencen en los próximos 30 días.
        <a href="/Suscripciones" class="alert-link ms-1">Ver ahora →</a>
    </span>
</div>
<?php endif; ?>

<!-- ── Gráfica + dona ─────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary d-flex align-items-center justify-content-between">
                <span><i class="fas fa-chart-line me-2"></i>Ingresos Mensuales</span>
                <span class="badge bg-accent text-dark"><?= date('Y') ?></span>
            </div>
            <div class="card-body">
                <canvas id="chartIngresos" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary">
                <i class="fas fa-chart-pie me-2"></i>Estado Suscripciones
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <canvas id="chartSuscripciones" style="max-width:180px;"></canvas>
                <div class="mt-3 w-100">
                    <div class="d-flex justify-content-between mb-1">
                        <small><span class="badge" style="background:#005C3E">●</span> Activas</small>
                        <small class="fw-semibold"><?= $metricas['suscripciones_activas'] ?? 0 ?></small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small><span class="badge bg-warning text-dark">●</span> Por vencer</small>
                        <small class="fw-semibold"><?= $metricas['suscripciones_por_vencer'] ?? 0 ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Actividad real + próximas a vencer ─────── -->
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary d-flex align-items-center justify-content-between">
                <span><i class="fas fa-clock me-2"></i>Actividad Reciente</span>
                <small class="text-white opacity-75">Últimas 8 acciones</small>
            </div>
            <div class="card-body p-0">
                <ul class="activity-list">
                    <?php if (empty($actividad)): ?>
                    <li class="activity-item">
                        <span class="activity-dot dot-success"></span>
                        <div>
                            <p class="mb-0 fw-semibold">Sin actividad registrada aún</p>
                            <small class="text-muted">El historial aparecerá aquí</small>
                        </div>
                    </li>
                    <?php else: ?>
                    <?php foreach ($actividad as $a): ?>
                    <li class="activity-item">
                        <span class="activity-dot <?= dashDot($a['accion']) ?>"></span>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-semibold"><?= dashLabel($a['accion'], $a['tabla']) ?></p>
                            <small class="text-muted">
                                <?= htmlspecialchars($a['empleado_nombre']) ?>
                                · <?= dashTiempo($a['created_at']) ?>
                            </small>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary d-flex align-items-center justify-content-between">
                <span><i class="fas fa-clock me-2"></i>Próximas a Vencer</span>
                <a href="/Suscripciones" class="btn btn-accent btn-sm">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($proximasAVencer)): ?>
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-check-circle fa-2x mb-2 d-block text-success opacity-50"></i>
                    Sin vencimientos próximos
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-sm">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Plan</th>
                                <th>Días</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($proximasAVencer as $s):
                                $d = (int)$s['dias_restantes'];
                                $cls = $d <= 7 ? 'text-danger' : ($d <= 15 ? 'text-warning' : 'text-success');
                            ?>
                            <tr>
                                <td>
                                    <p class="mb-0 fw-semibold small"><?= htmlspecialchars($s['cliente_nombre']) ?></p>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($s['fecha_vencimiento'])) ?></small>
                                </td>
                                <td><span class="badge bg-secondary small"><?= htmlspecialchars($s['plan_nombre']) ?></span></td>
                                <td>
                                    <span class="fw-bold <?= $cls ?>">
                                        <?= $d === 0 ? 'HOY' : $d.'d' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ── Accesos rápidos ────────────────────────── -->
<div class="row g-3">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary">
                <i class="fas fa-bolt me-2"></i>Accesos Rápidos
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="/Clientes/Registry" class="btn btn-outline-success">
                        <i class="fas fa-user-plus me-2"></i>Nuevo Cliente
                    </a>
                    <a href="/Tickets" class="btn btn-outline-warning">
                        <i class="fas fa-ticket-alt me-2"></i>Ver Tickets
                    </a>
                    <a href="/Suscripciones" class="btn btn-outline-primary">
                        <i class="fas fa-sync-alt me-2"></i>Suscripciones
                    </a>
                    <a href="/Pagos/Registry" class="btn btn-outline-danger">
                        <i class="fas fa-credit-card me-2"></i>Registrar Pago
                    </a>
                    <a href="/Reportes" class="btn btn-outline-secondary">
                        <i class="fas fa-chart-bar me-2"></i>Reportes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark-mode');
    const gridColor  = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)';
    const tickColor  = isDark ? '#adb5bd' : '#6c757d';

    // Datos de ingresos desde PHP → JS
    const ingresosMes = <?= json_encode($ingresosMes) ?>;

    // ── Gráfica de línea — Ingresos mensuales ──
    const ctxLine = document.getElementById('chartIngresos');
    if (ctxLine) {
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
                datasets: [{
                    label: 'Ingresos (L.)',
                    data: ingresosMes,
                    borderColor: '#005C3E',
                    backgroundColor: 'rgba(0,230,118,0.08)',
                    borderWidth: 2.5, fill: true, tension: 0.4,
                    pointBackgroundColor: '#00E676',
                    pointRadius: 4, pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: {
                            color: tickColor,
                            callback: v => 'L.' + v.toLocaleString()
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: tickColor }
                    }
                }
            }
        });
    }

    // ── Gráfica de dona — Estado suscripciones ──
    const ctxPie = document.getElementById('chartSuscripciones');
    if (ctxPie) {
        const activas   = <?= (int)($metricas['suscripciones_activas']    ?? 0) ?>;
        const porVencer = <?= (int)($metricas['suscripciones_por_vencer'] ?? 0) ?>;
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Activas','Por vencer'],
                datasets: [{
                    data: [activas, porVencer],
                    backgroundColor: ['#005C3E','#ffc107'],
                    borderWidth: 2,
                    borderColor: isDark ? '#111f18' : '#fff',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true, cutout: '70%',
                plugins: { legend: { display: false } }
            }
        });
    }

    // Re-renderiza gráficas si cambia el tema
    new MutationObserver(() => location.reload())
        .observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});
</script>