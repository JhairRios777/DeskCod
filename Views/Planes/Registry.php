<!-- Views/Planes/Registry.php -->

<style>
.input-group-text { background:#005C3E; color:#fff; border-color:#005C3E; }
html.dark-mode .input-group-text { background:#1a3329; border-color:#1e3329; color:#e0e0e0; }
html.dark-mode .form-control, html.dark-mode .form-select {
    background:#111f18; border-color:#1e3329; color:#e0e0e0;
}
html.dark-mode .form-control:focus {
    background:#0f1a15; border-color:#00E676; box-shadow:0 0 0 3px rgba(0,230,118,0.1);
}
html.dark-mode .form-label { color:#c8e6d5; }
html.dark-mode .form-text  { color:#7ab89a; }

.plan-preview { border-radius:16px; overflow:hidden; border:2px solid var(--accent); }
.plan-preview .preview-header {
    background: linear-gradient(135deg, #005C3E, #00895a);
    color: white; padding: 1.5rem; text-align: center;
}

/* Badge de descuento */
.badge-descuento {
    background: linear-gradient(135deg, #dc3545, #ff6b6b);
    color: #fff; font-size: .75rem; padding: 4px 10px;
    border-radius: 20px; font-weight: 700;
}

/* Anual preview */
.anual-preview {
    background: rgba(0,230,118,0.08);
    border: 1px dashed #00E676;
    border-radius: 10px; padding: 1rem;
}
html.dark-mode .anual-preview { background: rgba(0,230,118,0.05); }
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <?php if ($plan): ?>
                <i class="fas fa-edit me-2 text-warning"></i>Editar Plan
            <?php else: ?>
                <i class="fas fa-plus-circle me-2 text-success"></i>Nuevo Plan
            <?php endif; ?>
        </h4>
    </div>
    <a href="/Planes" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Regresar
    </a>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-times-circle fa-lg"></i>
    <span><?= htmlspecialchars($error) ?></span>
</div>
<?php endif; ?>

<form id="planForm" action="" method="POST">
    <input type="hidden" name="Registrar" value="1">

    <div class="row g-4">

        <!-- Datos del plan -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <i class="fas fa-layer-group me-2"></i>Datos del Plan
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Nombre del plan <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <input type="text" class="form-control" name="nombre" id="nombre"
                                       value="<?= htmlspecialchars($plan['nombre'] ?? '') ?>"
                                       placeholder="Ej. Básico, Estándar, Premium"
                                       maxlength="100" oninput="actualizarPreview()">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Precio mensual (L.) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input type="number" class="form-control" name="precio" id="precio"
                                       value="<?= $plan['precio'] ?? '' ?>"
                                       placeholder="0.00" min="0" step="0.01"
                                       oninput="actualizarPreview()">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Duración mensual (días) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="number" class="form-control" name="duracion_dias"
                                       value="<?= $plan['duracion_dias'] ?? 30 ?>"
                                       placeholder="30" min="1">
                            </div>
                            <small class="form-text">Generalmente 30 días para planes mensuales.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Máximo de tickets por mes</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-ticket-alt"></i></span>
                                <input type="number" class="form-control" name="max_tickets"
                                       value="<?= $plan['max_tickets'] ?? '' ?>"
                                       placeholder="Dejar vacío = ilimitado" min="1">
                            </div>
                            <small class="form-text">Dejar vacío para tickets ilimitados.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Descuento anual (%)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                <input type="number" class="form-control" name="descuento_anual"
                                       id="descuento_anual"
                                       value="<?= $plan['descuento_anual'] ?? 0 ?>"
                                       placeholder="0" min="0" max="100" step="0.01"
                                       oninput="actualizarPreview()">
                            </div>
                            <small class="form-text">
                                0 = sin descuento anual. Ej: 15 = 15% de descuento al pagar anual.
                            </small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                                <textarea class="form-control" name="descripcion" id="descripcion"
                                          rows="3" placeholder="Describe qué incluye este plan..."
                                          oninput="actualizarPreview()"><?= htmlspecialchars($plan['descripcion'] ?? '') ?></textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Preview del plan -->
        <div class="col-lg-4">
            <div class="card shadow-sm plan-preview">
                <div class="preview-header">
                    <i class="fas fa-layer-group fa-2x mb-2 d-block opacity-75"></i>
                    <h5 class="fw-bold mb-0" id="previewNombre">
                        <?= htmlspecialchars($plan['nombre'] ?? 'Nombre del plan') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Precio mensual -->
                    <div class="text-center mb-3">
                        <div class="text-muted small mb-1">Precio mensual</div>
                        <span class="fs-2 fw-bold text-success" id="previewPrecio">
                            L.<?= number_format($plan['precio'] ?? 0, 2) ?>
                        </span>
                        <span class="text-muted">/mes</span>
                    </div>

                    <!-- Precio anual con descuento -->
                    <div class="anual-preview mb-3" id="anualPreview">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <small class="fw-semibold text-success">
                                <i class="fas fa-calendar-alt me-1"></i>Precio anual
                            </small>
                            <span class="badge-descuento" id="previewBadge">
                                <?= ($plan['descuento_anual'] ?? 0) > 0 ? $plan['descuento_anual'].'% OFF' : 'Sin descuento' ?>
                            </span>
                        </div>
                        <div class="text-center">
                            <?php
                            $precioAnual  = ($plan['precio'] ?? 0) * 12 * (1 - ($plan['descuento_anual'] ?? 0) / 100);
                            $ahorroAnual  = ($plan['precio'] ?? 0) * 12 * (($plan['descuento_anual'] ?? 0) / 100);
                            ?>
                            <span class="fs-4 fw-bold text-success" id="previewPrecioAnual">
                                L.<?= number_format($precioAnual, 2) ?>
                            </span>
                            <span class="text-muted small">/año</span>
                            <div class="text-success small mt-1" id="previewAhorro"
                                 style="<?= ($plan['descuento_anual'] ?? 0) > 0 ? '' : 'display:none' ?>">
                                <i class="fas fa-tag me-1"></i>
                                Ahorro: L.<span id="previewAhorroMonto"><?= number_format($ahorroAnual, 2) ?></span>
                            </div>
                        </div>
                    </div>

                    <p class="text-muted small text-center mb-3" id="previewDescripcion">
                        <?= htmlspecialchars($plan['descripcion'] ?? 'Descripción del plan') ?>
                    </p>
                    <hr>
                    <div class="d-flex justify-content-center gap-4 small">
                        <div class="text-center">
                            <i class="fas fa-calendar text-success d-block mb-1"></i>
                            <span id="previewDias"><?= $plan['duracion_dias'] ?? 30 ?> días</span>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-ticket-alt text-success d-block mb-1"></i>
                            <span id="previewTickets">
                                <?= ($plan['max_tickets'] ?? null) ? $plan['max_tickets'].' tickets' : 'Ilimitado' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="/Planes" class="btn btn-outline-secondary">
            <i class="fas fa-times me-2"></i>Cancelar
        </a>
        <button type="submit" class="btn btn-primary" id="btnGuardar">
            <i class="fas fa-save me-2"></i>
            <?= $plan ? 'Guardar Cambios' : 'Crear Plan' ?>
        </button>
    </div>

</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function actualizarPreview() {
    const nombre      = document.getElementById('nombre').value || 'Nombre del plan';
    const precio      = parseFloat(document.getElementById('precio').value) || 0;
    const descuento   = parseFloat(document.getElementById('descuento_anual').value) || 0;
    const descripcion = document.getElementById('descripcion').value || 'Descripción del plan';

    const precioAnual = precio * 12 * (1 - descuento / 100);
    const ahorro      = precio * 12 * (descuento / 100);

    document.getElementById('previewNombre').textContent      = nombre;
    document.getElementById('previewPrecio').textContent      = 'L.' + precio.toFixed(2);
    document.getElementById('previewDescripcion').textContent = descripcion;
    document.getElementById('previewPrecioAnual').textContent = 'L.' + precioAnual.toFixed(2);
    document.getElementById('previewAhorroMonto').textContent = ahorro.toFixed(2);
    document.getElementById('previewBadge').textContent       = descuento > 0 ? descuento + '% OFF' : 'Sin descuento';

    // Muestra/oculta la sección de ahorro
    document.getElementById('previewAhorro').style.display = descuento > 0 ? '' : 'none';
}

document.getElementById('planForm').addEventListener('submit', function(e) {
    const nombre    = document.getElementById('nombre').value.trim();
    const precio    = parseFloat(document.getElementById('precio').value);
    const descuento = parseFloat(document.getElementById('descuento_anual').value) || 0;

    if (!nombre) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'El nombre del plan es obligatorio.', confirmButtonColor:'#005C3E' });
        return;
    }
    if (!precio || precio <= 0) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'El precio debe ser mayor a 0.', confirmButtonColor:'#005C3E' });
        return;
    }
    if (descuento < 0 || descuento > 100) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Descuento inválido',
            text:'El descuento debe estar entre 0 y 100.', confirmButtonColor:'#005C3E' });
        return;
    }

    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
});
</script>