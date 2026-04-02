<!-- Views/Home/index.php — Dashboard DeskCod -->

<style>
/* ══════════════════════════════════════════════
   ESTILOS EXCLUSIVOS DEL DASHBOARD
   ══════════════════════════════════════════════ */

.metric-icon {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; flex-shrink: 0;
}

.bg-primary-soft { background: rgba(0, 92, 62, 0.1);   color: #005C3E; }
.bg-accent-soft  { background: rgba(0, 230, 118, 0.15); color: #007a52; }
.bg-warning-soft { background: rgba(255, 193, 7, 0.15); color: #b38600; }
.bg-danger-soft  { background: rgba(220, 53, 69, 0.1);  color: #dc3545; }
.bg-info-soft    { background: rgba(13, 202, 240, 0.1); color: #087990; }

.metric-label { font-size: .8rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .05em; }
.metric-value { font-size: 1.8rem; font-weight: 700; color: #005C3E; }

.avatar-circle {
    width: 44px; height: 44px; border-radius: 50%;
    background: linear-gradient(135deg, #005C3E, #00E676);
    color: #fff; display: flex; align-items: center;
    justify-content: center; font-weight: 700; font-size: .85rem; flex-shrink: 0;
}
.avatar-sm { width: 34px; height: 34px; font-size: .75rem; }

.activity-list { list-style: none; padding: 0; margin: 0; }
.activity-item {
    display: flex; align-items: flex-start; gap: .75rem;
    padding: .75rem 1rem; border-bottom: 1px solid var(--border-color); font-size: .875rem;
}
.activity-item:last-child { border-bottom: none; }
.activity-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; margin-top: 5px; }
.dot-success { background: #00E676; }
.dot-warning { background: #ffc107; }
.dot-primary { background: #005C3E; }
.dot-danger  { background: #dc3545; }

.welcome-card {
    background: linear-gradient(135deg, #005C3E 0%, #007a52 50%, #00E676 100%);
    border-radius: 16px; color: white; padding: 1.5rem 2rem;
    position: relative; overflow: hidden;
}

.welcome-card::before {
    content: '';
    position: absolute; top: -50%; right: -10%;
    width: 300px; height: 300px; border-radius: 50%;
    background: rgba(255,255,255,0.06);
}

.welcome-card::after {
    content: '';
    position: absolute; bottom: -60%; right: 10%;
    width: 200px; height: 200px; border-radius: 50%;
    background: rgba(255,255,255,0.04);
}

.bg-accent { background-color: #00E676 !important; }
.text-accent-deskcod { color: #00E676 !important; }
.card-header.bg-primary { color: #ffffff !important; }

/* ── TABLA HOVER TEMA CLARO ── */
.table-hover tbody tr:hover { background: rgba(0, 92, 62, 0.06); }
.table-hover > tbody > tr:hover > :not(caption) > * > *,
.table-hover > tbody > tr:hover > * {
    background-color: rgba(0, 92, 62, 0.06) !important;
    color: #212529 !important;
    --bs-table-bg-state: rgba(0, 92, 62, 0.06) !important;
}
.table-hover > tbody > tr:hover small,
.table-hover > tbody > tr:hover .text-muted { color: #6c757d !important; }

/* ── MODO OSCURO ── */
body.dark-mode .metric-label { color: #adb5bd; }
body.dark-mode .metric-value { color: #00E676; }
body.dark-mode .activity-item { border-color: #1e3329; }
body.dark-mode .activity-item p { color: #e0e0e0; }

body.dark-mode .table {
    --bs-table-color: #e0e0e0; --bs-table-bg: transparent;
    --bs-table-border-color: #1e3329; --bs-table-hover-color: #e0e0e0;
    --bs-table-hover-bg: rgba(0,230,118,0.07); color: #e0e0e0 !important;
}
body.dark-mode .table > :not(caption) > * > * {
    color: #e0e0e0 !important; background-color: transparent !important;
    border-bottom-color: #1e3329 !important;
}
body.dark-mode .table thead > tr > * {
    color: #fff !important; background-color: #1a3329 !important; border-color: #1e3329 !important;
}
body.dark-mode .table p,
body.dark-mode .table .fw-semibold,
body.dark-mode .table .fw-bold { color: #fff !important; }
body.dark-mode .table small,
body.dark-mode .table .text-muted { color: #adb5bd !important; }
body.dark-mode .table-hover > tbody > tr:hover > :not(caption) > * > *,
body.dark-mode .table-hover > tbody > tr:hover > * {
    background-color: rgba(0,230,118,0.09) !important;
    color: #e0e0e0 !important;
    --bs-table-bg-state: rgba(0,230,118,0.09) !important;
}
body.dark-mode .card-footer {
    background: #111f18 !important; border-color: #1e3329 !important; color: #adb5bd !important;
}
body.dark-mode .progress { background: #1e3329; }
body.dark-mode .welcome-card { box-shadow: 0 8px 32px rgba(0,0,0,0.4); }
</style>


<!-- ============================================
     KPIs — DATOS REALES DESDE LA BD
     ============================================ -->
<div class="row g-3 mb-4">



    <div class="col-xl-3 col-md-6">
        <div class="card card-metric shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="metric-icon bg-primary-soft">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="metric-label mb-0">Total Clientes</p>
                    <h3 class="metric-value mb-0">
                        <?= number_format($metricas['total_clientes'] ?? 0) ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-metric shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="metric-icon bg-accent-soft">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div>
                    <p class="metric-label mb-0">Suscripciones Activas</p>
                    <h3 class="metric-value mb-0">
                        <?= number_format($metricas['suscripciones_activas'] ?? 0) ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-metric shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="metric-icon bg-warning-soft">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div>
                    <p class="metric-label mb-0">Tickets Abiertos</p>
                    <h3 class="metric-value mb-0">
                        <?= number_format($metricas['tickets_abiertos'] ?? 0) ?>
                    </h3>
                    <?php if(($metricas['tickets_abiertos'] ?? 0) > 0): ?>
                        <small class="text-warning fw-semibold">
                            <i class="fas fa-exclamation-circle me-1"></i>Requieren atención
                        </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-metric shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="metric-icon bg-danger-soft">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div>
                    <p class="metric-label mb-0">Ingresos del Mes</p>
                    <h3 class="metric-value mb-0">
                        L. <?= number_format($metricas['ingresos_mes'] ?? 0, 2) ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ============================================
     ALERTAS DINÁMICAS
     ============================================ -->
<?php if(($metricas['suscripciones_por_vencer'] ?? 0) > 0): ?>
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-0 shadow-sm">
            <i class="fas fa-exclamation-triangle fa-lg"></i>
            <span>
                <strong>Atención:</strong>
                Hay <?= $metricas['suscripciones_por_vencer'] ?> suscripción(es) próximas a vencer.
                <a href="/Suscripciones/por-vencer" class="alert-link ms-1">Ver ahora →</a>
            </span>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ============================================
     GRÁFICAS
     ============================================ -->
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
                <canvas id="chartSuscripciones"></canvas>
                <div class="mt-3 w-100">
                    <div class="d-flex justify-content-between mb-1">
                        <small><span class="badge" style="background:#005C3E">●</span> Activas</small>
                        <small class="fw-semibold"><?= $metricas['suscripciones_activas'] ?? 0 ?></small>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small><span class="badge bg-warning text-dark">●</span> Por vencer</small>
                        <small class="fw-semibold"><?= $metricas['suscripciones_por_vencer'] ?? 0 ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ============================================
     ACTIVIDAD RECIENTE + ACCESOS RÁPIDOS
     ============================================ -->
<div class="row g-3 mb-4">

    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary d-flex align-items-center justify-content-between">
                <span><i class="fas fa-clock me-2"></i>Actividad Reciente</span>
                <small class="text-white opacity-75">Últimas acciones</small>
            </div>
            <div class="card-body p-0">
                <ul class="activity-list">
                    <li class="activity-item">
                        <span class="activity-dot dot-success"></span>
                        <div>
                            <p class="mb-0 fw-semibold">Sistema iniciado correctamente</p>
                            <small class="text-muted">hace un momento</small>
                        </div>
                    </li>
                    <li class="activity-item">
                        <span class="activity-dot dot-primary"></span>
                        <div>
                            <p class="mb-0 fw-semibold">
                                <?= htmlspecialchars($userName ?? 'Usuario') ?> inició sesión
                            </p>
                            <small class="text-muted">Ahora mismo</small>
                        </div>
                    </li>
                    <?php if(($metricas['tickets_abiertos'] ?? 0) > 0): ?>
                    <li class="activity-item">
                        <span class="activity-dot dot-warning"></span>
                        <div>
                            <p class="mb-0 fw-semibold">
                                <?= $metricas['tickets_abiertos'] ?> ticket(s) pendientes de atención
                            </p>
                            <small class="text-muted">
                                <a href="/Tickets" class="text-warning">Ver tickets →</a>
                            </small>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if(($metricas['suscripciones_por_vencer'] ?? 0) > 0): ?>
                    <li class="activity-item">
                        <span class="activity-dot dot-danger"></span>
                        <div>
                            <p class="mb-0 fw-semibold">
                                <?= $metricas['suscripciones_por_vencer'] ?> suscripción(es) por vencer
                            </p>
                            <small class="text-muted">
                                <a href="/Suscripciones/por-vencer" class="text-danger">Revisar →</a>
                            </small>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary">
                <i class="fas fa-bolt me-2"></i>Accesos Rápidos
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="/Clientes" class="btn btn-outline-success text-start">
                    <i class="fas fa-user-plus me-2"></i>Nuevo Cliente
                </a>
                <a href="/Tickets" class="btn btn-outline-warning text-start">
                    <i class="fas fa-ticket-alt me-2"></i>Ver Tickets
                </a>
                <a href="/Suscripciones" class="btn btn-outline-primary text-start">
                    <i class="fas fa-sync-alt me-2"></i>Suscripciones
                </a>
                <a href="/Pagos" class="btn btn-outline-danger text-start">
                    <i class="fas fa-credit-card me-2"></i>Pagos
                </a>
                <a href="/Reportes" class="btn btn-outline-secondary text-start">
                    <i class="fas fa-chart-bar me-2"></i>Reportes
                </a>
            </div>
        </div>
    </div>

</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Gráfica de línea — Ingresos mensuales ──
    const ctxLine = document.getElementById('chartIngresos');
    if (ctxLine) {
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
                datasets: [{
                    label: 'Ingresos (L.)',
                    // Estos datos serán reales cuando implementes el SP de ingresos mensuales
                    data: [0,0,0,0,0,0,0,0,0,0,0,<?= number_format($metricas['ingresos_mes'] ?? 0, 2, '.', '') ?>],
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
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { callback: v => 'L.' + v.toLocaleString() }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // ── Gráfica de dona — Estado suscripciones ──
    const ctxPie = document.getElementById('chartSuscripciones');
    if (ctxPie) {
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Activas','Por vencer'],
                datasets: [{
                    data: [
                        <?= (int)($metricas['suscripciones_activas']    ?? 0) ?>,
                        <?= (int)($metricas['suscripciones_por_vencer'] ?? 0) ?>
                    ],
                    backgroundColor: ['#005C3E','#ffc107'],
                    borderWidth: 2, borderColor: '#fff', hoverOffset: 8
                }]
            },
            options: {
                responsive: true, cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>