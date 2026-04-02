<!-- Views/Planes/index.php -->

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

/* Cards de plan */
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
            : (str_contains($pN,'estándar') || str_contains($pN,'estandar') ? 'plan-estandar' : 'plan-basico');
        $icon  = str_contains($pN,'premium') ? 'fas fa-crown'
            : (str_contains($pN,'estándar') || str_contains($pN,'estandar') ? 'fas fa-star' : 'fas fa-leaf');
    ?>
    <div class="col-md-4">
        <div class="card shadow-sm plan-card-display <?= $clase ?>">
            <div class="card-header text-white py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="<?= $icon ?> me-2 fa-lg"></i>
                        <span class="fw-bold fs-5"><?= htmlspecialchars($p['nombre']) ?></span>
                    </div>
                    <span class="badge bg-white bg-opacity-25">
                        <?= $p['total_suscripciones'] ?> activos
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <span class="fs-2 fw-bold text-success">L.<?= number_format($p['precio'],2) ?></span>
                    <span class="text-muted">/mes</span>
                </div>
                <p class="text-muted small text-center mb-3">
                    <?= htmlspecialchars($p['descripcion'] ?? '') ?>
                </p>
                <hr>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">
                        <i class="fas fa-calendar me-1"></i><?= $p['duracion_dias'] ?> días
                    </span>
                    <span class="text-muted">
                        <i class="fas fa-ticket-alt me-1"></i>
                        <?= $p['max_tickets'] ? $p['max_tickets'].' tickets/mes' : 'Tickets ilimitados' ?>
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
                        <th>Precio</th>
                        <th>Duración</th>
                        <th>Max Tickets</th>
                        <th>Suscripciones activas</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($planes as $p): ?>
                    <tr>
                        <td>
                            <p class="mb-0 fw-semibold"><?= htmlspecialchars($p['nombre']) ?></p>
                            <small class="text-muted"><?= htmlspecialchars(substr($p['descripcion'] ?? '',0,50)) ?>...</small>
                        </td>
                        <td><strong class="text-success">L.<?= number_format($p['precio'],2) ?></strong></td>
                        <td><?= $p['duracion_dias'] ?> días</td>
                        <td><?= $p['max_tickets'] ?? '<span class="text-muted">Ilimitado</span>' ?></td>
                        <td>
                            <span class="badge bg-primary"><?= $p['total_suscripciones'] ?></span>
                        </td>
                        <td>
                            <?php if ($p['activo']): ?>
                                <span class="badge-activa"><span class="status-active-pulse me-1"></span>Activo</span>
                            <?php else: ?>
                                <span class="badge-vencida">Inactivo</span>
                            <?php endif; ?>
                        </td>
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
        html: `El plan <strong>${nombre}</strong> no estará disponible para nuevas suscripciones.<br>
               <small class="text-muted">Las suscripciones activas no se verán afectadas.</small>`,
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