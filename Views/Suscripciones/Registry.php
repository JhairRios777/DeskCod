<!-- Views/Suscripciones/Registry.php -->

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
    border-radius:12px; padding:1rem; cursor:pointer;
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
    display:flex; border-radius:10px; overflow:hidden;
    border:2px solid #005C3E; width:100%;
}
.periodo-btn {
    flex:1; padding:.6rem 1rem; text-align:center;
    cursor:pointer; font-weight:600; font-size:.875rem;
    transition:all 0.2s; border:none; background:transparent;
}
.periodo-btn.activo {
    background:#005C3E; color:#fff;
}
.periodo-btn:not(.activo) { color:#005C3E; }
html.dark-mode .periodo-btn:not(.activo) { color:#00E676; }
html.dark-mode .periodo-toggle { border-color:#00E676; }

/* Badge descuento */
.badge-ahorro {
    background:linear-gradient(135deg,#dc3545,#ff6b6b);
    color:#fff; font-size:.7rem; padding:3px 8px;
    border-radius:20px; font-weight:700;
}

/* Resumen de precio */
.precio-resumen {
    background:rgba(0,92,62,0.06);
    border:1px solid rgba(0,92,62,0.2);
    border-radius:12px; padding:1rem;
}
html.dark-mode .precio-resumen {
    background:rgba(0,230,118,0.05);
    border-color:rgba(0,230,118,0.15);
}
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-plus-circle me-2 text-success"></i>Nueva Suscripción
        </h4>
    </div>
    <a href="/Suscripciones" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Regresar
    </a>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-times-circle fa-lg"></i>
    <span><?= htmlspecialchars($error) ?></span>
</div>
<?php endif; ?>

<!-- Datos de planes para JS -->
<script>
const planesData = <?= json_encode(array_values($planes)) ?>;
</script>

<form id="suscripcionForm" action="" method="POST">
    <input type="hidden" name="Registrar" value="1">
    <input type="hidden" name="tipo_periodo" id="tipoPeriodo" value="mensual">

    <div class="row g-4">

        <!-- Datos principales -->
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <i class="fas fa-user me-2"></i>Datos de la Suscripción
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Cliente <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <select class="form-select" name="cliente_id" id="selectCliente">
                                    <option value="">Selecciona un cliente</option>
                                    <?php foreach ($clientes as $c): ?>
                                    <option value="<?= $c['id'] ?>"
                                        <?= $clientePreId === $c['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['nombre']) ?>
                                        <?= $c['empresa_nombre'] ? '— '.htmlspecialchars($c['empresa_nombre']) : '' ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Fecha de inicio <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" class="form-control" name="fecha_inicio"
                                       id="fechaInicio" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Fecha de vencimiento <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-times"></i></span>
                                <input type="date" class="form-control" name="fecha_vencimiento"
                                       id="fechaVencimiento" readonly>
                            </div>
                            <small class="text-muted">Se calcula automáticamente según el plan y período.</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Notas</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                                <input type="text" class="form-control" name="notas"
                                       placeholder="Observaciones opcionales..." maxlength="200">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Resumen de precio (aparece al seleccionar plan) -->
            <div class="card shadow-sm mt-3 d-none" id="cardResumen">
                <div class="card-header bg-primary">
                    <i class="fas fa-receipt me-2"></i>Resumen del Período
                </div>
                <div class="card-body">
                    <div class="precio-resumen">
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="text-muted small mb-1">Plan</div>
                                <div class="fw-bold" id="resumenPlan">—</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small mb-1">Período</div>
                                <div class="fw-bold" id="resumenPeriodo">—</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small mb-1">Total a cobrar</div>
                                <div class="fw-bold text-success fs-5" id="resumenTotal">—</div>
                            </div>
                        </div>
                        <div class="text-center mt-2 d-none" id="resumenAhorro">
                            <span class="text-success small">
                                <i class="fas fa-tag me-1"></i>
                                Ahorro vs mensual: <strong id="resumenAhorroMonto"></strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plan y período -->
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary">
                    <i class="fas fa-layer-group me-2"></i>Plan y Período
                </div>
                <div class="card-body d-flex flex-column gap-3">

                    <!-- Selector mensual/anual -->
                    <div>
                        <label class="form-label fw-semibold mb-2">Tipo de período</label>
                        <div class="periodo-toggle">
                            <button type="button" class="periodo-btn activo" id="btnMensual"
                                    onclick="seleccionarPeriodo('mensual')">
                                <i class="fas fa-calendar me-1"></i>Mensual
                            </button>
                            <button type="button" class="periodo-btn" id="btnAnual"
                                    onclick="seleccionarPeriodo('anual')">
                                <i class="fas fa-calendar-alt me-1"></i>Anual
                                <span class="badge-ahorro ms-1" id="badgeDescuento" style="display:none;">%OFF</span>
                            </button>
                        </div>
                        <small class="text-muted mt-1 d-block" id="textoDescuento"></small>
                    </div>

                    <!-- Planes -->
                    <div>
                        <label class="form-label fw-semibold mb-2">
                            Selecciona el plan <span class="text-danger">*</span>
                        </label>
                        <input type="hidden" name="plan_id" id="inputPlanId" value="">

                        <div class="d-flex flex-column gap-2" id="contenedorPlanes">
                            <?php foreach ($planes as $plan):
                                $pN    = strtolower($plan['nombre']);
                                $pCls  = str_contains($pN,'premium') ? 'plan-premium'
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
                                    <i class="<?= $pIcon ?>" style="font-size:1.3rem;"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold"><?= htmlspecialchars($plan['nombre']) ?></div>
                                        <div class="small text-muted precio-display">
                                            L.<?= number_format($plan['precio'],2) ?>/mes
                                        </div>
                                        <?php if ($tieneDescuento): ?>
                                        <div class="small text-success precio-anual-display" style="display:none;">
                                            L.<?= number_format($plan['precio_anual'],2) ?>/año
                                            <span class="badge-ahorro ms-1"><?= number_format($plan['descuento_anual'],0) ?>% OFF</span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <i class="fas fa-check-circle text-success d-none check-icon"></i>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="/Suscripciones" class="btn btn-outline-secondary">
            <i class="fas fa-times me-2"></i>Cancelar
        </a>
        <button type="submit" class="btn btn-primary" id="btnGuardar">
            <i class="fas fa-save me-2"></i>Crear Suscripción
        </button>
    </div>

</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let periodoActual    = 'mensual';
let planSeleccionado = null;

// ── Selector de período ──
function seleccionarPeriodo(periodo) {
    periodoActual = periodo;
    document.getElementById('tipoPeriodo').value = periodo;

    document.getElementById('btnMensual').classList.toggle('activo', periodo === 'mensual');
    document.getElementById('btnAnual').classList.toggle('activo', periodo === 'anual');

    // Muestra/oculta precios en las cards
    document.querySelectorAll('.precio-display').forEach(el => {
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

    // Muestra badge de descuento en botón anual si el plan tiene descuento
    const badge = document.getElementById('badgeDescuento');
    const texto = document.getElementById('textoDescuento');
    if (planSeleccionado.descuento > 0) {
        badge.textContent = planSeleccionado.descuento + '% OFF';
        badge.style.display = '';
        texto.textContent = `Ahorra L.${planSeleccionado.ahorro.toFixed(2)} pagando anualmente.`;
    } else {
        badge.style.display = 'none';
        texto.textContent = 'Este plan no tiene descuento anual.';
    }

    calcularVencimiento();
    actualizarResumen();
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

    const card = document.getElementById('cardResumen');
    card.classList.remove('d-none');

    const total = periodoActual === 'anual'
        ? planSeleccionado.precioAnual
        : planSeleccionado.precio;

    document.getElementById('resumenPlan').textContent    = planSeleccionado.nombre;
    document.getElementById('resumenPeriodo').textContent = periodoActual === 'anual' ? 'Anual (365 días)' : `Mensual (${planSeleccionado.duracion} días)`;
    document.getElementById('resumenTotal').textContent   = 'L.' + total.toFixed(2);

    const divAhorro = document.getElementById('resumenAhorro');
    if (periodoActual === 'anual' && planSeleccionado.ahorro > 0) {
        document.getElementById('resumenAhorroMonto').textContent = 'L.' + planSeleccionado.ahorro.toFixed(2);
        divAhorro.classList.remove('d-none');
    } else {
        divAhorro.classList.add('d-none');
    }
}

// ── Recalcula al cambiar fecha de inicio ──
document.getElementById('fechaInicio').addEventListener('change', calcularVencimiento);

// ── Validación ──
document.getElementById('suscripcionForm').addEventListener('submit', function(e) {
    const cliente = document.getElementById('selectCliente').value;
    const plan    = document.getElementById('inputPlanId').value;

    if (!cliente) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'Selecciona un cliente.', confirmButtonColor:'#005C3E' });
        return;
    }
    if (!plan) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'Selecciona un plan.', confirmButtonColor:'#005C3E' });
        return;
    }

    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
});
</script>