<!-- Views/Clientes/Registry.php -->

<style>
.input-group-text { background:#005C3E; color:#fff; border-color:#005C3E; }

/* Cards visuales de plan */
.plan-card {
    border-radius: 12px; padding: 1rem;
    cursor: pointer; transition: all 0.2s;
    border: 2px solid transparent; text-align: center;
}
.plan-card:hover { transform: translateY(-2px); }
.plan-card.plan-basico   { background: rgba(108,117,125,0.1); border-color: #6c757d; }
.plan-card.plan-estandar { background: rgba(13,110,253,0.1);  border-color: #0d6efd; }
.plan-card.plan-premium  { background: rgba(255,193,7,0.12);  border-color: #ffc107; }
.plan-card.selected.plan-basico   { background: rgba(108,117,125,0.25); box-shadow: 0 4px 15px rgba(108,117,125,0.3); }
.plan-card.selected.plan-estandar { background: rgba(13,110,253,0.2);   box-shadow: 0 4px 15px rgba(13,110,253,0.3); }
.plan-card.selected.plan-premium  { background: rgba(255,193,7,0.25);   box-shadow: 0 4px 15px rgba(255,193,7,0.3); }

/* Dark mode */
body.dark-mode .input-group-text { background:#1a3329; border-color:#1e3329; color:#e0e0e0; }
body.dark-mode .form-control, body.dark-mode .form-select {
    background:#111f18; border-color:#1e3329; color:#e0e0e0;
}
body.dark-mode .form-control:focus, body.dark-mode .form-select:focus {
    background:#0f1a15; border-color:#00E676;
    box-shadow:0 0 0 3px rgba(0,230,118,0.1); color:#e0e0e0;
}
body.dark-mode .form-label { color:#c8e6d5; }
body.dark-mode .plan-card.plan-basico   { background: rgba(108,117,125,0.15); }
body.dark-mode .plan-card.plan-estandar { background: rgba(13,110,253,0.15); }
body.dark-mode .plan-card.plan-premium  { background: rgba(255,193,7,0.15); }
body.dark-mode .plan-card.selected.plan-basico   { background: rgba(108,117,125,0.3); }
body.dark-mode .plan-card.selected.plan-estandar { background: rgba(13,110,253,0.3); }
body.dark-mode .plan-card.selected.plan-premium  { background: rgba(255,193,7,0.25); }
body.dark-mode .info-row { border-color: #1e3329; }
body.dark-mode .info-row .label { color: #adb5bd; }
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

<form id="clienteForm" action="" method="POST">
    <input type="hidden" name="Registrar" value="1">
    <input type="hidden" name="id" value="<?= $cliente['id'] ?? '' ?>">

    <div class="row g-4">

        <!-- ── DATOS DEL CLIENTE ── -->
        <div class="col-lg-<?= $cliente ? '12' : '8' ?>">
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <i class="fas fa-user me-2"></i>Datos del Cliente
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
                                       value="<?= htmlspecialchars($cliente['nombre'] ?? '') ?>"
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

            <?php if ($cliente): ?>
            <!-- ── PANEL DE INFO SUSCRIPCIÓN AL EDITAR ── -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-sync-alt me-2"></i>Suscripción Actual</span>
                    <a href="/Suscripciones?cliente=<?= $cliente['id'] ?>"
                       class="btn btn-accent btn-sm">
                        <i class="fas fa-external-link-alt me-1"></i>Gestionar suscripción
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($cliente['plan_nombre'])): ?>
                    <?php
                    $planActual   = strtolower($cliente['plan_nombre']);
                    $bgActual     = str_contains($planActual,'premium')
                        ? 'rgba(255,193,7,0.12)' : (str_contains($planActual,'estándar') || str_contains($planActual,'estandar')
                        ? 'rgba(13,110,253,0.08)' : 'rgba(108,117,125,0.08)');
                    $borderActual = str_contains($planActual,'premium') ? '#ffc107'
                        : (str_contains($planActual,'estándar') || str_contains($planActual,'estandar') ? '#0d6efd' : '#6c757d');
                    $dias   = (int)((strtotime($cliente['fecha_vencimiento']) - time()) / 86400);
                    $claseD = $dias > 7 ? 'text-success' : ($dias > 0 ? 'text-warning' : 'text-danger');
                    ?>
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background:<?= $bgActual ?>;border:2px solid <?= $borderActual ?>;">
                                <p class="text-muted small mb-1">Plan activo</p>
                                <h5 class="fw-bold mb-1"><?= htmlspecialchars($cliente['plan_nombre']) ?></h5>
                                <span class="badge bg-success">$<?= number_format($cliente['plan_precio'],2) ?>/mes</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-2">
                                <?php
                                $est = $cliente['suscripcion_estado'] ?? '';
                                $badges = [
                                    'activa'     => '<span class="badge-activa"><span class="status-active-pulse me-1"></span>Activa</span>',
                                    'por_vencer' => '<span class="badge-por-vencer"><i class="fas fa-clock me-1"></i>Por vencer</span>',
                                    'vencida'    => '<span class="badge-vencida"><i class="fas fa-times-circle me-1"></i>Vencida</span>',
                                    'suspendida' => '<span class="badge-suspendida"><i class="fas fa-pause-circle me-1"></i>Suspendida</span>',
                                ];
                                echo $badges[$est] ?? '';
                                ?>
                                <small class="<?= $claseD ?> fw-semibold">
                                    <i class="fas fa-calendar me-1"></i>
                                    Vence: <?= date('d/m/Y', strtotime($cliente['fecha_vencimiento'])) ?>
                                    (<?= max(0,$dias) ?> días restantes)
                                </small>
                                <?php if (!empty($cliente['renovacion_plan_id'])): ?>
                                <small class="text-info">
                                    <i class="fas fa-arrow-right me-1"></i>
                                    Cambio programado al vencer
                                </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-exclamation-circle fa-2x mb-2 d-block opacity-50"></i>
                        <p class="mb-2">Este cliente no tiene suscripción activa.</p>
                        <a href="/Suscripciones?cliente=<?= $cliente['id'] ?>"
                           class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Crear suscripción
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- ── PLAN INICIAL (solo al crear) ── -->
        <?php if (!$cliente): ?>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary">
                    <i class="fas fa-sync-alt me-2"></i>Plan Inicial
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Opcional — puedes asignarlo después desde
                        <strong>Suscripciones</strong>.
                    </p>

                    <input type="hidden" name="plan_id" id="inputPlanId" value="">

                    <!-- Cards visuales de plan -->
                    <div class="d-flex flex-column gap-2 mb-3">
                        <?php foreach ($planes as $plan):
                            $pNombre = strtolower($plan['nombre']);
                            $pClase  = str_contains($pNombre,'premium') ? 'plan-premium'
                                : (str_contains($pNombre,'estándar') || str_contains($pNombre,'estandar')
                                ? 'plan-estandar' : 'plan-basico');
                            $pIcon   = str_contains($pNombre,'premium') ? 'fas fa-crown'
                                : (str_contains($pNombre,'estándar') || str_contains($pNombre,'estandar')
                                ? 'fas fa-star' : 'fas fa-leaf');
                        ?>
                        <div class="plan-card <?= $pClase ?>"
                             data-id="<?= $plan['id'] ?>"
                             data-duracion="<?= $plan['duracion_dias'] ?>"
                             onclick="seleccionarPlan(this)">
                            <div class="d-flex align-items-center gap-3">
                                <i class="<?= $pIcon ?>" style="font-size:1.4rem;"></i>
                                <div class="text-start">
                                    <div class="fw-bold"><?= htmlspecialchars($plan['nombre']) ?></div>
                                    <div class="small text-muted">
                                        $<?= number_format($plan['precio'],2) ?>/mes
                                        · <?= $plan['duracion_dias'] ?> días
                                    </div>
                                </div>
                                <i class="fas fa-check-circle ms-auto d-none check-icon text-success"></i>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Fechas (aparecen al seleccionar plan) -->
                    <div id="camposFecha" style="display:none;">
                        <hr>
                        <div class="mb-2">
                            <label class="form-label fw-semibold small">Fecha de inicio</label>
                            <input type="date" class="form-control form-control-sm"
                                   name="fecha_inicio" id="fechaInicio"
                                   value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-semibold small">Fecha de vencimiento</label>
                            <input type="date" class="form-control form-control-sm"
                                   name="fecha_vencimiento" id="fechaVencimiento">
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

                </div>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /row -->

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
// ── Seleccionar plan ──
function seleccionarPlan(el) {
    // Quita selección anterior
    document.querySelectorAll('.plan-card').forEach(c => {
        c.classList.remove('selected');
        c.querySelector('.check-icon')?.classList.add('d-none');
    });

    // Marca el seleccionado
    el.classList.add('selected');
    el.querySelector('.check-icon')?.classList.remove('d-none');
    document.getElementById('inputPlanId').value = el.dataset.id;

    // Muestra campos de fecha
    document.getElementById('camposFecha').style.display = 'block';
    calcularVencimiento(el.dataset.duracion);
}

// ── Limpiar plan seleccionado ──
function limpiarPlan() {
    document.querySelectorAll('.plan-card').forEach(c => {
        c.classList.remove('selected');
        c.querySelector('.check-icon')?.classList.add('d-none');
    });
    document.getElementById('inputPlanId').value = '';
    document.getElementById('camposFecha').style.display = 'none';
}

// ── Calcula fecha vencimiento ──
function calcularVencimiento(duracion) {
    const fi = document.getElementById('fechaInicio');
    const fv = document.getElementById('fechaVencimiento');
    if (!fi || !fv) return;
    const d = new Date(fi.value);
    d.setDate(d.getDate() + parseInt(duracion || 30));
    fv.value = d.toISOString().split('T')[0];
}

const fi = document.getElementById('fechaInicio');
if (fi) {
    fi.addEventListener('change', function () {
        const sel = document.querySelector('.plan-card.selected');
        if (sel) calcularVencimiento(sel.dataset.duracion);
    });
}

// ── Validación submit ──
document.getElementById('clienteForm').addEventListener('submit', function (e) {
    const nombre = document.getElementById('nombre').value.trim();
    const email  = document.getElementById('email').value.trim();

    if (!nombre) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'El nombre del cliente es obligatorio.',
            confirmButtonColor:'#005C3E'
        }).then(() => document.getElementById('nombre').focus());
        return;
    }

    if (!email) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'El correo electrónico es obligatorio.',
            confirmButtonColor:'#005C3E'
        }).then(() => document.getElementById('email').focus());
        return;
    }

    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
});
</script>