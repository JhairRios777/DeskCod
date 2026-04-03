<!-- Views/Perfil/index.php -->

<style>
.input-group-text { background:#005C3E; color:#fff; border-color:#005C3E; }
body.dark-mode .input-group-text { background:#1a3329; border-color:#1e3329; color:#e0e0e0; }
body.dark-mode .form-control {
    background:#111f18; border-color:#1e3329; color:#e0e0e0;
}
body.dark-mode .form-control:focus {
    background:#0f1a15; border-color:#00E676; box-shadow:0 0 0 3px rgba(0,230,118,0.1);
}
body.dark-mode .form-label { color:#c8e6d5; }
body.dark-mode .nav-tabs .nav-link { color:#adb5bd; border-color:#1e3329; }
body.dark-mode .nav-tabs .nav-link.active { background:#111f18; color:#00E676; border-color:#1e3329; }
body.dark-mode .card-footer { background:#111f18 !important; border-color:#1e3329 !important; }
body.dark-mode .table {
    --bs-table-color:#e0e0e0 !important; --bs-table-bg:transparent !important;
    --bs-table-border-color:#1e3329 !important; color:#e0e0e0 !important;
}
body.dark-mode .table > :not(caption) > * > * {
    background-color:transparent !important; color:#e0e0e0 !important;
    border-bottom-color:#1e3329 !important;
}
body.dark-mode .table thead > tr > * { background-color:#1a3329 !important; color:#fff !important; }

/* Avatar */
.avatar-wrap { position:relative; display:inline-block; }
.avatar-perfil {
    width:90px; height:90px; border-radius:50%;
    background:linear-gradient(135deg,#005C3E,#00E676);
    color:#fff; display:flex; align-items:center; justify-content:center;
    font-weight:700; font-size:2rem; border:3px solid var(--accent);
    overflow:hidden; flex-shrink:0;
}
.avatar-perfil img { width:100%; height:100%; object-fit:cover; }
.avatar-edit-btn {
    position:absolute; bottom:0; right:0;
    width:28px; height:28px; border-radius:50%;
    background:#005C3E; color:#fff; border:2px solid #fff;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; font-size:0.7rem; transition:background 0.2s;
}
.avatar-edit-btn:hover { background:#00895a; }

.actividad-item {
    padding:0.5rem 0; border-bottom:1px solid var(--border-color); font-size:0.85rem;
}
.actividad-item:last-child { border-bottom:none; }
</style>

<!-- Header -->
<div class="d-flex align-items-center gap-3 mb-4">
    <?php
    $fotoActual = $empleado['foto'] ?? null;
    $p   = explode(' ', $empleado['nombre']);
    $ini = strtoupper(substr($p[0],0,1).(isset($p[1])?substr($p[1],0,1):''));
    ?>
    <div class="avatar-wrap">
        <div class="avatar-perfil" id="avatarPerfil">
            <?php if ($fotoActual && file_exists(ROOT . $fotoActual)): ?>
                <img src="/<?= htmlspecialchars($fotoActual) ?>" alt="Foto de perfil" id="fotoPreview">
            <?php else: ?>
                <span id="avatarIniciales"><?= $ini ?></span>
            <?php endif; ?>
        </div>
        <div class="avatar-edit-btn" title="Cambiar foto"
             onclick="document.getElementById('inputFoto').click()">
            <i class="fas fa-camera"></i>
        </div>
    </div>
    <div>
        <h4 class="fw-bold mb-0"><?= htmlspecialchars($empleado['nombre']) ?></h4>
        <small class="text-muted">
            <?= htmlspecialchars($empleado['rol_nombre']) ?>
            <?php if ($empleado['es_admin']): ?>
            <span class="badge bg-danger ms-1"><i class="fas fa-crown me-1"></i>Admin</span>
            <?php endif; ?>
        </small>
    </div>
</div>

<!-- Form oculto para subir foto — se envía automáticamente -->
<form id="fotoForm" action="" method="POST" enctype="multipart/form-data" style="display:none;">
    <input type="hidden" name="ActualizarFoto" value="1">
    <input type="file" id="inputFoto" name="foto" accept="image/jpeg,image/png"
           onchange="subirFoto(this)">
</form>

<?php if (!empty($error)): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-times-circle fa-lg"></i>
    <span><?= htmlspecialchars($error) ?></span>
</div>
<?php endif; ?>

<?php if (!empty($success)): ?>
<div class="alert alert-success d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-check-circle fa-lg"></i>
    <span><?= htmlspecialchars($success) ?></span>
</div>
<?php endif; ?>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabDatos">
            <i class="fas fa-user me-2"></i>Mis datos
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabPassword">
            <i class="fas fa-lock me-2"></i>Contraseña
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabAccesos">
            <i class="fas fa-history me-2"></i>Últimos accesos
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabActividad">
            <i class="fas fa-bolt me-2"></i>Mi actividad
        </button>
    </li>
</ul>

<div class="tab-content">

    <!-- ── TAB: Datos ── -->
    <div class="tab-pane fade show active" id="tabDatos">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary">
                        <i class="fas fa-user me-2"></i>Información Personal
                    </div>
                    <form action="" method="POST">
                        <input type="hidden" name="ActualizarPerfil" value="1">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Nombre completo <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" name="nombre"
                                               value="<?= htmlspecialchars($empleado['nombre']) ?>" maxlength="100">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Correo electrónico <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" name="email"
                                               value="<?= htmlspecialchars($empleado['email']) ?>" maxlength="150">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Nombre de usuario <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                                        <input type="text" class="form-control" name="username"
                                               value="<?= htmlspecialchars($empleado['username']) ?>" maxlength="50">
                                    </div>
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
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info del rol -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary">
                        <i class="fas fa-shield-alt me-2"></i>Mi Rol
                    </div>
                    <div class="card-body text-center py-4">
                        <i class="fas <?= $empleado['es_admin'] ? 'fa-crown text-danger' : 'fa-user-tag text-success' ?> fa-3x mb-3"></i>
                        <h5 class="fw-bold"><?= htmlspecialchars($empleado['rol_nombre']) ?></h5>
                        <p class="text-muted small">
                            <?= $empleado['es_admin'] ? 'Acceso total al sistema' : 'Acceso según permisos asignados' ?>
                        </p>
                        <hr>
                        <div class="text-start small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Miembro desde:</span>
                                <span><?= date('d/m/Y', strtotime($empleado['created_at'])) ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Último acceso:</span>
                                <span><?= $empleado['ultimo_login']
                                    ? date('d/m/Y H:i', strtotime($empleado['ultimo_login']))
                                    : 'N/A' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── TAB: Contraseña ── -->
    <div class="tab-pane fade" id="tabPassword">
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary">
                        <i class="fas fa-lock me-2"></i>Cambiar Contraseña
                    </div>
                    <form action="" method="POST">
                        <input type="hidden" name="CambiarPassword" value="1">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Contraseña actual</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" name="password_actual" id="passActual">
                                        <button type="button" class="btn btn-outline-secondary"
                                                onclick="toggle('passActual','iconActual')">
                                            <i class="fas fa-eye" id="iconActual"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Nueva contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        <input type="password" class="form-control" name="password_nueva"
                                               id="passNueva" minlength="8" placeholder="Mínimo 8 caracteres">
                                        <button type="button" class="btn btn-outline-secondary"
                                                onclick="toggle('passNueva','iconNueva')">
                                            <i class="fas fa-eye" id="iconNueva"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Confirmar contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        <input type="password" class="form-control" name="password_confirm" id="passConfirm">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cambiar contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ── TAB: Últimos accesos ── -->
    <div class="tab-pane fade" id="tabAccesos">
        <div class="card shadow-sm">
            <div class="card-header bg-primary">
                <i class="fas fa-sign-in-alt me-2"></i>Últimos Accesos al Sistema
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr><th>Acción</th><th>IP</th><th>Fecha y hora</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($accesos)): ?>
                            <tr><td colspan="3" class="text-center py-4 text-muted">Sin registros de acceso</td></tr>
                            <?php else: ?>
                            <?php foreach ($accesos as $a): ?>
                            <tr>
                                <td>
                                    <?php if ($a['accion'] === 'LOGIN_OK'): ?>
                                    <span class="badge-activa"><span class="status-active-pulse me-1"></span>Inicio de sesión</span>
                                    <?php else: ?>
                                    <span class="badge-vencida"><i class="fas fa-sign-out-alt me-1"></i>Cierre de sesión</span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= htmlspecialchars($a['ip']) ?></code></td>
                                <td><?= date('d/m/Y H:i:s', strtotime($a['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ── TAB: Actividad reciente ── -->
    <div class="tab-pane fade" id="tabActividad">
        <div class="card shadow-sm">
            <div class="card-header bg-primary">
                <i class="fas fa-bolt me-2"></i>Mi Actividad Reciente
            </div>
            <div class="card-body">
                <?php if (empty($actividad)): ?>
                <p class="text-muted text-center py-4">Sin actividad registrada</p>
                <?php else: ?>
                <?php
                $iconosAccion = [
                    'CLIENTE_CREADO'         => ['fas fa-user-plus',  'text-success'],
                    'CLIENTE_ACTUALIZADO'    => ['fas fa-user-edit',   'text-warning'],
                    'CLIENTE_DESACTIVADO'    => ['fas fa-user-times',  'text-danger'],
                    'TICKET_CREADO'          => ['fas fa-ticket-alt',  'text-primary'],
                    'TICKET_ESTADO_CAMBIADO' => ['fas fa-exchange-alt','text-info'],
                    'PAGO_REGISTRADO'        => ['fas fa-dollar-sign', 'text-success'],
                    'PLAN_CREADO'            => ['fas fa-layer-group', 'text-primary'],
                    'EMPLEADO_CREADO'        => ['fas fa-user-tie',    'text-success'],
                    'PERFIL_ACTUALIZADO'     => ['fas fa-user-edit',   'text-warning'],
                    'PASSWORD_CAMBIADO'      => ['fas fa-key',         'text-danger'],
                    'FOTO_ACTUALIZADA'       => ['fas fa-camera',      'text-info'],
                    'CUENTA_CREADA'          => ['fas fa-file-invoice','text-primary'],
                ];
                foreach ($actividad as $a):
                    $cfg = $iconosAccion[$a['accion']] ?? ['fas fa-circle','text-muted'];
                ?>
                <div class="actividad-item d-flex align-items-center gap-3">
                    <i class="<?= $cfg[0] ?> <?= $cfg[1] ?>" style="width:20px;"></i>
                    <div class="flex-grow-1">
                        <span class="fw-semibold small"><?= str_replace('_',' ',$a['accion']) ?></span>
                        <span class="text-muted small ms-1">en <?= $a['tabla'] ?> #<?= $a['registro_id'] ?></span>
                    </div>
                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($a['created_at'])) ?></small>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ── Toggle contraseña ──
function toggle(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye','fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash','fa-eye');
    }
}

// ── Subir foto automáticamente al seleccionar ──
function subirFoto(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];

    if (file.size > 3 * 1024 * 1024) {
        Swal.fire({ icon:'error', title:'Imagen muy grande',
            text:'La foto no puede superar 3MB.', confirmButtonColor:'#005C3E' });
        input.value = '';
        return;
    }

    // Preview inmediato antes de subir
    const reader = new FileReader();
    reader.onload = function(e) {
        const avatar = document.getElementById('avatarPerfil');
        avatar.innerHTML = `<img src="${e.target.result}" alt="Foto" style="width:100%;height:100%;object-fit:cover;">`;
    };
    reader.readAsDataURL(file);

    // Envía el formulario automáticamente
    Swal.fire({
        title: '¿Actualizar foto de perfil?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#005C3E',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, actualizar',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('fotoForm').submit();
        } else {
            input.value = '';
            // Restaura avatar original
            <?php if ($fotoActual && file_exists(ROOT . $fotoActual)): ?>
            document.getElementById('avatarPerfil').innerHTML =
                `<img src="/<?= htmlspecialchars($fotoActual) ?>" alt="Foto" style="width:100%;height:100%;object-fit:cover;">`;
            <?php else: ?>
            document.getElementById('avatarPerfil').innerHTML = `<span><?= $ini ?></span>`;
            <?php endif; ?>
        }
    });
}
</script>