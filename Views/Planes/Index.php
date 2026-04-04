<!-- Views/Planes/index.php -->

<style>
.table-hover > tbody > tr:hover > :not(caption) > * > *,
.table-hover > tbody > tr:hover > * {
    background-color: rgba(0,92,62,0.08) !important;
    --bs-table-bg-state: rgba(0,92,62,0.08) !important;
}
html.dark-mode .table {
    --bs-table-color: #e0e0e0 !important; --bs-table-bg: transparent !important;
    --bs-table-border-color: #1e3329 !important; color: #e0e0e0 !important;
}
html.dark-mode .table > :not(caption) > * > * {
    background-color: transparent !important; color: #e0e0e0 !important;
    border-bottom-color: #1e3329 !important;
}
html.dark-mode .table thead > tr > * {
    background-color: #1a3329 !important; color: #fff !important; border-color: #1e3329 !important;
}
html.dark-mode .table p, html.dark-mode .table .fw-semibold { color: #fff !important; }
html.dark-mode .table small, html.dark-mode .table .text-muted { color: #adb5bd !important; }
html.dark-mode .table-hover > tbody > tr:hover > :not(caption) > * > *,
html.dark-mode .table-hover > tbody > tr:hover > * {
    background-color: rgba(0,230,118,0.12) !important; color: #e0e0e0 !important;
}
html.dark-mode .card-footer { background: #111f18 !important; border-color: #1e3329 !important; }

/* Cards */
.plan-card-display {
    border-radius: 16px; overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}
.plan-card-display:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,92,62,0.2) !important;
}
.plan-card-display.plan-basico   .card-header { background: #6c757d !important; }
.plan-card-display.plan-estandar .card-header { background: #0d6efd !important; }
.plan-card-display.plan-premium  .card-header { background: linear-gradient(135deg,#b8860b,#ffc107) !important; }

/* Badge descuento */
.badge-descuento {
    background: linear-gradient(135deg, #dc3545, #ff6b6b);
    color: #fff; font-size: .7rem; padding: 3px 8px;
    border-radius: 20px; font-weight: 700;
}

/* Precio anual */
.precio-anual-box {
    background: rgba(0,230,118,0.08);
    border: 1px dashed rgba(0,230,118,0.4);
    border-radius: 8px; padding: .5rem .75rem;
    margin-top: .5rem;
}
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-layer-group me-2 text-success"></i>Planes
        </h4>
        <small class="text-muted">Gestión de planes de suscripción</small>
    </div>
    <a href="/Planes/Registry" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nuevo Plan
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

<!-- Cards de planes -->
<div class="row g-4 mb-5">
    <?php foreach ($planes as $p):
        $pN    = strtolower($p['nombre']);
        $clase = str_contains($pN,'premium') ? 'plan-premium'
            : (str_contains($pN,'est') ? 'plan-estandar' : 'plan-basico');
        $icon  = str_contains($pN,'premium') ? 'fas fa-crown'
            : (str_contains($pN,'est') ? 'fas fa-star' : 'fas fa-leaf');
        $tieneDescuento = (float)($p['descuento_anual'] ?? 0) > 0;
    ?>
    <div class="col-md-4">
        <div class="card shadow-sm plan-card-display <?= $clase ?>">
            <div class="card-header text-white py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="<?= $icon ?> me-2 fa-lg"></i>
                        <span class="fw-bold fs-5"><?= htmlspecialchars($p['nombre']) ?></span>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        <span class="badge bg-white bg-opacity-25">
                            <?= $p['total_suscripciones'] ?> activos
                        </span>
                        <?php if ($tieneDescuento): ?>
                        <span class="badge-descuento">
                            <?= number_format($p['descuento_anual'], 0) ?>% anual
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Precio mensual -->
                <div class="text-center mb-2">
                    <span class="fs-2 fw-bold text-success">L.<?= number_format($p['precio'],2) ?></span>
                    <span class="text-muted">/mes</span>
                </div>

                <!-- Precio anual si tiene descuento -->
                <?php if ($tieneDescuento): ?>
                <div class="precio-anual-box text-center mb-2">
                    <div class="small text-muted mb-1">
                        <i class="fas fa-calendar-alt me-1"></i>Precio anual
                    </div>
                    <span class="fw-bold text-success">
                        L.<?= number_format($p['precio_anual'], 2) ?>
                    </span>
                    <span class="text-muted small">/año</span>
                    <div class="text-success small">
                        Ahorro: L.<?= number_format($p['ahorro_anual'], 2) ?>
                    </div>
                </div>
                <?php endif; ?>

                <p class="text-muted small text-center mb-3">
                    <?= htmlspecialchars(substr($p['descripcion'] ?? '',0,80)) ?>
                </p>
                <hr>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">
                        <i class="fas fa-calendar me-1"></i><?= $p['duracion_dias'] ?> días
                    </span>
                    <span class="text-muted">
                        <i class="fas fa-ticket-alt me-1"></i>
                        <?= $p['max_tickets'] ? $p['max_tickets'].' tickets/mes' : 'Ilimitados' ?>
                    </span>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="/Planes/Registry/<?= $p['id'] ?>"
                   class="btn btn-outline-warning btn-sm flex-fill">
                    <i class="fas fa-edit me-1"></i>Editar
                </a>
                <button class="btn btn-outline-danger btn-sm"
                        onclick="desactivar(<?= $p['id'] ?>, <?= json_encode($p['nombre']) ?>)"
                        title="Desactivar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Tabla detalle -->
<div class="card shadow-sm">
    <div class="card-header bg-primary d-flex align-items-center justify-content-between">
        <span><i class="fas fa-table me-2"></i>Detalle de Planes</span>
        <span class="badge bg-light text-dark"><?= count($planes) ?> planes</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Plan</th>
                        <th>Precio/mes</th>
                        <th>Descuento anual</th>
                        <th>Precio/año</th>
                        <th>Ahorro</th>
                        <th>Duración</th>
                        <th>Max Tickets</th>
                        <th>Activos</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($planes as $p):
                        $tieneDescuento = (float)($p['descuento_anual'] ?? 0) > 0;
                    ?>
                    <tr>
                        <td>
                            <p class="mb-0 fw-semibold"><?= htmlspecialchars($p['nombre']) ?></p>
                            <small class="text-muted"><?= htmlspecialchars(substr($p['descripcion'] ?? '',0,40)) ?>...</small>
                        </td>
                        <td><strong class="text-success">L.<?= number_format($p['precio'],2) ?></strong></td>
                        <td>
                            <?php if ($tieneDescuento): ?>
                            <span class="badge-descuento"><?= number_format($p['descuento_anual'],0) ?>% OFF</span>
                            <?php else: ?>
                            <span class="text-muted small">Sin descuento</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($tieneDescuento): ?>
                            <strong class="text-success">L.<?= number_format($p['precio_anual'],2) ?></strong>
                            <?php else: ?>
                            <span class="text-muted">L.<?= number_format($p['precio'] * 12, 2) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($tieneDescuento): ?>
                            <span class="text-success small">L.<?= number_format($p['ahorro_anual'],2) ?></span>
                            <?php else: ?>
                            <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $p['duracion_dias'] ?> días</td>
                        <td><?= $p['max_tickets'] ?? '<span class="text-muted">Ilimitado</span>' ?></td>
                        <td><span class="badge bg-primary"><?= $p['total_suscripciones'] ?></span></td>
                        <td class="text-center">
                            <a href="/Planes/Registry/<?= $p['id'] ?>"
                               class="btn btn-sm btn-outline-warning me-1" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="desactivar(<?= $p['id'] ?>, <?= json_encode($p['nombre']) ?>)"
                                    title="Desactivar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function desactivar(id, nombre) {
    Swal.fire({
        title: '¿Desactivar plan?',
        html: `El plan <strong>${nombre}</strong> no estará disponible para nuevas suscripciones.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#005C3E',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (!r.isConfirmed) return;
        const fd = new FormData();
        fd.append('id', id);
        fetch('/Planes/desactivar', { method:'POST', body:fd, credentials:'same-origin' })
            .then(r => r.json())
            .then(data => {
                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.success ? '¡Desactivado!' : 'Error',
                    text: data.message, confirmButtonColor:'#005C3E'
                }).then(() => { if (data.success) location.reload(); });
            });
    });
}
</script>