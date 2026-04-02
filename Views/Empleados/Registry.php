<!-- Views/Empleados/Registry.php -->

<style>
.input-group-text { background:#005C3E; color:#fff; border-color:#005C3E; }
body.dark-mode .input-group-text { background:#1a3329; border-color:#1e3329; color:#e0e0e0; }
body.dark-mode .form-control, body.dark-mode .form-select {
    background:#111f18; border-color:#1e3329; color:#e0e0e0;
}
body.dark-mode .form-control:focus, body.dark-mode .form-select:focus {
    background:#0f1a15; border-color:#00E676; box-shadow:0 0 0 3px rgba(0,230,118,0.1);
}
body.dark-mode .form-label { color:#c8e6d5; }

/* Rol cards */
.rol-card {
    border-radius: 10px; padding: 0.85rem 1rem;
    cursor: pointer; transition: all 0.2s;
    border: 2px solid var(--border-color);
}
.rol-card:hover { border-color: #005C3E; background: rgba(0,92,62,0.05); }
.rol-card.selected {
    border-color: #005C3E !important;
    background: rgba(0,92,62,0.1) !important;
    box-shadow: 0 3px 12px rgba(0,92,62,0.2);
}
.rol-card.admin.selected { border-color: #dc3545 !important; background: rgba(220,53,69,0.08) !important; }
body.dark-mode .rol-card { border-color: #1e3329; }
body.dark-mode .rol-card:hover { border-color: #00E676; background: rgba(0,230,118,0.05); }
body.dark-mode .rol-card.selected { border-color: #00E676 !important; background: rgba(0,230,118,0.1) !important; }
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <?php if ($empleado): ?>
                <i class="fas fa-edit me-2 text-warning"></i>Editar Empleado
            <?php else: ?>
                <i class="fas fa-user-plus me-2 text-success"></i>Nuevo Empleado
            <?php endif; ?>
        </h4>
    </div>
    <a href="/Empleados" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Regresar
    </a>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-times-circle fa-lg"></i>
    <span><?= htmlspecialchars($error) ?></span>
</div>
<?php endif; ?>

<form id="empleadoForm" action="" method="POST">
    <input type="hidden" name="Registrar" value="1">

    <div class="row g-4">

        <!-- Datos del empleado -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <i class="fas fa-user me-2"></i>Datos del Empleado
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nombre completo <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" name="nombre" id="nombre"
                                       value="<?= htmlspecialchars($empleado['nombre'] ?? '') ?>"
                                       placeholder="Ej. Juan Pérez" maxlength="100">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Correo electrónico <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" name="email" id="email"
                                       value="<?= htmlspecialchars($empleado['email'] ?? '') ?>"
                                       placeholder="correo@empresa.com" maxlength="150">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nombre de usuario <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-at"></i></span>
                                <input type="text" class="form-control" name="username" id="username"
                                       value="<?= htmlspecialchars($empleado['username'] ?? '') ?>"
                                       placeholder="Ej. jperez" maxlength="50">
                            </div>
                            <small class="text-muted">Se usa para iniciar sesión</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                <input type="tel" class="form-control" name="telefono"
                                       value="<?= htmlspecialchars($empleado['telefono'] ?? '') ?>"
                                       placeholder="+504 9999-9999" maxlength="25">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Contraseña <?= $empleado ? '' : '<span class="text-danger">*</span>' ?>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" name="password"
                                       id="password" placeholder="<?= $empleado ? 'Dejar vacío para no cambiar' : 'Mínimo 8 caracteres' ?>"
                                       minlength="8">
                                <button type="button" class="btn btn-outline-secondary"
                                        onclick="togglePassword()">
                                    <i class="fas fa-eye" id="iconPassword"></i>
                                </button>
                            </div>
                            <?php if ($empleado): ?>
                            <small class="text-muted">Dejar vacío para mantener la contraseña actual</small>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmar contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" name="password_confirm"
                                       id="passwordConfirm" placeholder="Repite la contraseña">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Rol -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <i class="fas fa-shield-alt me-2"></i>Rol del Empleado
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        El rol determina a qué módulos y acciones tiene acceso el empleado.
                    </p>
                    <input type="hidden" name="rol_id" id="inputRolId"
                           value="<?= htmlspecialchars($empleado['rol_id'] ?? '') ?>">

                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($roles as $rol): ?>
                        <div class="rol-card <?= $rol['es_admin'] ? 'admin' : '' ?> <?= ($empleado['rol_id'] ?? '') == $rol['id'] ? 'selected' : '' ?>"
                             data-id="<?= $rol['id'] ?>"
                             onclick="seleccionarRol(this)">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas <?= $rol['es_admin'] ? 'fa-crown text-danger' : 'fa-user-tag text-success' ?>"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-bold"><?= htmlspecialchars($rol['nombre']) ?></div>
                                    <?php if ($rol['descripcion']): ?>
                                    <small class="text-muted"><?= htmlspecialchars($rol['descripcion']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <i class="fas fa-check-circle text-success d-none check-icon"></i>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-3">
                        <a href="/Empleados/Roles" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-cog me-1"></i>Gestionar roles y permisos
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="/Empleados" class="btn btn-outline-secondary">
            <i class="fas fa-times me-2"></i>Cancelar
        </a>
        <button type="submit" class="btn btn-primary" id="btnGuardar">
            <i class="fas fa-save me-2"></i>
            <?= $empleado ? 'Guardar Cambios' : 'Crear Empleado' ?>
        </button>
    </div>

</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Marcar checkicons de rol ya seleccionado al cargar
document.querySelectorAll('.rol-card.selected').forEach(c => {
    c.querySelector('.check-icon')?.classList.remove('d-none');
});

function seleccionarRol(el) {
    document.querySelectorAll('.rol-card').forEach(c => {
        c.classList.remove('selected');
        c.querySelector('.check-icon')?.classList.add('d-none');
    });
    el.classList.add('selected');
    el.querySelector('.check-icon')?.classList.remove('d-none');
    document.getElementById('inputRolId').value = el.dataset.id;
}

function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('iconPassword');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.getElementById('empleadoForm').addEventListener('submit', function(e) {
    const nombre   = document.getElementById('nombre').value.trim();
    const email    = document.getElementById('email').value.trim();
    const username = document.getElementById('username').value.trim();
    const rolId    = document.getElementById('inputRolId').value;
    const pass     = document.getElementById('password').value;
    const passC    = document.getElementById('passwordConfirm').value;

    if (!nombre) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'El nombre es obligatorio.', confirmButtonColor:'#005C3E'
        });
        return;
    }
    if (!email) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'El correo es obligatorio.', confirmButtonColor:'#005C3E'
        });
        return;
    }
    if (!username) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'El nombre de usuario es obligatorio.', confirmButtonColor:'#005C3E'
        });
        return;
    }
    if (!rolId) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'Selecciona un rol.', confirmButtonColor:'#005C3E'
        });
        return;
    }
    if (pass && pass !== passC) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Las contraseñas no coinciden',
            text:'Verifica que ambas contraseñas sean iguales.', confirmButtonColor:'#005C3E'
        });
        return;
    }

    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
});
</script>