<!-- Views/Pagos/Cuenta.php -->

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
body.dark-mode .form-text  { color:#7ab89a; }

.tipo-cuenta-card {
    border-radius: 12px; padding: 1.25rem;
    cursor: pointer; transition: all 0.2s;
    border: 2px solid var(--border-color); text-align: center;
}
.tipo-cuenta-card:hover { border-color: #005C3E; }
.tipo-cuenta-card.selected {
    border-color: #005C3E !important;
    background: rgba(0,92,62,0.08) !important;
    box-shadow: 0 4px 15px rgba(0,92,62,0.2);
}
body.dark-mode .tipo-cuenta-card { border-color: #1e3329; }
body.dark-mode .tipo-cuenta-card.selected { border-color:#00E676 !important; background:rgba(0,230,118,0.1) !important; }
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-file-invoice me-2 text-success"></i>Nueva Cuenta por Cobrar
        </h4>
    </div>
    <a href="/Pagos" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Regresar
    </a>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-times-circle fa-lg"></i>
    <span><?= htmlspecialchars($error) ?></span>
</div>
<?php endif; ?>

<form id="cuentaForm" action="" method="POST">
    <input type="hidden" name="CrearCuenta" value="1">
    <input type="hidden" name="tipo" id="inputTipo" value="sistema">

    <div class="row g-4">

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Datos de la Cuenta
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

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Descripción <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <input type="text" class="form-control" name="descripcion"
                                       placeholder="Ej. Desarrollo sistema de gestión ZonaMarcol"
                                       maxlength="200">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Monto total (USD) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input type="number" class="form-control" name="monto_total"
                                       placeholder="0.00" min="0.01" step="0.01" id="montoTotal"
                                       oninput="actualizarResumen()">
                            </div>
                        </div>

                        <div class="col-md-6" id="divSuscripcion" style="display:none;">
                            <label class="form-label fw-semibold">Suscripción asociada</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-sync-alt"></i></span>
                                <select class="form-select" name="suscripcion_id" id="selectSuscripcion">
                                    <option value="">Sin suscripción</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Notas</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                                <textarea class="form-control" name="notas" rows="2"
                                          placeholder="Observaciones, condiciones de pago..."></textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Tipo de cuenta -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary">
                    <i class="fas fa-tag me-2"></i>Tipo de Cuenta
                </div>
                <div class="card-body d-flex flex-column gap-3">

                    <div class="tipo-cuenta-card selected" onclick="seleccionarTipo('sistema', this)">
                        <i class="fas fa-code fa-2x text-info mb-2 d-block"></i>
                        <div class="fw-bold">Costo del Sistema</div>
                        <small class="text-muted">Desarrollo, instalación o implementación</small>
                    </div>

                    <div class="tipo-cuenta-card" onclick="seleccionarTipo('suscripcion', this)">
                        <i class="fas fa-sync-alt fa-2x text-primary mb-2 d-block"></i>
                        <div class="fw-bold">Suscripción</div>
                        <small class="text-muted">Mensualidad del plan del cliente</small>
                    </div>

                </div>
            </div>

            <!-- Resumen -->
            <div class="card shadow-sm" style="border:2px solid var(--accent);">
                <div class="card-header bg-primary">
                    <i class="fas fa-receipt me-2"></i>Resumen
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tipo:</span>
                        <span id="resumenTipo" class="fw-semibold">Costo del Sistema</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total a cobrar:</span>
                        <span class="fw-bold text-success fs-5" id="resumenMonto">$0.00</span>
                    </div>
                    <hr>
                    <small class="text-muted d-block text-center">
                        <i class="fas fa-info-circle me-1"></i>
                        Los abonos se registran desde "Registrar Pago"
                    </small>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="/Pagos" class="btn btn-outline-secondary">
            <i class="fas fa-times me-2"></i>Cancelar
        </a>
        <button type="submit" class="btn btn-primary" id="btnGuardar">
            <i class="fas fa-save me-2"></i>Crear Cuenta
        </button>
    </div>

</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function seleccionarTipo(tipo, el) {
    document.querySelectorAll('.tipo-cuenta-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('inputTipo').value = tipo;

    const etiquetas = { sistema: 'Costo del Sistema', suscripcion: 'Suscripción' };
    document.getElementById('resumenTipo').textContent = etiquetas[tipo];

    // Muestra selector de suscripción solo si es tipo suscripcion
    const divSus = document.getElementById('divSuscripcion');
    divSus.style.display = tipo === 'suscripcion' ? 'block' : 'none';
}

function actualizarResumen() {
    const monto = parseFloat(document.getElementById('montoTotal').value) || 0;
    document.getElementById('resumenMonto').textContent = '$' + monto.toFixed(2);
}

// Carga suscripciones al seleccionar cliente
document.getElementById('selectCliente').addEventListener('change', function() {
    const clienteId = this.value;
    if (!clienteId) return;
    fetch(`/Pagos/suscripciones?cliente_id=${clienteId}`, { credentials:'same-origin' })
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById('selectSuscripcion');
            select.innerHTML = '<option value="">Sin suscripción</option>';
            data.forEach(s => {
                const vence = new Date(s.fecha_vencimiento).toLocaleDateString('es-HN');
                select.innerHTML += `<option value="${s.id}">${s.plan_nombre} — Vence: ${vence}</option>`;
            });
        });
});

document.getElementById('cuentaForm').addEventListener('submit', function(e) {
    const cliente     = document.getElementById('selectCliente').value;
    const monto       = parseFloat(document.getElementById('montoTotal').value);

    if (!cliente) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'Selecciona un cliente.', confirmButtonColor:'#005C3E' });
        return;
    }
    if (!monto || monto <= 0) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'El monto debe ser mayor a 0.', confirmButtonColor:'#005C3E' });
        return;
    }

    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
});
</script>