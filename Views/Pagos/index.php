<!-- Views/Pagos/index.php -->

<style>
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
body.dark-mode .form-select, body.dark-mode .form-control {
    background:#111f18; border-color:#1e3329; color:#e0e0e0;
}
body.dark-mode .nav-tabs .nav-link { color: #adb5bd; border-color: #1e3329; }
body.dark-mode .nav-tabs .nav-link.active { background:#111f18; color:#00E676; border-color:#1e3329; }

/* Barra de progreso de pago */
.barra-pago { height: 8px; border-radius: 4px; background: #1e3329; overflow: hidden; }
.barra-pago .progreso {
    height: 100%; border-radius: 4px;
    background: linear-gradient(90deg, #005C3E, #00E676);
    transition: width 0.5s ease;
}
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-credit-card me-2 text-success"></i>Pagos y Facturación
        </h4>
        <small class="text-muted">Historial de pagos y cuentas por cobrar</small>
    </div>
    <div class="d-flex gap-2">
        <a href="/Pagos/Cuenta" class="btn btn-outline-primary">
            <i class="fas fa-file-invoice me-2"></i>Nueva Cuenta
        </a>
        <a href="/Pagos/Registry" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Registrar Pago
        </a>
    </div>
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

<!-- Métricas -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm card-metric">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Total pagos</p>
                        <div class="metric-value"><?= $totalPagos ?></div>
                    </div>
                    <i class="fas fa-receipt fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm card-metric">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Ingresos este mes</p>
                        <div class="metric-value">$<?= number_format($totalMes, 2) ?></div>
                    </div>
                    <i class="fas fa-calendar-check fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm card-metric">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Total cobrado</p>
                        <div class="metric-value">$<?= number_format($totalGeneral, 2) ?></div>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm card-metric" style="border-left-color:#dc3545 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Saldo pendiente</p>
                        <div class="metric-value text-danger">$<?= number_format($totalPendiente, 2) ?></div>
                    </div>
                    <i class="fas fa-exclamation-circle fa-2x text-danger opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="pagosTabs">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabCuentas">
            <i class="fas fa-file-invoice me-2"></i>Cuentas por cobrar
            <?php $pendientes = array_filter($cuentas, fn($c) => $c['estado'] !== 'pagado'); ?>
            <?php if (count($pendientes) > 0): ?>
            <span class="badge bg-danger ms-1"><?= count($pendientes) ?></span>
            <?php endif; ?>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabPagos">
            <i class="fas fa-history me-2"></i>Historial de pagos
        </button>
    </li>
</ul>

<div class="tab-content">

    <!-- TAB: Cuentas por cobrar -->
    <div class="tab-pane fade show active" id="tabCuentas">
        <div class="card shadow-sm">
            <div class="card-header bg-primary d-flex align-items-center justify-content-between">
                <span><i class="fas fa-file-invoice-dollar me-2"></i>Cuentas por Cobrar</span>
                <span class="badge bg-light text-dark"><?= count($cuentas) ?> cuentas</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Descripción</th>
                                <th>Tipo</th>
                                <th>Total</th>
                                <th>Pagado</th>
                                <th>Pendiente</th>
                                <th>Progreso</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($cuentas)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-file-invoice fa-3x mb-3 d-block opacity-25"></i>
                                    No hay cuentas por cobrar.
                                    <br>
                                    <a href="/Pagos/Cuenta" class="btn btn-primary btn-sm mt-3">
                                        <i class="fas fa-plus me-1"></i>Crear cuenta
                                    </a>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($cuentas as $c):
                                $pct = $c['monto_total'] > 0
                                    ? min(100, round(($c['monto_pagado'] / $c['monto_total']) * 100))
                                    : 0;
                            ?>
                            <tr>
                                <td>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($c['cliente_nombre']) ?></p>
                                    <small class="text-muted"><?= htmlspecialchars($c['empresa_nombre'] ?? '') ?></small>
                                </td>
                                <td><?= htmlspecialchars($c['descripcion']) ?></td>
                                <td>
                                    <?php if ($c['tipo'] === 'sistema'): ?>
                                        <span class="badge bg-info text-dark">
                                            <i class="fas fa-code me-1"></i>Sistema
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-sync-alt me-1"></i>Suscripción
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><strong>$<?= number_format($c['monto_total'],2) ?></strong></td>
                                <td class="text-success">$<?= number_format($c['monto_pagado'],2) ?></td>
                                <td class="<?= $c['saldo_pendiente'] > 0 ? 'text-danger fw-bold' : 'text-success' ?>">
                                    $<?= number_format($c['saldo_pendiente'],2) ?>
                                </td>
                                <td width="120">
                                    <div class="barra-pago">
                                        <div class="progreso" style="width:<?= $pct ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?= $pct ?>%</small>
                                </td>
                                <td>
                                    <?php
                                    $badgeEstado = [
                                        'pendiente' => '<span class="badge-vencida">Pendiente</span>',
                                        'parcial'   => '<span class="badge-por-vencer">Parcial</span>',
                                        'pagado'    => '<span class="badge-activa"><span class="status-active-pulse me-1"></span>Pagado</span>',
                                    ];
                                    echo $badgeEstado[$c['estado']] ?? $c['estado'];
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($c['estado'] !== 'pagado'): ?>
                                    <a href="/Pagos/Registry?cliente=<?= $c['cliente_id'] ?>&cuenta=<?= $c['id'] ?>"
                                       class="btn btn-sm btn-primary" title="Registrar abono">
                                        <i class="fas fa-plus me-1"></i>Abono
                                    </a>
                                    <?php endif; ?>
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

    <!-- TAB: Historial de pagos -->
    <div class="tab-pane fade" id="tabPagos">
        <!-- Filtros -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text" style="background:#005C3E;color:#fff;border-color:#005C3E;">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="buscador"
                                   placeholder="Cliente, concepto, factura...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <input type="month" class="form-control" id="filtroMes" value="<?= date('Y-m') ?>">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filtroMetodo">
                            <option value="">Todos</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia bancaria">Transferencia</option>
                            <option value="Cheque">Cheque</option>
                            <option value="PayPal">PayPal</option>
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

        <div class="card shadow-sm">
            <div class="card-header bg-primary d-flex align-items-center justify-content-between">
                <span><i class="fas fa-history me-2"></i>Historial de Pagos</span>
                <span class="badge bg-light text-dark" id="contador"><?= count($pagos) ?> registros</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tablaPagos">
                        <thead>
                            <tr>
                                <th>Factura</th>
                                <th>Cliente</th>
                                <th>Concepto</th>
                                <th>Método</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                                <th>Comprobante</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pagos)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-receipt fa-3x mb-3 d-block opacity-25"></i>
                                    No hay pagos registrados.
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($pagos as $p):
                                $fecha = $p['fecha_pago'] ?? $p['created_at'];
                            ?>
                            <tr data-buscar="<?= strtolower(htmlspecialchars($p['cliente_nombre'].' '.($p['concepto']??'').' '.($p['numero_factura']??''))) ?>"
                                data-metodo="<?= htmlspecialchars($p['metodo_pago']) ?>"
                                data-mes="<?= date('Y-m', strtotime($fecha)) ?>">
                                <td>
                                    <small class="fw-bold text-success">
                                        <?= htmlspecialchars($p['numero_factura'] ?? '—') ?>
                                    </small>
                                </td>
                                <td>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($p['cliente_nombre']) ?></p>
                                </td>
                                <td><small><?= htmlspecialchars($p['concepto'] ?? '—') ?></small></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= htmlspecialchars($p['metodo_pago']) ?>
                                    </span>
                                </td>
                                <td><strong class="text-success">$<?= number_format($p['monto'],2) ?></strong></td>
                                <td><small><?= date('d/m/Y', strtotime($fecha)) ?></small></td>
                                <td>
                                    <?php if (!empty($p['comprobante_imagen'])): ?>
                                    <a href="/<?= htmlspecialchars($p['comprobante_imagen']) ?>"
                                       target="_blank" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-image me-1"></i>Ver
                                    </a>
                                    <?php else: ?>
                                    <span class="text-muted small">—</span>
                                    <?php endif; ?>
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

</div>

<script>
const buscador     = document.getElementById('buscador');
const filtroMes    = document.getElementById('filtroMes');
const filtroMetodo = document.getElementById('filtroMetodo');

function filtrar() {
    const txt    = buscador.value.toLowerCase();
    const mes    = filtroMes.value;
    const metodo = filtroMetodo.value;
    let visibles = 0;

    document.querySelectorAll('#tablaPagos tbody tr[data-buscar]').forEach(fila => {
        const ok = fila.dataset.buscar.includes(txt)
            && (!mes    || fila.dataset.mes    === mes)
            && (!metodo || fila.dataset.metodo === metodo);
        fila.style.display = ok ? '' : 'none';
        if (ok) visibles++;
    });
    document.getElementById('contador').textContent = visibles + ' registros';
}

function limpiarFiltros() {
    buscador.value = filtroMetodo.value = '';
    filtroMes.value = '<?= date('Y-m') ?>';
    document.querySelectorAll('#tablaPagos tbody tr').forEach(r => r.style.display = '');
    document.getElementById('contador').textContent =
        document.querySelectorAll('#tablaPagos tbody tr[data-buscar]').length + ' registros';
    filtrar();
}

buscador?.addEventListener('input', filtrar);
filtroMes?.addEventListener('change', filtrar);
filtroMetodo?.addEventListener('change', filtrar);
filtrar();
</script>