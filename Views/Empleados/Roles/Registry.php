<!-- Views/Empleados/Roles/Registry.php -->

<style>
.input-group-text { background:#005C3E; color:#fff; border-color:#005C3E; }
body.dark-mode .input-group-text { background:#1a3329; border-color:#1e3329; color:#e0e0e0; }
body.dark-mode .form-control, body.dark-mode .form-select {
    background:#111f18; border-color:#1e3329; color:#e0e0e0;
}
body.dark-mode .form-control:focus {
    background:#0f1a15; border-color:#00E676; box-shadow:0 0 0 3px rgba(0,230,118,0.1);
}
body.dark-mode .form-label { color:#c8e6d5; }

/* Matriz de permisos */
.tabla-permisos th { font-size: 0.78rem; text-align: center; vertical-align: middle; }
.tabla-permisos td { vertical-align: middle; }
.tabla-permisos td.modulo-nombre { font-weight: 600; white-space: nowrap; }

.permiso-check {
    width: 22px; height: 22px; cursor: pointer;
    accent-color: #005C3E;
}

.btn-toggle-fila {
    font-size: 0.7rem; padding: 2px 8px;
    border-radius: 20px; cursor: pointer;
}

body.dark-mode .tabla-permisos { color: #e0e0e0; }
body.dark-mode .tabla-permisos thead th {
    background: #1a3329 !important; color: #fff !important; border-color: #1e3329 !important;
}
body.dark-mode .tabla-permisos tbody td { border-color: #1e3329 !important; }
body.dark-mode .tabla-permisos tbody tr:hover td {
    background: rgba(0,230,118,0.06) !important;
}
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <?php if ($rol): ?>
                <i class="fas fa-edit me-2 text-warning"></i>Editar Rol
            <?php else: ?>
                <i class="fas fa-plus-circle me-2 text-success"></i>Nuevo Rol
            <?php endif; ?>
        </h4>
    </div>
    <a href="/Empleados/Roles" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Regresar
    </a>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-times-circle fa-lg"></i>
    <span><?= htmlspecialchars($error) ?></span>
</div>
<?php endif; ?>

<form id="rolForm" action="" method="POST">
    <input type="hidden" name="GuardarRol" value="1">

    <div class="row g-4">

        <!-- Datos del rol -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <i class="fas fa-shield-alt me-2"></i>Datos del Rol
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nombre del rol <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            <input type="text" class="form-control" name="nombre" id="nombre"
                                   value="<?= htmlspecialchars($rol['nombre'] ?? '') ?>"
                                   placeholder="Ej. Vendedor" maxlength="100">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                            <input type="text" class="form-control" name="descripcion"
                                   value="<?= htmlspecialchars($rol['descripcion'] ?? '') ?>"
                                   placeholder="Breve descripción del rol" maxlength="200">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="es_admin"
                                   id="esAdmin" <?= ($rol['es_admin'] ?? false) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="esAdmin">
                                <i class="fas fa-crown text-danger me-1"></i>
                                Es administrador
                            </label>
                        </div>
                        <small class="text-muted">
                            Los administradores pueden gestionar empleados y roles.
                        </small>
                    </div>

                    <hr>

                    <div class="d-flex gap-2 mb-2">
                        <button type="button" class="btn btn-outline-success btn-sm flex-fill"
                                onclick="toggleTodos(true)">
                            <i class="fas fa-check-double me-1"></i>Todos
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm flex-fill"
                                onclick="toggleTodos(false)">
                            <i class="fas fa-times me-1"></i>Ninguno
                        </button>
                    </div>
                    <small class="text-muted">Activar o desactivar todos los permisos</small>

                </div>
            </div>
        </div>

        <!-- Matriz de permisos -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-table me-2"></i>Permisos por Módulo</span>
                    <small class="text-white opacity-75">Marca los accesos permitidos</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table tabla-permisos mb-0">
                            <thead>
                                <tr>
                                    <th class="text-start ps-3" width="200">Módulo</th>
                                    <?php foreach ($acciones as $accion): ?>
                                    <th><?= htmlspecialchars($accion['label']) ?></th>
                                    <?php endforeach; ?>
                                    <th>Todo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($modulos as $modulo): ?>
                                <tr>
                                    <td class="modulo-nombre ps-3">
                                        <i class="<?= htmlspecialchars($modulo['icono']) ?> me-2 text-success"></i>
                                        <?= htmlspecialchars($modulo['label']) ?>
                                    </td>
                                    <?php foreach ($acciones as $accion): ?>
                                    <td class="text-center">
                                        <input type="checkbox"
                                               class="permiso-check permiso-individual"
                                               name="permiso_<?= $modulo['id'] ?>_<?= $accion['id'] ?>"
                                               data-modulo="<?= $modulo['id'] ?>"
                                               <?= ($permisos[$modulo['id']][$accion['id']] ?? false) ? 'checked' : '' ?>>
                                    </td>
                                    <?php endforeach; ?>
                                    <td class="text-center">
                                        <button type="button"
                                                class="btn btn-outline-success btn-toggle-fila"
                                                onclick="toggleFila(this, <?= $modulo['id'] ?>)">
                                            <i class="fas fa-check"></i>
                                        </button>
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

    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="/Empleados/Roles" class="btn btn-outline-secondary">
            <i class="fas fa-times me-2"></i>Cancelar
        </a>
        <button type="submit" class="btn btn-primary" id="btnGuardar">
            <i class="fas fa-save me-2"></i>
            <?= $rol ? 'Guardar Cambios' : 'Crear Rol' ?>
        </button>
    </div>

</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ── Toggle todos los permisos ──
function toggleTodos(activar) {
    document.querySelectorAll('.permiso-individual').forEach(cb => cb.checked = activar);
}

// ── Toggle todos los permisos de una fila ──
function toggleFila(btn, moduloId) {
    const checks = document.querySelectorAll(`.permiso-individual[data-modulo="${moduloId}"]`);
    const todosActivos = Array.from(checks).every(c => c.checked);
    checks.forEach(c => c.checked = !todosActivos);
}

// ── Validación ──
document.getElementById('rolForm').addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    if (!nombre) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'El nombre del rol es obligatorio.', confirmButtonColor:'#005C3E'
        });
        return;
    }
    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
});
</script>