<!-- Views/Pagos/Registry.php -->

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

.resumen-pago {
    border-radius: 12px;
    border: 2px solid var(--accent);
    background: rgba(0,230,118,0.05);
}
body.dark-mode .resumen-pago { background: rgba(0,230,118,0.08); }

.tipo-card {
    border-radius: 10px; padding: 0.75rem;
    cursor: pointer; transition: all 0.2s;
    border: 2px solid var(--border-color); text-align: center;
}
.tipo-card:hover { border-color: #005C3E; }
.tipo-card.selected {
    border-color: #005C3E !important;
    background: rgba(0,92,62,0.08) !important;
}
body.dark-mode .tipo-card { border-color: #1e3329; }
body.dark-mode .tipo-card.selected { border-color:#00E676 !important; background:rgba(0,230,118,0.1) !important; }

/* Zona de upload */
.upload-zona {
    border: 2px dashed #005C3E; border-radius: 12px;
    padding: 1.5rem; text-align: center;
    cursor: pointer; transition: all 0.2s;
    background: rgba(0,92,62,0.03);
}
.upload-zona:hover { background: rgba(0,92,62,0.08); border-color: #00E676; }
.upload-zona.drag-over { background: rgba(0,92,62,0.12); border-color: #00E676; }
body.dark-mode .upload-zona { background: rgba(0,92,62,0.08); border-color: #1e3329; }
body.dark-mode .upload-zona:hover { background: rgba(0,230,118,0.1); border-color: #00E676; }

#previewImagen {
    max-height: 150px; object-fit: contain;
    border-radius: 8px; border: 1px solid var(--border-color);
}

/* Cuenta por cobrar card */
.cuenta-card {
    border-radius: 10px; padding: 0.75rem 1rem;
    border: 2px solid transparent; cursor: pointer;
    transition: all 0.2s;
    background: rgba(0,92,62,0.04);
}
.cuenta-card:hover { border-color: #005C3E; }
.cuenta-card.selected { border-color: #00E676 !important; background: rgba(0,230,118,0.1) !important; }
body.dark-mode .cuenta-card { background: rgba(0,92,62,0.1); }
body.dark-mode .cuenta-card.selected { border-color:#00E676 !important; background:rgba(0,230,118,0.15) !important; }

.barra-mini { height:6px; border-radius:3px; background:#dee2e6; overflow:hidden; }
.barra-mini .prog { height:100%; border-radius:3px; background:linear-gradient(90deg,#005C3E,#00E676); }
body.dark-mode .barra-mini { background:#1e3329; }
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-plus-circle me-2 text-success"></i>Registrar Pago
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

<form id="pagoForm" action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="Registrar" value="1">

    <div class="row g-4">

        <!-- ── Columna principal ── -->
        <div class="col-lg-8">

            <!-- Cliente -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary">
                    <i class="fas fa-user me-2"></i>Cliente
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

                        <!-- Cuentas por cobrar del cliente -->
                        <div class="col-12" id="divCuentas" style="display:none;">
                            <label class="form-label fw-semibold">
                                Cuenta por cobrar
                                <small class="text-muted fw-normal">(opcional)</small>
                            </label>
                            <input type="hidden" name="cuenta_id" id="inputCuentaId" value="">
                            <div id="listaCuentas" class="d-flex flex-column gap-2">
                                <!-- Se llena dinámicamente -->
                            </div>
                            <small class="form-text">
                                Selecciona si este pago corresponde a una deuda específica.
                            </small>
                        </div>

                        <!-- Panel informativo de deuda seleccionada -->
                        <div class="col-12" id="infoCuenta" style="display:none;">
                            <div class="p-3 rounded" style="background:rgba(255,193,7,0.08);border:2px solid #ffc107;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold small" id="infoDesc"></span>
                                    <small class="text-muted" id="infoPct"></small>
                                </div>
                                <div class="barra-mini mb-2"><div class="prog" id="infoBarra" style="width:0%"></div></div>
                                <div class="row g-2 text-center">
                                    <div class="col-4"><small class="text-muted d-block">Total</small><span class="fw-bold small" id="infoTotal"></span></div>
                                    <div class="col-4"><small class="text-muted d-block">Pagado</small><span class="fw-bold small text-success" id="infoPagado"></span></div>
                                    <div class="col-4"><small class="text-muted d-block">Pendiente</small><span class="fw-bold small text-danger" id="infoSaldo"></span></div>
                                </div>
                                <div class="mt-2 pt-2 border-top">
                                    <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Ingresa el monto que el cliente paga hoy (mu00e1x. <strong>$<span id="infoMaximo"></span></strong>)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Suscripción -->
                        <div class="col-12" id="divSuscripcion" style="display:none;">
                            <label class="form-label fw-semibold">
                                Suscripción asociada
                                <small class="text-muted fw-normal">(opcional)</small>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-sync-alt"></i></span>
                                <select class="form-select" name="suscripcion_id" id="selectSuscripcion">
                                    <option value="">Sin suscripción asociada</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Detalles del pago -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Detalles del Pago
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Concepto <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <input type="text" class="form-control" name="concepto" id="concepto"
                                       placeholder="Ej. Abono sistema — Abril 2026"
                                       maxlength="200" oninput="actualizarResumen()">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Monto (USD) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input type="number" class="form-control" name="monto" id="monto"
                                       placeholder="0.00" min="0.01" step="0.01"
                                       oninput="actualizarResumen()">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha del pago</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="datetime-local" class="form-control" name="fecha_pago"
                                       value="<?= date('Y-m-d\TH:i') ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Referencia / Número</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                <input type="text" class="form-control" name="referencia"
                                       placeholder="Número de transferencia, recibo..."
                                       maxlength="150">
                            </div>
                            <small class="form-text">Opcional</small>
                        </div>

                        <!-- Comprobante imagen -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Comprobante</label>
                            <div class="upload-zona" id="uploadZona"
                                 onclick="document.getElementById('inputImagen').click()"
                                 ondragover="dragOver(event)"
                                 ondragleave="dragLeave(event)"
                                 ondrop="drop(event)">
                                <div id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-success mb-2 d-block"></i>
                                    <p class="mb-0 small fw-semibold">Haz clic o arrastra la imagen</p>
                                    <small class="text-muted">JPG, PNG — máx. 5MB — Opcional</small>
                                </div>
                                <div id="uploadPreview" style="display:none;">
                                    <img id="previewImagen" src="" alt="Comprobante" class="mb-2">
                                    <br>
                                    <small class="text-muted" id="nombreArchivo"></small>
                                    <br>
                                    <button type="button" class="btn btn-outline-danger btn-sm mt-1"
                                            onclick="quitarImagen(event)">
                                        <i class="fas fa-times me-1"></i>Quitar
                                    </button>
                                </div>
                            </div>
                            <input type="file" id="inputImagen" name="comprobante"
                                   accept="image/jpeg,image/png" style="display:none;"
                                   onchange="previsualizarImagen(this)">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Notas adicionales</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                                <textarea class="form-control" name="notas" rows="2"
                                          placeholder="Observaciones opcionales..."></textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- ── Columna lateral ── -->
        <div class="col-lg-4">

            <!-- Método de pago -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary">
                    <i class="fas fa-wallet me-2"></i>Método de Pago
                </div>
                <div class="card-body">
                    <input type="hidden" name="metodo_pago_id" id="inputMetodoPago" value="">
                    <div class="d-flex flex-column gap-2">
                        <?php
                        $iconos = [
                            'Efectivo'               => 'fas fa-money-bill-wave',
                            'Transferencia bancaria' => 'fas fa-university',
                            'Cheque'                 => 'fas fa-money-check',
                            'PayPal'                 => 'fab fa-paypal',
                        ];
                        foreach ($metodosPago as $mp):
                            $icon = $iconos[$mp['nombre']] ?? 'fas fa-credit-card';
                        ?>
                        <div class="tipo-card" data-id="<?= $mp['id'] ?>"
                             onclick="seleccionarMetodo(this)">
                            <i class="<?= $icon ?> fa-lg me-2 text-success"></i>
                            <span class="fw-semibold"><?= htmlspecialchars($mp['nombre']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Resumen -->
            <div class="card shadow-sm resumen-pago">
                <div class="card-header bg-primary">
                    <i class="fas fa-receipt me-2"></i>Resumen
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Concepto:</span>
                        <span id="resumenConcepto" class="text-end small fw-semibold">—</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Método:</span>
                        <span id="resumenMetodo">—</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Comprobante:</span>
                        <span id="resumenComprobante" class="text-muted small">Sin imagen</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total:</span>
                        <span class="fw-bold text-success fs-5" id="resumenMonto">$0.00</span>
                    </div>
                    <hr>
                    <small class="text-muted d-block text-center">
                        <i class="fas fa-file-invoice me-1"></i>
                        Se generará una factura automáticamente
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
            <i class="fas fa-save me-2"></i>Registrar Pago
        </button>
    </div>

</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ── Método de pago ──
function seleccionarMetodo(el) {
    document.querySelectorAll('.tipo-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('inputMetodoPago').value = el.dataset.id;
    document.getElementById('resumenMetodo').textContent = el.querySelector('span').textContent;
}

// ── Resumen en tiempo real ──
function actualizarResumen() {
    const concepto = document.getElementById('concepto').value || '—';
    const monto    = parseFloat(document.getElementById('monto').value) || 0;
    document.getElementById('resumenConcepto').textContent = concepto;
    document.getElementById('resumenMonto').textContent    = '$' + monto.toFixed(2);
}

// ── Seleccionar cuenta por cobrar ──
function seleccionarCuenta(el, id) {
    document.querySelectorAll('.cuenta-card').forEach(c => c.classList.remove('selected'));
    const infoCuenta = document.getElementById('infoCuenta');

    if (document.getElementById('inputCuentaId').value === String(id)) {
        // Deselecciona
        document.getElementById('inputCuentaId').value = '';
        infoCuenta.style.display = 'none';
        document.getElementById('concepto').value = '';
    } else {
        el.classList.add('selected');
        document.getElementById('inputCuentaId').value = id;

        const desc  = el.dataset.descripcion;
        const saldo = parseFloat(el.dataset.saldo);
        const total = parseFloat(el.dataset.total);
        const pagado = total - saldo;
        const pct   = Math.round((pagado / total) * 100);

        // Solo sugiere el concepto, NO el monto
        document.getElementById('concepto').value = 'Abono — ' + desc;

        // Muestra panel informativo de la deuda
        document.getElementById('infoDesc').textContent    = desc;
        document.getElementById('infoTotal').textContent   = '$' + total.toFixed(2);
        document.getElementById('infoPagado').textContent  = '$' + pagado.toFixed(2);
        document.getElementById('infoSaldo').textContent   = '$' + saldo.toFixed(2);
        document.getElementById('infoBarra').style.width   = pct + '%';
        document.getElementById('infoPct').textContent     = pct + '%';
        document.getElementById('infoMaximo').textContent  = saldo.toFixed(2);
        infoCuenta.style.display = 'block';

        // Foco en el campo monto para que el empleado ingrese el abono real
        document.getElementById('monto').focus();
        actualizarResumen();
    }
}

// ── Carga datos del cliente ──
document.getElementById('selectCliente').addEventListener('change', function() {
    const clienteId = this.value;
    const divC = document.getElementById('divCuentas');
    const divS = document.getElementById('divSuscripcion');

    if (!clienteId) {
        divC.style.display = 'none';
        divS.style.display = 'none';
        return;
    }

    // Carga cuentas por cobrar
    fetch(`/Pagos/cuentas?cliente_id=${clienteId}`, { credentials:'same-origin' })
        .then(r => r.json())
        .then(data => {
            const lista = document.getElementById('listaCuentas');
            lista.innerHTML = '';
            if (data.length > 0) {
                data.forEach(c => {
                    const pct = Math.min(100, Math.round((c.monto_pagado / c.monto_total) * 100));
                    const tipoIcon = c.tipo === 'sistema' ? 'fas fa-code' : 'fas fa-sync-alt';
                    const div = document.createElement('div');
                    div.className = 'cuenta-card';
                    div.dataset.descripcion = c.descripcion;
                    div.dataset.saldo = c.saldo_pendiente;
                    div.dataset.total = c.monto_total;
                    div.onclick = function() { seleccionarCuenta(this, c.id); };
                    div.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold small">
                                <i class="${tipoIcon} me-1 text-success"></i>${c.descripcion}
                            </span>
                            <span class="badge bg-${c.estado === 'parcial' ? 'warning text-dark' : 'danger'}">
                                $${parseFloat(c.saldo_pendiente).toFixed(2)} pendiente
                            </span>
                        </div>
                        <div class="barra-mini">
                            <div class="prog" style="width:${pct}%"></div>
                        </div>
                        <small class="text-muted">$${parseFloat(c.monto_pagado).toFixed(2)} de $${parseFloat(c.monto_total).toFixed(2)} pagado</small>`;
                    lista.appendChild(div);
                });
                divC.style.display = 'block';

                // Si viene pre-seleccionada una cuenta
                <?php if ($cuentaPreId): ?>
                const preCard = lista.querySelector('[data-descripcion]');
                // Busca la cuenta específica
                fetch(`/Pagos/cuentas?cliente_id=${clienteId}`, { credentials:'same-origin' })
                    .then(r => r.json())
                    .then(cuentas => {
                        cuentas.forEach((c, i) => {
                            if (c.id === <?= $cuentaPreId ?>) {
                                const cards = lista.querySelectorAll('.cuenta-card');
                                if (cards[i]) seleccionarCuenta(cards[i], c.id);
                            }
                        });
                    });
                <?php endif; ?>
            } else {
                divC.style.display = 'none';
            }
        });

    // Carga suscripciones
    fetch(`/Pagos/suscripciones?cliente_id=${clienteId}`, { credentials:'same-origin' })
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById('selectSuscripcion');
            select.innerHTML = '<option value="">Sin suscripción asociada</option>';
            data.forEach(s => {
                const vence = new Date(s.fecha_vencimiento).toLocaleDateString('es-HN');
                select.innerHTML += `<option value="${s.id}">
                    ${s.plan_nombre} — $${parseFloat(s.precio).toFixed(2)}/mes — Vence: ${vence}
                </option>`;
            });
            if (data.length > 0) divS.style.display = 'block';
        });
});

// Auto-carga si hay cliente pre-seleccionado
<?php if ($clientePreId): ?>
document.getElementById('selectCliente').value = <?= $clientePreId ?>;
document.getElementById('selectCliente').dispatchEvent(new Event('change'));
<?php endif; ?>

// ── Upload imagen ──
function previsualizarImagen(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    if (file.size > 5 * 1024 * 1024) {
        Swal.fire({ icon:'error', title:'Archivo muy grande',
            text:'El comprobante no puede superar 5MB.', confirmButtonColor:'#005C3E' });
        input.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('previewImagen').src = e.target.result;
        document.getElementById('nombreArchivo').textContent = file.name;
        document.getElementById('uploadPlaceholder').style.display = 'none';
        document.getElementById('uploadPreview').style.display    = 'block';
        document.getElementById('resumenComprobante').textContent = '✅ ' + file.name;
        document.getElementById('resumenComprobante').className   = 'text-success small';
    };
    reader.readAsDataURL(file);
}

function quitarImagen(e) {
    e.stopPropagation();
    document.getElementById('inputImagen').value = '';
    document.getElementById('uploadPlaceholder').style.display = 'block';
    document.getElementById('uploadPreview').style.display    = 'none';
    document.getElementById('resumenComprobante').textContent = 'Sin imagen';
    document.getElementById('resumenComprobante').className   = 'text-muted small';
}

function dragOver(e) {
    e.preventDefault();
    document.getElementById('uploadZona').classList.add('drag-over');
}
function dragLeave() {
    document.getElementById('uploadZona').classList.remove('drag-over');
}
function drop(e) {
    e.preventDefault();
    document.getElementById('uploadZona').classList.remove('drag-over');
    const input = document.getElementById('inputImagen');
    input.files = e.dataTransfer.files;
    previsualizarImagen(input);
}

// ── Validación submit ──
document.getElementById('pagoForm').addEventListener('submit', function(e) {
    const cliente  = document.getElementById('selectCliente').value;
    const concepto = document.getElementById('concepto').value.trim();
    const monto    = parseFloat(document.getElementById('monto').value);
    const metodo   = document.getElementById('inputMetodoPago').value;

    if (!cliente) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'Selecciona un cliente.', confirmButtonColor:'#005C3E' });
        return;
    }
    if (!concepto) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'El concepto es obligatorio.', confirmButtonColor:'#005C3E' });
        return;
    }
    if (!monto || monto <= 0) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'El monto debe ser mayor a 0.', confirmButtonColor:'#005C3E' });
        return;
    }
    if (!metodo) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'Selecciona un método de pago.', confirmButtonColor:'#005C3E' });
        return;
    }

    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registrando...';
});
</script>