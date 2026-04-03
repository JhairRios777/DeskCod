<!-- Views/Reportes/index.php -->

<style>
body.dark-mode .card-footer { background:#111f18 !important; border-color:#1e3329 !important; }
body.dark-mode .form-select, body.dark-mode .form-control {
    background:#111f18; border-color:#1e3329; color:#e0e0e0;
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
body.dark-mode .table .fw-semibold, body.dark-mode .table p { color:#fff !important; }
body.dark-mode .table small, body.dark-mode .table .text-muted { color:#adb5bd !important; }
body.dark-mode .nav-tabs .nav-link { color:#adb5bd; border-color:#1e3329; }
body.dark-mode .nav-tabs .nav-link.active { background:#111f18; color:#00E676; border-color:#1e3329; }

/* Barra de ingreso mensual */
.barra-ingreso { height:32px; border-radius:6px; background:rgba(0,92,62,0.1); overflow:hidden; position:relative; }
.barra-ingreso .fill { height:100%; background:linear-gradient(90deg,#005C3E,#00E676); border-radius:6px; transition:width 0.5s; }
.barra-ingreso .label { position:absolute; right:8px; top:50%; transform:translateY(-50%); font-size:0.78rem; font-weight:600; }
body.dark-mode .barra-ingreso { background:rgba(0,92,62,0.2); }

/* Barra de saldo */
.barra-saldo { height:8px; border-radius:4px; background:#dee2e6; overflow:hidden; }
.barra-saldo .fill { height:100%; background:linear-gradient(90deg,#005C3E,#00E676); border-radius:4px; }
body.dark-mode .barra-saldo { background:#1e3329; }

/* Días restantes badge */
.dias-badge { padding:0.25em 0.6em; border-radius:20px; font-size:0.75rem; font-weight:600; }
.dias-0-7   { background:rgba(220,53,69,0.15);  color:#dc3545; }
.dias-8-15  { background:rgba(255,193,7,0.15);  color:#b38600; }
.dias-16-30 { background:rgba(0,92,62,0.12);    color:#005C3E; }
body.dark-mode .dias-16-30 { color:#00E676; }
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-chart-bar me-2 text-success"></i>Reportes
        </h4>
        <small class="text-muted">Resumen general del sistema</small>
    </div>
    <!-- Filtros globales -->
    <div class="d-flex gap-2 align-items-center">
        <form method="GET" action="/Reportes" class="d-flex gap-2">
            <select name="anio" class="form-select form-select-sm" style="width:90px;"
                    onchange="this.form.submit()">
                <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                <option value="<?= $y ?>" <?= $y === $anio ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <select name="dias" class="form-select form-select-sm" style="width:130px;"
                    onchange="this.form.submit()">
                <option value="7"  <?= $dias===7  ? 'selected':'' ?>>Próx. 7 días</option>
                <option value="15" <?= $dias===15 ? 'selected':'' ?>>Próx. 15 días</option>
                <option value="30" <?= $dias===30 ? 'selected':'' ?>>Próx. 30 días</option>
                <option value="60" <?= $dias===60 ? 'selected':'' ?>>Próx. 60 días</option>
            </select>
        </form>
    </div>
</div>

<!-- Métricas rápidas -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm card-metric">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Ingresos <?= $anio ?></p>
                        <div class="metric-value">$<?= number_format($totalIngresosAnio,2) ?></div>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm card-metric" style="border-left-color:#dc3545 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Saldo pendiente total</p>
                        <div class="metric-value text-danger">$<?= number_format($totalPendiente,2) ?></div>
                    </div>
                    <i class="fas fa-exclamation-circle fa-2x text-danger opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm card-metric" style="border-left-color:#ffc107 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Suscripciones por vencer</p>
                        <div class="metric-value text-warning"><?= $totalPorVencer ?></div>
                    </div>
                    <i class="fas fa-clock fa-2x text-warning opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="reportesTabs">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabIngresos">
            <i class="fas fa-dollar-sign me-2"></i>Ingresos
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabSaldos">
            <i class="fas fa-exclamation-circle me-2"></i>Saldos pendientes
            <?php if (count($saldosPendientes) > 0): ?>
            <span class="badge bg-danger ms-1"><?= count($saldosPendientes) ?></span>
            <?php endif; ?>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabSuscripciones">
            <i class="fas fa-clock me-2"></i>Por vencer
            <?php if ($totalPorVencer > 0): ?>
            <span class="badge bg-warning text-dark ms-1"><?= $totalPorVencer ?></span>
            <?php endif; ?>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabTickets">
            <i class="fas fa-ticket-alt me-2"></i>Tickets
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabEmpleados">
            <i class="fas fa-user-tie me-2"></i>Empleados
        </button>
    </li>
</ul>

<div class="tab-content">

    <!-- ── TAB: Ingresos ── -->
    <div class="tab-pane fade show active" id="tabIngresos">
        <div class="card shadow-sm">
            <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-bar me-2"></i>Ingresos por mes — <?= $anio ?></span>
                <span class="text-white small">Total: <strong>$<?= number_format($totalIngresosAnio,2) ?></strong></span>
            </div>
            <div class="card-body">
                <?php
                $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                $maxIngreso = !empty($ingresosMes) ? max(array_column($ingresosMes,'total_ingresos')) : 1;
                $ingresosPorMes = array_column($ingresosMes, null, 'mes');
                ?>
                <div class="d-flex flex-column gap-2">
                    <?php for ($m = 1; $m <= 12; $m++):
                        $data   = $ingresosPorMes[$m] ?? null;
                        $monto  = $data ? (float)$data['total_ingresos'] : 0;
                        $pagos  = $data ? (int)$data['total_pagos'] : 0;
                        $pct    = $maxIngreso > 0 ? ($monto / $maxIngreso) * 100 : 0;
                        $esMes  = $m === (int)date('m') && $anio === (int)date('Y');
                    ?>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted small fw-semibold" style="width:30px;">
                            <?= $meses[$m-1] ?>
                        </span>
                        <div class="flex-grow-1 barra-ingreso">
                            <div class="fill" style="width:<?= $pct ?>%"></div>
                            <?php if ($monto > 0): ?>
                            <span class="label <?= $pct > 50 ? 'text-white' : '' ?>">
                                $<?= number_format($monto,2) ?>
                                <span class="opacity-75">(<?= $pagos ?> pagos)</span>
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php if ($esMes): ?>
                        <span class="badge bg-success">Actual</span>
                        <?php endif; ?>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ── TAB: Saldos pendientes ── -->
    <div class="tab-pane fade" id="tabSaldos">
        <div class="card shadow-sm">
            <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                <span><i class="fas fa-exclamation-circle me-2"></i>Clientes con Saldo Pendiente</span>
                <span class="badge bg-light text-dark"><?= count($saldosPendientes) ?> clientes</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Cuentas</th>
                                <th>Total deuda</th>
                                <th>Pagado</th>
                                <th>Pendiente</th>
                                <th>Progreso</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($saldosPendientes)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-2 d-block text-success opacity-50"></i>
                                    No hay saldos pendientes
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($saldosPendientes as $s):
                                $pct = $s['total_deuda'] > 0
                                    ? min(100, round(($s['total_pagado'] / $s['total_deuda']) * 100))
                                    : 0;
                            ?>
                            <tr>
                                <td>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($s['cliente_nombre']) ?></p>
                                    <small class="text-muted"><?= htmlspecialchars($s['empresa_nombre'] ?? $s['email']) ?></small>
                                </td>
                                <td><span class="badge bg-secondary"><?= $s['total_cuentas'] ?></span></td>
                                <td>$<?= number_format($s['total_deuda'],2) ?></td>
                                <td class="text-success">$<?= number_format($s['total_pagado'],2) ?></td>
                                <td class="text-danger fw-bold">$<?= number_format($s['saldo_pendiente'],2) ?></td>
                                <td width="150">
                                    <div class="barra-saldo">
                                        <div class="fill" style="width:<?= $pct ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?= $pct ?>% pagado</small>
                                </td>
                                <td class="text-center">
                                    <a href="/Pagos/Registry?cliente=<?= $s['id'] ?>"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus me-1"></i>Abono
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ── TAB: Suscripciones por vencer ── -->
    <div class="tab-pane fade" id="tabSuscripciones">
        <div class="card shadow-sm">
            <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                <span><i class="fas fa-clock me-2"></i>Suscripciones por Vencer — próximos <?= $dias ?> días</span>
                <span class="badge bg-light text-dark"><?= count($suscripcionesPorVencer) ?> suscripciones</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Plan</th>
                                <th>Precio</th>
                                <th>Vence</th>
                                <th>Días restantes</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($suscripcionesPorVencer)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-2 d-block text-success opacity-50"></i>
                                    No hay suscripciones próximas a vencer
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($suscripcionesPorVencer as $s):
                                $d = (int)$s['dias_restantes'];
                                $claseD = $d <= 7 ? 'dias-0-7' : ($d <= 15 ? 'dias-8-15' : 'dias-16-30');
                            ?>
                            <tr>
                                <td>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($s['cliente_nombre']) ?></p>
                                    <small class="text-muted"><?= htmlspecialchars($s['empresa_nombre'] ?? $s['cliente_email']) ?></small>
                                </td>
                                <td>
                                    <?php
                                    $pN = strtolower($s['plan_nombre']);
                                    $bc = str_contains($pN,'premium') ? 'warning text-dark'
                                        : (str_contains($pN,'estándar')||str_contains($pN,'estandar') ? 'primary' : 'secondary');
                                    ?>
                                    <span class="badge bg-<?= $bc ?>"><?= htmlspecialchars($s['plan_nombre']) ?></span>
                                </td>
                                <td class="text-success fw-bold">$<?= number_format($s['plan_precio'],2) ?></td>
                                <td><?= date('d/m/Y', strtotime($s['fecha_vencimiento'])) ?></td>
                                <td>
                                    <span class="dias-badge <?= $claseD ?>">
                                        <?= $d === 0 ? 'Hoy' : $d.' días' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="/Pagos/Registry?cliente=<?= $s['cliente_id'] ?? '' ?>"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-credit-card me-1"></i>Cobrar
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ── TAB: Tickets ── -->
    <div class="tab-pane fade" id="tabTickets">
        <div class="row g-4">
            <?php
            // Organiza por estado
            $porEstado = [];
            $porPrioridad = [];
            foreach ($ticketsResumen as $t) {
                $porEstado[$t['estado']] = ($porEstado[$t['estado']] ?? 0) + $t['total'];
                $porPrioridad[$t['prioridad']] = ($porPrioridad[$t['prioridad']] ?? 0) + $t['total'];
            }
            $totalTickets = array_sum($porEstado);

            $estadoConfig = [
                'abierto'           => ['label'=>'Abierto',           'color'=>'#005C3E', 'icon'=>'fas fa-folder-open'],
                'en_proceso'        => ['label'=>'En proceso',        'color'=>'#ffc107', 'icon'=>'fas fa-spinner'],
                'esperando_cliente' => ['label'=>'Esperando cliente', 'color'=>'#6c757d', 'icon'=>'fas fa-pause'],
                'resuelto'          => ['label'=>'Resuelto',          'color'=>'#0d6efd', 'icon'=>'fas fa-check'],
                'cerrado'           => ['label'=>'Cerrado',           'color'=>'#adb5bd', 'icon'=>'fas fa-lock'],
            ];
            $prioConfig = [
                'critica' => ['label'=>'Crítica', 'color'=>'#dc3545'],
                'alta'    => ['label'=>'Alta',    'color'=>'#fd7e14'],
                'media'   => ['label'=>'Media',   'color'=>'#ffc107'],
                'baja'    => ['label'=>'Baja',    'color'=>'#6c757d'],
            ];
            ?>

            <!-- Por estado -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary">
                        <i class="fas fa-layer-group me-2"></i>Por Estado
                        <span class="badge bg-light text-dark ms-2"><?= $totalTickets ?> total</span>
                    </div>
                    <div class="card-body">
                        <?php foreach ($estadoConfig as $key => $conf): ?>
                        <?php $total = $porEstado[$key] ?? 0; ?>
                        <?php $pct = $totalTickets > 0 ? round(($total / $totalTickets) * 100) : 0; ?>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <i class="<?= $conf['icon'] ?> text-muted" style="width:20px;"></i>
                            <span class="small fw-semibold" style="width:140px;"><?= $conf['label'] ?></span>
                            <div class="flex-grow-1" style="background:#f0f0f0;border-radius:4px;height:18px;overflow:hidden;">
                                <div style="width:<?= $pct ?>%;height:100%;background:<?= $conf['color'] ?>;border-radius:4px;"></div>
                            </div>
                            <span class="badge" style="background:<?= $conf['color'] ?>;min-width:30px;"><?= $total ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Por prioridad -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary">
                        <i class="fas fa-exclamation-triangle me-2"></i>Por Prioridad
                    </div>
                    <div class="card-body">
                        <?php foreach ($prioConfig as $key => $conf): ?>
                        <?php $total = $porPrioridad[$key] ?? 0; ?>
                        <?php $pct = $totalTickets > 0 ? round(($total / $totalTickets) * 100) : 0; ?>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="small fw-semibold" style="width:70px;"><?= $conf['label'] ?></span>
                            <div class="flex-grow-1" style="background:#f0f0f0;border-radius:4px;height:18px;overflow:hidden;">
                                <div style="width:<?= $pct ?>%;height:100%;background:<?= $conf['color'] ?>;border-radius:4px;"></div>
                            </div>
                            <span class="badge" style="background:<?= $conf['color'] ?>;min-width:30px;"><?= $total ?></span>
                        </div>
                        <?php endforeach; ?>

                        <hr>
                        <div class="text-center">
                            <a href="/Tickets" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-ticket-alt me-1"></i>Ver todos los tickets
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── TAB: Empleados activos ── -->
    <div class="tab-pane fade" id="tabEmpleados">
        <div class="card shadow-sm">
            <div class="card-header bg-primary">
                <i class="fas fa-user-tie me-2"></i>Empleados más activos — últimos <?= $dias ?> días
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Empleado</th>
                                <th>Rol</th>
                                <th>Acciones realizadas</th>
                                <th>Última actividad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($empleadosActivos as $i => $e): ?>
                            <tr>
                                <td class="text-muted fw-bold"><?= $i + 1 ?></td>
                                <td>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($e['nombre']) ?></p>
                                    <small class="text-muted"><?= htmlspecialchars($e['email']) ?></small>
                                </td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($e['rol_nombre']) ?></span></td>
                                <td>
                                    <span class="badge bg-primary fs-6"><?= $e['total_acciones'] ?></span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= $e['ultima_accion']
                                            ? date('d/m/Y H:i', strtotime($e['ultima_accion']))
                                            : 'Sin actividad' ?>
                                    </small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>