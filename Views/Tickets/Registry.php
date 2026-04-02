<!-- Views/Tickets/Registry.php -->

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

/* Prioridad cards */
.prioridad-card {
    border-radius:10px; padding:0.75rem 1rem;
    cursor:pointer; transition:all 0.2s;
    border:2px solid transparent; text-align:center;
}
.prioridad-card:hover { transform:translateY(-2px); }
.prioridad-card.p-baja     { background:rgba(108,117,125,0.1); border-color:#6c757d; }
.prioridad-card.p-media    { background:rgba(255,193,7,0.1);   border-color:#ffc107; }
.prioridad-card.p-alta     { background:rgba(253,126,20,0.1);  border-color:#fd7e14; }
.prioridad-card.p-critica  { background:rgba(220,53,69,0.1);   border-color:#dc3545; }
.prioridad-card.selected.p-baja    { background:rgba(108,117,125,0.25); box-shadow:0 4px 15px rgba(108,117,125,0.3); }
.prioridad-card.selected.p-media   { background:rgba(255,193,7,0.25);   box-shadow:0 4px 15px rgba(255,193,7,0.3); }
.prioridad-card.selected.p-alta    { background:rgba(253,126,20,0.25);  box-shadow:0 4px 15px rgba(253,126,20,0.3); }
.prioridad-card.selected.p-critica { background:rgba(220,53,69,0.25);   box-shadow:0 4px 15px rgba(220,53,69,0.3); }
body.dark-mode .prioridad-card.p-baja    { background:rgba(108,117,125,0.15); }
body.dark-mode .prioridad-card.p-media   { background:rgba(255,193,7,0.1); }
body.dark-mode .prioridad-card.p-alta    { background:rgba(253,126,20,0.1); }
body.dark-mode .prioridad-card.p-critica { background:rgba(220,53,69,0.1); }
</style>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-plus-circle me-2 text-success"></i>Nuevo Ticket
        </h4>
    </div>
    <a href="/Tickets" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Regresar
    </a>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-times-circle fa-lg"></i>
    <span><?= htmlspecialchars($error) ?></span>
</div>
<?php endif; ?>

<form id="ticketForm" action="" method="POST">
    <input type="hidden" name="Registrar" value="1">
    <input type="hidden" name="prioridad" id="inputPrioridad" value="media">

    <div class="row g-4">

        <!-- Datos principales -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <i class="fas fa-ticket-alt me-2"></i>Datos del Ticket
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
                                    <option value="<?= $c['id'] ?>">
                                        <?= htmlspecialchars($c['nombre']) ?>
                                        <?= $c['empresa_nombre'] ? '— '.htmlspecialchars($c['empresa_nombre']) : '' ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12" id="divSuscripcion" style="display:none;">
                            <label class="form-label fw-semibold">
                                Suscripción <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-sync-alt"></i></span>
                                <select class="form-select" name="suscripcion_id" id="selectSuscripcion">
                                    <option value="">Cargando...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Título <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                <input type="text" class="form-control" name="titulo" id="titulo"
                                       placeholder="Describe brevemente el problema" maxlength="200">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <select class="form-select" name="tipo">
                                    <option value="error">Error / Bug</option>
                                    <option value="modificacion">Modificación</option>
                                    <option value="nueva_funcion">Nueva función</option>
                                    <option value="consulta" selected>Consulta</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoría</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-folder"></i></span>
                                <input type="text" class="form-control" name="categoria"
                                       placeholder="Ej. Módulo Clientes, Login..." maxlength="80">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Asignar a</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                <select class="form-select" name="empleado_id">
                                    <option value="<?= $_SESSION['system']['UserID'] ?>">
                                        Yo (<?= htmlspecialchars($_SESSION['system']['UserName']) ?>)
                                    </option>
                                    <?php foreach ($empleados as $emp): ?>
                                    <?php if ($emp['id'] !== (int)$_SESSION['system']['UserID']): ?>
                                    <option value="<?= $emp['id'] ?>">
                                        <?= htmlspecialchars($emp['nombre']) ?>
                                    </option>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha límite</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="datetime-local" class="form-control" name="fecha_limite">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Descripción <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" name="descripcion" id="descripcion"
                                      rows="6" placeholder="Describe el problema en detalle..."></textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Prioridad -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <i class="fas fa-exclamation-triangle me-2"></i>Prioridad
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Selecciona la urgencia del ticket.</p>
                    <div class="d-flex flex-column gap-2">

                        <div class="prioridad-card p-baja" onclick="seleccionarPrioridad('baja', this)">
                            <i class="fas fa-arrow-down me-2"></i>
                            <span class="fw-bold">Baja</span>
                            <br><small class="text-muted">Sin urgencia, puede esperar</small>
                        </div>

                        <div class="prioridad-card p-media selected" onclick="seleccionarPrioridad('media', this)">
                            <i class="fas fa-minus me-2"></i>
                            <span class="fw-bold">Media</span>
                            <br><small class="text-muted">Prioridad normal</small>
                        </div>

                        <div class="prioridad-card p-alta" onclick="seleccionarPrioridad('alta', this)">
                            <i class="fas fa-arrow-up me-2"></i>
                            <span class="fw-bold">Alta</span>
                            <br><small class="text-muted">Requiere atención pronta</small>
                        </div>

                        <div class="prioridad-card p-critica" onclick="seleccionarPrioridad('critica', this)">
                            <i class="fas fa-fire me-2"></i>
                            <span class="fw-bold">Crítica</span>
                            <br><small class="text-muted">Sistema caído o bloqueado</small>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="/Tickets" class="btn btn-outline-secondary">
            <i class="fas fa-times me-2"></i>Cancelar
        </a>
        <button type="submit" class="btn btn-primary" id="btnGuardar">
            <i class="fas fa-save me-2"></i>Crear Ticket
        </button>
    </div>

</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function seleccionarPrioridad(valor, el) {
    document.querySelectorAll('.prioridad-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('inputPrioridad').value = valor;
}

document.getElementById('ticketForm').addEventListener('submit', function(e) {
    const cliente     = document.getElementById('selectCliente').value;
    const titulo      = document.getElementById('titulo').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();

    if (!cliente) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'Selecciona un cliente.', confirmButtonColor:'#005C3E' });
        return;
    }
    if (!titulo) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'El título es obligatorio.', confirmButtonColor:'#005C3E' });
        return;
    }
    if (!descripcion) {
        e.preventDefault();
        Swal.fire({ icon:'error', title:'Campo requerido',
            text:'La descripción es obligatoria.', confirmButtonColor:'#005C3E' });
        return;
    }

    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
});

// Carga suscripciones al seleccionar cliente
document.getElementById('selectCliente').addEventListener('change', function() {
    const clienteId = this.value;
    const div       = document.getElementById('divSuscripcion');
    const select    = document.getElementById('selectSuscripcion');

    if (!clienteId) {
        div.style.display = 'none';
        select.innerHTML  = '<option value="">Cargando...</option>';
        return;
    }

    fetch(`/Tickets/suscripciones?cliente_id=${clienteId}`, { credentials:'same-origin' })
        .then(r => r.json())
        .then(data => {
            select.innerHTML = '<option value="">Selecciona una suscripción</option>';
            if (data.length === 0) {
                select.innerHTML = '<option value="">Sin suscripciones activas</option>';
            } else {
                data.forEach(s => {
                    const vence = new Date(s.fecha_vencimiento).toLocaleDateString('es-HN');
                    select.innerHTML += `<option value="${s.id}">${s.plan_nombre} — Vence: ${vence}</option>`;
                });
                // Si solo hay una, la selecciona automáticamente
                if (data.length === 1) select.value = data[0].id;
            }
            div.style.display = 'block';
        });
});
</script>