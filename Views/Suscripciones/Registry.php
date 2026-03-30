<!-- Views/Suscripciones/Registry.php -->

<style>
.input-group-text { background:#005C3E; color:#fff; border-color:#005C3E; }
.plan-card {
    border-radius: 12px; padding: 1rem; cursor: pointer;
    transition: all 0.2s; border: 2px solid transparent; text-align: center;
}
.plan-card:hover { transform: translateY(-2px); }
.plan-card.plan-basico   { background: rgba(108,117,125,0.1); border-color: #6c757d; }
.plan-card.plan-estandar { background: rgba(13,110,253,0.1);  border-color: #0d6efd; }
.plan-card.plan-premium  { background: rgba(255,193,7,0.12);  border-color: #ffc107; }
.plan-card.selected.plan-basico   { background: rgba(108,117,125,0.25); box-shadow: 0 4px 15px rgba(108,117,125,0.3); }
.plan-card.selected.plan-estandar { background: rgba(13,110,253,0.2);   box-shadow: 0 4px 15px rgba(13,110,253,0.3); }
.plan-card.selected.plan-premium  { background: rgba(255,193,7,0.25);   box-shadow: 0 4px 15px rgba(255,193,7,0.3); }

body.dark-mode .input-group-text { background:#1a3329; border-color:#1e3329; color:#e0e0e0; }
body.dark-mode .form-control, body.dark-mode .form-select {
    background:#111f18; border-color:#1e3329; color:#e0e0e0;
}
body.dark-mode .form-control:focus, body.dark-mode .form-select:focus {
    background:#0f1a15; border-color:#00E676; box-shadow:0 0 0 3px rgba(0,230,118,0.1);
}
body.dark-mode .form-label { color:#c8e6d5; }
body.dark-mode .plan-card.plan-basico   { background: rgba(108,117,125,0.15); }
body.dark-mode .plan-card.plan-estandar { background: rgba(13,110,253,0.15); }
body.dark-mode .plan-card.plan-premium  { background: rgba(255,193,7,0.15); }
body.dark-mode .plan-card.selected.plan-basico   { background: rgba(108,117,125,0.3); }
body.dark-mode .plan-card.selected.plan-estandar { background: rgba(13,110,253,0.3); }
body.dark-mode .plan-card.selected.plan-premium  { background: rgba(255,193,7,0.25); }
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

<form id="suscripcionForm" action="" method="POST">
    <input type="hidden" name="Registrar" value="1">

    <div class="row g-4">

        <!-- Cliente y fechas -->
        <div class="col-lg-8">
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
                                       id="fechaVencimiento">
                            </div>
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
        </div>

        <!-- Selección de plan -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary">
                    <i class="fas fa-layer-group me-2"></i>Selecciona el Plan
                </div>
                <div class="card-body">
                    <input type="hidden" name="plan_id" id="inputPlanId" value="">

                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($planes as $plan):
                            $pN    = strtolower($plan['nombre']);
                            $pCls  = str_contains($pN,'premium') ? 'plan-premium'
                                : (str_contains($pN,'estándar') || str_contains($pN,'estandar') ? 'plan-estandar' : 'plan-basico');
                            $pIcon = str_contains($pN,'premium') ? 'fas fa-crown'
                                : (str_contains($pN,'estándar') || str_contains($pN,'estandar') ? 'fas fa-star' : 'fas fa-leaf');
                        ?>
                        <div class="plan-card <?= $pCls ?>"
                             data-id="<?= $plan['id'] ?>"
                             data-duracion="<?= $plan['duracion_dias'] ?>"
                             onclick="seleccionarPlan(this)">
                            <div class="d-flex align-items-center gap-3">
                                <i class="<?= $pIcon ?>" style="font-size:1.4rem;"></i>
                                <div class="text-start flex-grow-1">
                                    <div class="fw-bold"><?= htmlspecialchars($plan['nombre']) ?></div>
                                    <div class="small text-muted">
                                        $<?= number_format($plan['precio'],2) ?>/mes
                                        · <?= $plan['duracion_dias'] ?> días
                                    </div>
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
function seleccionarPlan(el) {
    document.querySelectorAll('.plan-card').forEach(c => {
        c.classList.remove('selected');
        c.querySelector('.check-icon')?.classList.add('d-none');
    });
    el.classList.add('selected');
    el.querySelector('.check-icon')?.classList.remove('d-none');
    document.getElementById('inputPlanId').value = el.dataset.id;
    calcularVencimiento(el.dataset.duracion);
}

function calcularVencimiento(duracion) {
    const fi = document.getElementById('fechaInicio');
    const fv = document.getElementById('fechaVencimiento');
    if (!fi || !fv) return;
    const d = new Date(fi.value);
    d.setDate(d.getDate() + parseInt(duracion || 30));
    fv.value = d.toISOString().split('T')[0];
}

document.getElementById('fechaInicio').addEventListener('change', function() {
    const sel = document.querySelector('.plan-card.selected');
    if (sel) calcularVencimiento(sel.dataset.duracion);
});

document.getElementById('suscripcionForm').addEventListener('submit', function(e) {
    const cliente = document.getElementById('selectCliente').value;
    const plan    = document.getElementById('inputPlanId').value;

    if (!cliente) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'Selecciona un cliente.', confirmButtonColor:'#005C3E'
        });
        return;
    }
    if (!plan) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'Selecciona un plan.', confirmButtonColor:'#005C3E'
        });
        return;
    }

    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
});
</script>