<!-- Views/Empleados/Roles/index.php -->

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
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-shield-alt me-2 text-success"></i>Roles y Permisos
        </h4>
        <small class="text-muted">Gestión de roles del sistema</small>
    </div>
    <div class="d-flex gap-2">
        <a href="/Empleados" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Empleados
        </a>
        <a href="/Empleados/RolesRegistry" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Rol
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

<div class="card shadow-sm">
    <div class="card-header bg-primary d-flex align-items-center justify-content-between">
        <span><i class="fas fa-table me-2"></i>Lista de Roles</span>
        <span class="badge bg-light text-dark"><?= count($roles) ?> roles</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Rol</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Empleados</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $i => $r): ?>
                    <tr>
                        <td class="text-muted"><?= str_pad($i+1,3,'0',STR_PAD_LEFT) ?></td>
                        <td>
                            <p class="mb-0 fw-semibold"><?= htmlspecialchars($r['nombre']) ?></p>
                        </td>
                        <td>
                            <small class="text-muted"><?= htmlspecialchars($r['descripcion'] ?? '—') ?></small>
                        </td>
                        <td>
                            <?php if ($r['es_admin']): ?>
                                <span class="badge bg-danger">
                                    <i class="fas fa-crown me-1"></i>Administrador
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-user me-1"></i>Estándar
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-primary"><?= $r['total_empleados'] ?></span>
                        </td>
                        <td class="text-center">
                            <a href="/Empleados/RolesRegistry/<?= $r['id'] ?>"
                               class="btn btn-sm btn-outline-warning"
                               title="Editar rol y permisos">
                                <i class="fas fa-edit me-1"></i>Editar permisos
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Total: <strong><?= count($roles) ?></strong> roles</small>
        <small class="text-muted">DeskCod</small>
    </div>
</div>