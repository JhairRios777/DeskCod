<!-- Views/Empleados/index.php -->

<style>
.avatar-circle {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg,#005C3E,#00E676);
    color:#fff; display:flex; align-items:center;
    justify-content:center; font-weight:700; font-size:.85rem; flex-shrink:0;
}

/* Tabla dark mode */
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
body.dark-mode .form-control, body.dark-mode .form-select {
    background:#111f18; border-color:#1e3329; color:#e0e0e0;
}
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-user-tie me-2 text-success"></i>Empleados
        </h4>
        <small class="text-muted">Gestión de empleados del sistema</small>
    </div>
    <div class="d-flex gap-2">
        <a href="/Empleados/Roles" class="btn btn-outline-primary">
            <i class="fas fa-shield-alt me-2"></i>Roles y Permisos
        </a>
        <a href="/Empleados/Registry" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Empleado
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

<!-- Buscador -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-semibold mb-1">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:#005C3E;color:#fff;border-color:#005C3E;">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="buscador"
                           placeholder="Nombre, email o usuario...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold mb-1">Rol</label>
                <select class="form-select" id="filtroRol">
                    <option value="">Todos</option>
                    <?php
                    $rolesUnicos = array_unique(array_column($empleados, 'rol_nombre'));
                    foreach ($rolesUnicos as $rol):
                    ?>
                    <option value="<?= htmlspecialchars($rol) ?>"><?= htmlspecialchars($rol) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
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
        <span><i class="fas fa-table me-2"></i>Lista de Empleados</span>
        <span class="badge bg-light text-dark" id="contador"><?= count($empleados) ?> registros</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tablaEmpleados">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Empleado</th>
                        <th>Usuario</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Último acceso</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($empleados)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-user-tie fa-3x mb-3 d-block opacity-25"></i>
                            No hay empleados registrados.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($empleados as $i => $e):
                        $p   = explode(' ', $e['nombre']);
                        $ini = strtoupper(substr($p[0],0,1).(isset($p[1])?substr($p[1],0,1):''));
                    ?>
                    <tr data-rol="<?= htmlspecialchars($e['rol_nombre']) ?>"
                        data-buscar="<?= strtolower(htmlspecialchars($e['nombre'].' '.$e['email'].' '.$e['username'])) ?>">
                        <td class="text-muted"><?= str_pad($i+1,3,'0',STR_PAD_LEFT) ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle"><?= $ini ?></div>
                                <div>
                                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($e['nombre']) ?></p>
                                    <small class="text-muted"><?= htmlspecialchars($e['email']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code><?= htmlspecialchars($e['username']) ?></code>
                        </td>
                        <td><?= htmlspecialchars($e['telefono'] ?? '—') ?></td>
                        <td>
                            <?php if ($e['es_admin']): ?>
                                <span class="badge bg-danger">
                                    <i class="fas fa-crown me-1"></i><?= htmlspecialchars($e['rol_nombre']) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= htmlspecialchars($e['rol_nombre']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= $e['ultimo_login']
                                    ? date('d/m/Y H:i', strtotime($e['ultimo_login']))
                                    : 'Nunca' ?>
                            </small>
                        </td>
                        <td class="text-center">
                            <a href="/Empleados/Registry/<?= $e['id'] ?>"
                               class="btn btn-sm btn-outline-warning me-1" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($e['id'] !== (int)($_SESSION['system']['UserID'] ?? 0)): ?>
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="desactivar(<?= $e['id'] ?>, '<?= htmlspecialchars($e['nombre'], ENT_QUOTES) ?>')"
                                    title="Desactivar">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($empleados)): ?>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Total: <strong><?= count($empleados) ?></strong> empleados activos</small>
        <small class="text-muted">DeskCod</small>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

    
const buscador  = document.getElementById('buscador');
const filtroRol = document.getElementById('filtroRol');

function filtrar() {
    const txt = buscador.value.toLowerCase();
    const rol = filtroRol.value;
    let visibles = 0;
    document.querySelectorAll('#tablaEmpleados tbody tr[data-buscar]').forEach(fila => {
        const ok = fila.dataset.buscar.includes(txt) && (!rol || fila.dataset.rol === rol);
        fila.style.display = ok ? '' : 'none';
        if (ok) visibles++;
    });
    document.getElementById('contador').textContent = visibles + ' registros';
}

function limpiarFiltros() {
    buscador.value = filtroRol.value = '';
    document.querySelectorAll('#tablaEmpleados tbody tr').forEach(r => r.style.display = '');
    document.getElementById('contador').textContent =
        document.querySelectorAll('#tablaEmpleados tbody tr[data-buscar]').length + ' registros';
}

buscador.addEventListener('input', filtrar);
filtroRol.addEventListener('change', filtrar);

function desactivar(id, nombre) {
    Swal.fire({
        title: '¿Desactivar empleado?',
        html: `<strong>${nombre}</strong> no podrá acceder al sistema.`,
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
        fetch('/Empleados/desactivar', { method:'POST', body:fd, credentials:'same-origin' })
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