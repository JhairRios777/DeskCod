<!-- Views/Clientes/Registry.php -->

<style>
.input-group-text { background:#005C3E; color:#fff; border-color:#005C3E; }
html.dark-mode .input-group-text { background:#1a3329; border-color:#1e3329; color:#e0e0e0; }
html.dark-mode .form-control, html.dark-mode .form-select {
    background:#111f18; border-color:#1e3329; color:#e0e0e0;
}
html.dark-mode .form-control:focus, html.dark-mode .form-select:focus {
    background:#0f1a15; border-color:#00E676; box-shadow:0 0 0 3px rgba(0,230,118,0.1);
}
html.dark-mode .form-label { color:#c8e6d5; }

/* Plan cards */
.plan-card {
    border-radius:12px; padding:.85rem 1rem; cursor:pointer;
    transition:all 0.2s; border:2px solid transparent;
}
.plan-card:hover { transform:translateY(-2px); }
.plan-card.plan-basico   { background:rgba(108,117,125,0.1); border-color:#6c757d; }
.plan-card.plan-estandar { background:rgba(13,110,253,0.1);  border-color:#0d6efd; }
.plan-card.plan-premium  { background:rgba(255,193,7,0.12);  border-color:#ffc107; }
.plan-card.selected.plan-basico   { background:rgba(108,117,125,0.25); box-shadow:0 4px 15px rgba(108,117,125,0.3); }
.plan-card.selected.plan-estandar { background:rgba(13,110,253,0.2);   box-shadow:0 4px 15px rgba(13,110,253,0.3); }
.plan-card.selected.plan-premium  { background:rgba(255,193,7,0.25);   box-shadow:0 4px 15px rgba(255,193,7,0.3); }
html.dark-mode .plan-card.plan-basico   { background:rgba(108,117,125,0.15); }
html.dark-mode .plan-card.plan-estandar { background:rgba(13,110,253,0.15); }
html.dark-mode .plan-card.plan-premium  { background:rgba(255,193,7,0.15); }

/* Toggle mensual/anual */
.periodo-toggle {
    display:flex; border-radius:8px; overflow:hidden;
    border:2px solid #005C3E; width:100%;
}
.periodo-btn {
    flex:1; padding:.5rem .75rem; text-align:center;
    cursor:pointer; font-weight:600; font-size:.8rem;
    transition:all 0.2s; border:none; background:transparent; color:#005C3E;
}
.periodo-btn.activo { background:#005C3E; color:#fff; }
html.dark-mode .periodo-toggle { border-color:#00E676; }
html.dark-mode .periodo-btn { color:#00E676; }
html.dark-mode .periodo-btn.activo { background:#005C3E; color:#fff; }

/* Badge descuento */
.badge-off {
    background:linear-gradient(135deg,#dc3545,#ff6b6b);
    color:#fff; font-size:.65rem; padding:2px 6px;
    border-radius:20px; font-weight:700; vertical-align:middle;
}

/* Resumen precio */
.precio-resumen {
    background:rgba(0,92,62,0.06);
    border:1px solid rgba(0,92,62,0.2);
    border-radius:8px; padding:.75rem;
}
html.dark-mode .precio-resumen {
    background:rgba(0,230,118,0.05);
    border-color:rgba(0,230,118,0.15);
}

/* Logo upload */
.logo-wrap { position:relative; display:inline-block; }
.logo-preview-circle {
    width:80px; height:80px; border-radius:50%;
    background:linear-gradient(135deg,#005C3E,#00E676);
    color:#fff; display:flex; align-items:center; justify-content:center;
    font-weight:700; font-size:1.5rem;
    overflow:hidden; border:3px solid var(--accent); cursor:pointer;
}
.logo-preview-circle img { width:100%; height:100%; object-fit:cover; }
.logo-edit-btn {
    position:absolute; bottom:0; right:0;
    width:26px; height:26px; border-radius:50%;
    background:#005C3E; color:#fff; border:2px solid #fff;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; font-size:0.65rem;
}
.logo-edit-btn:hover { background:#00895a; }

.token-field { font-family:'Courier New',monospace; font-size:.8rem; letter-spacing:.03em; }
.bg-accent { background-color:#00E676 !important; }
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <?php if ($cliente): ?>
                <i class="fas fa-edit me-2 text-warning"></i>Editar Cliente
            <?php else: ?>
                <i class="fas fa-user-plus me-2 text-success"></i>Nuevo Cliente
            <?php endif; ?>
        </h4>
    </div>
    <a href="/Clientes" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Regresar
    </a>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-times-circle fa-lg"></i><span><?= htmlspecialchars($error) ?></span>
</div>
<?php endif; ?>
<?php if (!empty($success)): ?>
<div class="alert alert-success d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-check-circle fa-lg"></i><span><?= htmlspecialchars($success) ?></span>
</div>
<?php endif; ?>

<form id="clienteForm" action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="Registrar" value="1">
    <input type="hidden" name="id" value="<?= $cliente['id'] ?? '' ?>">
    <input type="hidden" name="tipo_periodo" id="tipoPeriodo" value="mensual">

    <div class="row g-4">

        <!-- DATOS DEL CLIENTE -->
        <div class="col-lg-<?= $cliente ? '12' : '8' ?>">
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <i class="fas fa-user me-2"></i>Datos del Cliente
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <!-- Logo -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Logo de la empresa</label>
                            <div class="d-flex align-items-center gap-3">
                                <?php
                                $p         = explode(' ', $cliente['nombre'] ?? 'C');
                                $ini       = strtoupper(substr($p[0],0,1).(isset($p[1])?substr($p[1],0,1):''));
                                $tienelogo = !empty($cliente['logo']) && file_exists(ROOT.($cliente['logo']??''));
                                ?>
                                <div class="logo-wrap">
                                    <div class="logo-preview-circle" id="logoPreview"
                                         onclick="document.getElementById('inputLogo').click()">
                                        <?php if ($tienelogo): ?>
                                            <img src="/<?= htmlspecialchars($cliente['logo']) ?>" alt="logo" id="logoImg">
                                        <?php else: ?>
                                            <span id="logoIniciales"><?= $ini ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="logo-edit-btn" onclick="document.getElementById('inputLogo').click()">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-1 small fw-semibold">Haz clic en el círculo para subir un logo</p>
                                    <small class="text-muted">JPG, PNG — máx. 3MB — Opcional</small>
                                    <?php if ($tienelogo): ?>
                                    <br><small class="text-success"><i class="fas fa-check me-1"></i>Logo cargado</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <input type="file" id="inputLogo" name="logo"
                                   accept="image/jpeg,image/png" style="display:none;"
                                   onchange="previsualizarLogo(this)">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nombre completo <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" name="nombre" id="nombre"
                                       value="<?= htmlspecialchars($cliente['nombre'] ?? '') ?>"
                                       placeholder="Ej. Juan Pérez" maxlength="100"
                                       oninput="actualizarIniciales(this.value)">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Correo electrónico <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" name="email" id="email"
                                       value="<?= htmlspecialchars($cliente['email'] ?? '') ?>"
                                       placeholder="correo@empresa.com" maxlength="150">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                <input type="tel" class="form-control" name="telefono"
                                       value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>"
                                       placeholder="+504 9999-9999" maxlength="25">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre de la empresa</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                <input type="text" class="form-control" name="empresa_nombre"
                                       value="<?= htmlspecialchars($cliente['empresa_nombre'] ?? '') ?>"
                                       placeholder="Ej. Zona Marcol S.A." maxlength="150">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">RTN / NIT / RUC</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input type="text" class="form-control" name="nit_ruc"
                                       value="<?= htmlspecialchars($cliente['nit_ruc'] ?? '') ?>"
                                       placeholder="Número fiscal" maxlength="30">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Dirección</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" class="form-control" name="direccion"
                                       value="<?= htmlspecialchars($cliente['direccion'] ?? '') ?>"
                                       placeholder="Ciudad, País" maxlength="200">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Suscripción actual (solo al editar) -->
            <?php if ($cliente): ?>
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-primary d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-sync-alt me-2"></i>Suscripción Actual</span>
                    <a href="/Suscripciones?cliente=<?= $cliente['id'] ?>" class="btn btn-accent btn-sm">
                        <i class="fas fa-external-link-alt me-1"></i>Gestionar
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($cliente['plan_nombre'])): ?>
                    <?php
                    $planActual   = strtolower($cliente['plan_nombre']);
                    $dias         = (int)((strtotime($cliente['fecha_vencimiento']) - time()) / 86400);
                    $claseD       = $dias > 7 ? 'text-success' : ($dias > 0 ? 'text-warning' : 'text-danger');
                    $bgActual     = str_contains($planActual,'premium') ? 'rgba(255,193,7,0.12)'
                        : (str_contains($planActual,'est') ? 'rgba(13,110,253,0.08)' : 'rgba(108,117,125,0.08)');
                    $borderActual = str_contains($planActual,'premium') ? '#ffc107'
                        : (str_contains($planActual,'est') ? '#0d6efd' : '#6c757d');
                    ?>
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <div class="p-3 rounded" style="background:<?= $bgActual ?>;border:2px solid <?= $borderActual ?>;">
                                <p class="text-muted small mb-1">Plan activo</p>
                                <h5 class="fw-bold mb-1"><?= htmlspecialchars($cliente['plan_nombre']) ?></h5>
                                <span class="badge bg-success">L.<?= number_format($cliente['plan_precio'],2) ?>/mes</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php
                            $est    = $cliente['suscripcion_estado'] ?? '';
                            $badges = [
                                'activa'     => '<span class="badge-activa"><span class="status-active-pulse me-1"></span>Activa</span>',
                                'por_vencer' => '<span class="badge-por-vencer"><i class="fas fa-clock me-1"></i>Por vencer</span>',
                                'vencida'    => '<span class="badge-vencida"><i class="fas fa-times-circle me-1"></i>Vencida</span>',
                                'suspendida' => '<span class="badge-suspendida"><i class="fas fa-pause-circle me-1"></i>Suspendida</span>',
                            ];
                            echo $badges[$est] ?? '';
                            ?>
                            <small class="<?= $claseD ?> fw-semibold d-block mt-2">
                                <i class="fas fa-calendar me-1"></i>
                                Vence: <?= date('d/m/Y', strtotime($cliente['fecha_vencimiento'])) ?>
                                (<?= max(0,$dias) ?> días)
                            </small>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-exclamation-circle fa-2x mb-2 d-block opacity-50"></i>
                        <p class="mb-2">Sin suscripción activa.</p>
                        <a href="/Suscripciones?cliente=<?= $cliente['id'] ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Crear suscripción
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TOKEN API -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-primary d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-key me-2"></i>Token de API</span>
                    <span class="badge bg-accent text-dark">Solo lectura</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Este token identifica al sistema del cliente ante la API de DeskCod.
                        Compártelo de forma segura.
                    </p>
                    <?php if ($apiToken): ?>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="text" class="form-control token-field" id="apiTokenInput"
                               value="<?= htmlspecialchars($apiToken) ?>" readonly>
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="copiarToken()" title="Copiar">
                            <i class="fas fa-copy" id="iconCopiar"></i>
                        </button>
                        <button type="button" class="btn btn-outline-warning"
                                onclick="confirmarRegenerar(<?= $cliente['id'] ?>)" title="Regenerar">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle me-1"></i>
                        Header: <code>Authorization: Bearer <?= substr($apiToken,0,8) ?>...</code>
                    </small>
                    <?php else: ?>
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-muted small">
                            <i class="fas fa-exclamation-circle me-1"></i>Sin token generado.
                        </div>
                        <button type="button" class="btn btn-primary btn-sm"
                                onclick="generarTokenNuevo(<?= $cliente['id'] ?>)">
                            <i class="fas fa-plus me-1"></i>Generar token
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- PLAN INICIAL (solo al crear) -->
        <?php if (!$cliente): ?>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header" style="background:#005C3E;color:#fff;">
                    <i class="fas fa-sync-alt me-2"></i>Plan Inicial
                </div>
                <div class="card-body d-flex flex-column gap-3">

                    <p class="text-muted small mb-0">
                        Opcional — puedes asignarlo después desde <strong>Suscripciones</strong>.
                    </p>

                    <!-- Toggle mensual/anual -->
                    <div id="bloqueToggle" style="display:none;">
                        <label class="form-label fw-semibold small mb-1">Tipo de período</label>
                        <div class="periodo-toggle">
                            <button type="button" class="periodo-btn activo" id="btnMensual"
                                    onclick="seleccionarPeriodo('mensual')">
                                <i class="fas fa-calendar me-1"></i>Mensual
                            </button>
                            <button type="button" class="periodo-btn" id="btnAnual"
                                    onclick="seleccionarPeriodo('anual')">
                                <i class="fas fa-calendar-alt me-1"></i>Anual
                                <span class="badge-off ms-1 d-none" id="badgeDescuento"></span>
                            </button>
                        </div>
                        <small class="text-muted mt-1 d-block" id="textoDescuento"></small>
                    </div>

                    <!-- Cards de planes -->
                    <input type="hidden" name="plan_id" id="inputPlanId" value="">
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($planes as $plan):
                            $pN   = strtolower($plan['nombre']);
                            $pCls = str_contains($pN,'premium') ? 'plan-premium'
                                : (str_contains($pN,'est') ? 'plan-estandar' : 'plan-basico');
                            $pIcon = str_contains($pN,'premium') ? 'fas fa-crown'
                                : (str_contains($pN,'est') ? 'fas fa-star' : 'fas fa-leaf');
                            $tieneDescuento = (float)$plan['descuento_anual'] > 0;
                        ?>
                        <div class="plan-card <?= $pCls ?>"
                             data-id="<?= $plan['id'] ?>"
                             data-duracion="<?= $plan['duracion_dias'] ?>"
                             data-precio="<?= $plan['precio'] ?>"
                             data-precio-anual="<?= $plan['precio_anual'] ?>"
                             data-ahorro="<?= $plan['ahorro_anual'] ?>"
                             data-descuento="<?= $plan['descuento_anual'] ?>"
                             data-nombre="<?= htmlspecialchars($plan['nombre'], ENT_QUOTES) ?>"
                             onclick="seleccionarPlan(this)">
                            <div class="d-flex align-items-center gap-2">
                                <i class="<?= $pIcon ?>" style="font-size:1.2rem;"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-bold small"><?= htmlspecialchars($plan['nombre']) ?></div>
                                    <div class="precio-mensual-display" style="font-size:.75rem;" class="text-muted">
                                        L.<?= number_format($plan['precio'],2) ?>/mes
                                    </div>
                                    <?php if ($tieneDescuento): ?>
                                    <div class="precio-anual-display" style="font-size:.75rem;display:none;">
                                        L.<?= number_format($plan['precio_anual'],2) ?>/año
                                        <span class="badge-off"><?= number_format($plan['descuento_anual'],0) ?>% OFF</span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <i class="fas fa-check-circle text-success d-none check-icon"></i>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Campos de fecha + resumen (aparecen al seleccionar plan) -->
                    <div id="camposFecha" style="display:none;">
                        <hr class="my-2">

                        <!-- Resumen precio -->
                        <div class="precio-resumen mb-3 d-none" id="resumenPrecio">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small text-muted">Total a cobrar</div>
                                    <div class="fw-bold text-success" id="resumenTotal"></div>
                                </div>
                                <div class="text-end d-none" id="bloqueAhorro">
                                    <div class="small text-success" id="resumenAhorro"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-semibold small">Fecha de inicio</label>
                            <input type="date" class="form-control form-control-sm"
                                   name="fecha_inicio" id="fechaInicio" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-semibold small">
                                Fecha de vencimiento
                                <small class="text-muted fw-normal">(calculada automáticamente)</small>
                            </label>
                            <input type="date" class="form-control form-control-sm"
                                   name="fecha_vencimiento" id="fechaVencimiento" readonly>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-semibold small">Notas</label>
                            <textarea class="form-control form-control-sm"
                                      name="notas_suscripcion" rows="2"
                                      placeholder="Observaciones..."></textarea>
                        </div>
                        <button type="button" class="btn btn-link btn-sm text-muted p-0"
                                onclick="limpiarPlan()">
                            <i class="fas fa-times me-1"></i>Quitar plan
                        </button>
                    </div>

                    <hr class="mt-auto mb-0">
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <i class="fas fa-key text-success"></i>
                        <span>Se generará un token de API automáticamente al registrar.</span>
                    </div>

                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- Botones -->
    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="/Clientes" class="btn btn-outline-secondary">
            <i class="fas fa-times me-2"></i>Cancelar
        </a>
        <button type="submit" class="btn btn-primary" id="btnGuardar">
            <i class="fas fa-save me-2"></i>
            <?= $cliente ? 'Guardar Cambios' : 'Registrar Cliente' ?>
        </button>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ── Estado global ──
let periodoActual    = 'mensual';
let planSeleccionado = null;

// ── Preview logo ──
function previsualizarLogo(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    if (file.size > 3 * 1024 * 1024) {
        Swal.fire({ icon:'error', title:'Imagen muy grande',
            text:'El logo no puede superar 3MB.', confirmButtonColor:'#005C3E' });
        input.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('logoPreview').innerHTML =
            `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
    };
    reader.readAsDataURL(file);
}

function actualizarIniciales(nombre) {
    const ini = document.getElementById('logoIniciales');
    if (!ini) return;
    const p = nombre.trim().split(' ');
    ini.textContent = (p[0][0] || '').toUpperCase() + (p[1] ? p[1][0].toUpperCase() : '');
}

// ── Selector de período ──
function seleccionarPeriodo(periodo) {
    periodoActual = periodo;
    document.getElementById('tipoPeriodo').value = periodo;

    document.getElementById('btnMensual').classList.toggle('activo', periodo === 'mensual');
    document.getElementById('btnAnual').classList.toggle('activo', periodo === 'anual');

    // Muestra precio correcto en cada card
    document.querySelectorAll('.precio-mensual-display').forEach(el => {
        el.style.display = periodo === 'mensual' ? '' : 'none';
    });
    document.querySelectorAll('.precio-anual-display').forEach(el => {
        el.style.display = periodo === 'anual' ? '' : 'none';
    });

    if (planSeleccionado) {
        calcularVencimiento();
        actualizarResumen();
    }
}

// ── Selección de plan ──
function seleccionarPlan(el) {
    document.querySelectorAll('.plan-card').forEach(c => {
        c.classList.remove('selected');
        c.querySelector('.check-icon')?.classList.add('d-none');
    });
    el.classList.add('selected');
    el.querySelector('.check-icon')?.classList.remove('d-none');

    planSeleccionado = {
        id:          el.dataset.id,
        nombre:      el.dataset.nombre,
        duracion:    parseInt(el.dataset.duracion),
        precio:      parseFloat(el.dataset.precio),
        precioAnual: parseFloat(el.dataset.precioAnual),
        ahorro:      parseFloat(el.dataset.ahorro),
        descuento:   parseFloat(el.dataset.descuento),
    };

    document.getElementById('inputPlanId').value = planSeleccionado.id;

    // Muestra toggle y campos de fecha
    document.getElementById('bloqueToggle').style.display = '';
    document.getElementById('camposFecha').style.display  = 'block';

    // Badge y texto de descuento en botón anual
    const badge = document.getElementById('badgeDescuento');
    const texto = document.getElementById('textoDescuento');
    if (planSeleccionado.descuento > 0) {
        badge.textContent = planSeleccionado.descuento + '% OFF';
        badge.classList.remove('d-none');
        texto.textContent = `Ahorra L.${planSeleccionado.ahorro.toFixed(2)} pagando anualmente.`;
    } else {
        badge.classList.add('d-none');
        texto.textContent = 'Este plan no tiene descuento anual.';
    }

    calcularVencimiento();
    actualizarResumen();
}

function limpiarPlan() {
    document.querySelectorAll('.plan-card').forEach(c => {
        c.classList.remove('selected');
        c.querySelector('.check-icon')?.classList.add('d-none');
    });
    planSeleccionado = null;
    periodoActual    = 'mensual';
    document.getElementById('tipoPeriodo').value          = 'mensual';
    document.getElementById('inputPlanId').value          = '';
    document.getElementById('bloqueToggle').style.display = 'none';
    document.getElementById('camposFecha').style.display  = 'none';
    document.getElementById('resumenPrecio').classList.add('d-none');
    document.getElementById('btnMensual').classList.add('activo');
    document.getElementById('btnAnual').classList.remove('activo');
    document.querySelectorAll('.precio-mensual-display').forEach(el => el.style.display = '');
    document.querySelectorAll('.precio-anual-display').forEach(el => el.style.display = 'none');
}

// ── Calcula fecha de vencimiento ──
function calcularVencimiento() {
    if (!planSeleccionado) return;
    const fi = document.getElementById('fechaInicio').value;
    if (!fi) return;
    const d = new Date(fi);
    if (periodoActual === 'anual') {
        d.setDate(d.getDate() + 365);
    } else {
        d.setDate(d.getDate() + planSeleccionado.duracion);
    }
    document.getElementById('fechaVencimiento').value = d.toISOString().split('T')[0];
}

// ── Actualiza resumen de precio ──
function actualizarResumen() {
    if (!planSeleccionado) return;

    const resumen = document.getElementById('resumenPrecio');
    resumen.classList.remove('d-none');

    const total = periodoActual === 'anual'
        ? planSeleccionado.precioAnual
        : planSeleccionado.precio;

    document.getElementById('resumenTotal').textContent = 'L.' + total.toFixed(2)
        + (periodoActual === 'anual' ? '/año' : '/mes');

    const bloqueAhorro = document.getElementById('bloqueAhorro');
    if (periodoActual === 'anual' && planSeleccionado.ahorro > 0) {
        document.getElementById('resumenAhorro').textContent =
            'Ahorro: L.' + planSeleccionado.ahorro.toFixed(2);
        bloqueAhorro.classList.remove('d-none');
    } else {
        bloqueAhorro.classList.add('d-none');
    }
}

// Recalcula al cambiar fecha de inicio
const fiInput = document.getElementById('fechaInicio');
if (fiInput) fiInput.addEventListener('change', calcularVencimiento);

// ── Token API ──
function copiarToken() {
    const input = document.getElementById('apiTokenInput');
    if (!input) return;
    navigator.clipboard.writeText(input.value).then(() => {
        const icon = document.getElementById('iconCopiar');
        icon.classList.replace('fa-copy', 'fa-check');
        setTimeout(() => icon.classList.replace('fa-check', 'fa-copy'), 2000);
    });
}

function confirmarRegenerar(clienteId) {
    Swal.fire({
        title: '¿Regenerar token?',
        html: 'El token anterior <strong>dejará de funcionar inmediatamente</strong>.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor:'#dc3545', cancelButtonColor:'#6c757d',
        confirmButtonText:'Sí, regenerar', cancelButtonText:'Cancelar'
    }).then(r => { if (r.isConfirmed) llamarRegenerar(clienteId); });
}

function generarTokenNuevo(clienteId) { llamarRegenerar(clienteId, true); }

function llamarRegenerar(clienteId, esNuevo = false) {
    const fd = new FormData();
    fd.append('id', clienteId);
    fetch('/Clientes/regenerarToken', { method:'POST', body:fd, credentials:'same-origin' })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon:'success',
                    title: esNuevo ? 'Token generado' : 'Token regenerado',
                    html: `<p class="mb-2">Copia el nuevo token:</p>
                           <code class="d-block p-2 bg-light rounded" style="font-size:.75rem;word-break:break-all;">
                               ${data.token}
                           </code>`,
                    confirmButtonColor:'#005C3E', confirmButtonText:'Copiar y cerrar'
                }).then(() => { navigator.clipboard.writeText(data.token); location.reload(); });
            } else {
                Swal.fire({ icon:'error', title:'Error', text:data.message });
            }
        });
}

// ── Validación ──
document.getElementById('clienteForm').addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    const email  = document.getElementById('email').value.trim();
    if (!nombre) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'El nombre del cliente es obligatorio.', confirmButtonColor:'#005C3E' });
        return;
    }
    if (!email) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'El correo es obligatorio.', confirmButtonColor:'#005C3E' });
        return;
    }
    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
});
</script>