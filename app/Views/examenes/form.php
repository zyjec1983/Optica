<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Views/examenes/form.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Vista: formulario de examen visual con firma manuscrita.
 * ==================================================================
 */

/** @var \App\Models\Examen $examen */
/** @var bool $esEdicion */
/** @var \App\Models\Paciente|null $pacienteSel */
/** @var int $pacienteId */
$pv = fn(string $key, string $fallback = '') => old($key, $fallback);
$pacId = (int)$pv('paciente_id', (string)($pacienteId > 0 ? $pacienteId : ($examen->paciente_id ?? '')));
$pacSelNombre = $pacienteSel !== null ? trim($pacienteSel->nombreCompleto() . ' — ' . $pacienteSel->identificacion) : '';
// Valor de una prueba complementaria (vuelve de old() o de la entidad).
$pruebaVal = function (string $clave, string $campo) use ($examen): string {
    $old = $_SESSION['_old']['pruebas'][$clave][$campo] ?? null;
    if ($old !== null && $old !== '') {
        return (string)$old;
    }
    $p = $examen->pruebas[$clave] ?? null;
    if ($p === null) {
        return '';
    }
    $v = $p->{$campo} ?? null;
    if ($v === null) {
        return '';
    }
    return $campo === 'normal' ? ($v ? '1' : '0') : (string)$v;
};
$fecha = $pv('fecha_examen', (string)$examen->fecha_examen);
$odE = $pv('od_esfera', (string)$examen->od_esfera);
$odC = $pv('od_cilindro', (string)$examen->od_cilindro);
$odJ = $pv('od_eje', (string)$examen->od_eje);
$osE = $pv('os_esfera', (string)$examen->os_esfera);
$osC = $pv('os_cilindro', (string)$examen->os_cilindro);
$osJ = $pv('os_eje', (string)$examen->os_eje);
$dp  = $pv('dp', (string)$examen->dp);
$add = $pv('add_value', (string)$examen->add_value);
$diag = $pv('diagnostico', (string)$examen->diagnostico);
$obs = $pv('observaciones', (string)$examen->observaciones);
$firmaRep = $examen->firma_representante;
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="fw-bold mb-0">
        <i class="bi bi-eyedropper me-2"></i><?= $esEdicion ? 'Editar examen' : 'Nuevo examen' ?>
    </h2>
    <a href="<?= e(app_url('/examenes')) ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<form method="post"
      action="<?= e(app_url($esEdicion ? '/examenes/editar/' . $examen->id : '/examenes')) ?>"
      id="examenForm">
    <?= csrf_field() ?>

    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-person-vcard me-2"></i>Paciente y fecha</h6>
        <div class="row g-3">
            <div class="col-12 col-md-8">
                <label class="fw-semibold">Buscar paciente (cédula, nombres o apellidos)</label>
                <div class="position-relative">
                    <input type="text" id="buscarPacienteForm" class="form-control" required
                           placeholder="Escriba la cédula o el nombre del paciente..."
                           value="<?= e($pacSelNombre) ?>" autocomplete="off" minlength="2">
                    <input type="hidden" name="paciente_id" id="pacienteIdForm" value="<?= (int)$pacId ?>">
                    <div class="list-group position-absolute w-100 shadow" id="coincidenciasForm" style="z-index:1030; display:none;"></div>
                </div>
                <div class="form-text" id="coincidenciasInfoForm"></div>
                <div class="form-text">¿El paciente no existe? <a href="<?= e(app_url('/pacientes/nuevo')) ?>">Regístrelo aquí</a>.</div>
                <?php if (!$esEdicion): ?>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="recordatorio_lentes" value="1" id="recLentes">
                        <label class="form-check-label" for="recLentes">
                            <i class="bi bi-whatsapp text-success me-1"></i>
                            Crear recordatorio para avisar por WhatsApp cuando los lentes estén listos
                        </label>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-12 col-md-4">
                <label class="fw-semibold">Fecha del examen</label>
                <input type="date" class="form-control" name="fecha_examen" value="<?= e($fecha) ?>" required max="<?= date('Y-m-d') ?>">
            </div>
        </div>
    </div>

    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-eye me-2"></i>Refracción</h6>
        <div class="row g-4">
            <!-- Ojo derecho -->
            <div class="col-12 col-lg-6">
                <div class="border rounded p-3 h-100 bg-light-subtle">
                    <h6 class="fw-bold text-primary mb-3">OD · Ojo derecho</h6>
                    <div class="row g-2">
                        <div class="col-6 col-sm-4">
                            <label>Esfera</label>
                            <input type="text" class="form-control" name="od_esfera" value="<?= e($odE) ?>" placeholder="-1.25" inputmode="decimal">
                        </div>
                        <div class="col-6 col-sm-4">
                            <label>Cilindro</label>
                            <input type="text" class="form-control" name="od_cilindro" value="<?= e($odC) ?>" placeholder="-0.50" inputmode="decimal">
                        </div>
                        <div class="col-6 col-sm-4">
                            <label>Eje</label>
                            <input type="number" class="form-control" name="od_eje" value="<?= e($odJ) ?>" placeholder="90" min="0" max="180">
                        </div>
                    </div>
                </div>
            </div>
            <!-- Ojo izquierdo -->
            <div class="col-12 col-lg-6">
                <div class="border rounded p-3 h-100 bg-light-subtle">
                    <h6 class="fw-bold text-success mb-3">OS · Ojo izquierdo</h6>
                    <div class="row g-2">
                        <div class="col-6 col-sm-4">
                            <label>Esfera</label>
                            <input type="text" class="form-control" name="os_esfera" value="<?= e($osE) ?>" placeholder="-1.25" inputmode="decimal">
                        </div>
                        <div class="col-6 col-sm-4">
                            <label>Cilindro</label>
                            <input type="text" class="form-control" name="os_cilindro" value="<?= e($osC) ?>" placeholder="-0.50" inputmode="decimal">
                        </div>
                        <div class="col-6 col-sm-4">
                            <label>Eje</label>
                            <input type="number" class="form-control" name="os_eje" value="<?= e($osJ) ?>" placeholder="90" min="0" max="180">
                        </div>
                    </div>
                </div>
            </div>
            <!-- DP y ADD -->
            <div class="col-6 col-md-3 col-lg-2">
                <label class="fw-semibold">DP (mm)</label>
                <input type="text" class="form-control" name="dp" value="<?= e($dp) ?>" placeholder="62" inputmode="decimal">
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label class="fw-semibold">ADD</label>
                <input type="text" class="form-control" name="add_value" value="<?= e($add) ?>" placeholder="+2.00" inputmode="decimal">
            </div>
        </div>
    </div>

    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold text-muted mb-3">
            <i class="bi bi-clipboard2-check me-2"></i>Pruebas de la consulta
        </h6>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>Prueba</th>
                    <th style="width:120px">OD · Derecho</th>
                    <th style="width:120px">OS · Izquierdo</th>
                    <th style="width:170px">Resultado</th>
                    <th style="width:150px">Estado</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach (\App\Models\PruebaExamen::PRUEBAS as $clave => $cfg): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($cfg['label']) ?></td>
                        <?php if ($cfg['tipo'] === 'ojos'): ?>
                            <td>
                                <input class="form-control form-control-sm" name="pruebas[<?= e($clave) ?>][od]"
                                       value="<?= e($pruebaVal($clave, 'od')) ?>" placeholder="<?= e($cfg['placeholder']) ?>">
                            </td>
                            <td>
                                <input class="form-control form-control-sm" name="pruebas[<?= e($clave) ?>][os]"
                                       value="<?= e($pruebaVal($clave, 'os')) ?>" placeholder="<?= e($cfg['placeholder']) ?>">
                            </td>
                            <td></td>
                        <?php else: ?>
                            <td></td>
                            <td></td>
                            <td>
                                <input class="form-control form-control-sm" name="pruebas[<?= e($clave) ?>][resultado]"
                                       value="<?= e($pruebaVal($clave, 'resultado')) ?>" placeholder="<?= e($cfg['placeholder']) ?>">
                            </td>
                        <?php endif; ?>
                        <td>
                            <select class="form-select form-select-sm" name="pruebas[<?= e($clave) ?>][normal]">
                                <option value="">—</option>
                                <option value="1" <?= $pruebaVal($clave, 'normal') === '1' ? 'selected' : '' ?>>Normal</option>
                                <option value="0" <?= $pruebaVal($clave, 'normal') === '0' ? 'selected' : '' ?>>Anormal</option>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="form-text">Refracción y astigmatismo se registran en la sección anterior (OD/OS: esfera, cilindro y eje).</div>
    </div>

    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-clipboard2-pulse me-2"></i>Diagnóstico y observaciones</h6>
        <div class="row g-3">
            <div class="col-12">
                <label class="fw-semibold">Diagnóstico</label>
                <input type="text" class="form-control" name="diagnostico" value="<?= e($diag) ?>"
                       placeholder="Ej: Miopía bilateral, astigmatismo...">
            </div>
            <div class="col-12">
                <label class="fw-semibold">Observaciones</label>
                <textarea class="form-control" name="observaciones" rows="3"
                          placeholder="Indicaciones, recomendaciones, próxima revisión..."><?= e($obs) ?></textarea>
            </div>
        </div>
    </div>

    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-pen me-2"></i>Firma manuscrita electrónica</h6>

        <?php if ($esEdicion && $examen->firma): ?>
            <div class="mb-3">
                <label class="fw-semibold d-block">Firma actual</label>
                <img src="<?= e($examen->firma) ?>" alt="Firma actual" class="border rounded bg-white" style="max-height:120px;">
            </div>
        <?php endif; ?>

        <canvas id="firmaCanvas" width="600" height="200" class="border rounded w-100"
                style="touch-action:none; background:#fff; cursor:crosshair;"></canvas>
        <div class="d-flex flex-wrap align-items-center gap-3 mt-2">
            <button type="button" id="limpiarFirma" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-eraser me-1"></i> Limpiar
            </button>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="firma_representante" value="1" id="firmaRep" <?= $firmaRep ? 'checked' : '' ?>>
                <label class="form-check-label" for="firmaRep">Firmó el representante (menor de edad)</label>
            </div>
        </div>
        <div class="form-text">Firme con el mouse o con el dedo en dispositivos táctiles.</div>
        <input type="hidden" name="firma" id="firmaData"
               value="<?= e($esEdicion ? (string)$examen->firma : '') ?>">
        <div class="invalid-feedback" id="firmaError"></div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i> <?= $esEdicion ? 'Actualizar examen' : 'Guardar examen' ?>
        </button>
        <a href="<?= e(app_url('/examenes')) ?>" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>

<script>
    // ===== Firma manuscrita con Canvas (pointer events: mouse + táctil) =====
    (function () {
        const canvas = document.getElementById('firmaCanvas');
        const limpiar = document.getElementById('limpiarFirma');
        const firmaData = document.getElementById('firmaData');
        const firmaError = document.getElementById('firmaError');
        const ctx = canvas.getContext('2d');
        let dibujando = false;

        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#000';

        // Para ediciones: precargar la firma existente sobre el canvas.
        if (firmaData.value) {
            const img = new Image();
            img.onload = function () {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            };
            img.src = firmaData.value;
        }

        function posicion(e) {
            const rect = canvas.getBoundingClientRect();
            const escalaX = canvas.width / rect.width;
            const escalaY = canvas.height / rect.height;
            return {
                x: (e.clientX - rect.left) * escalaX,
                y: (e.clientY - rect.top) * escalaY
            };
        }

        canvas.addEventListener('pointerdown', function (e) {
            e.preventDefault();
            canvas.setPointerCapture(e.pointerId);
            dibujando = true;
            const p = posicion(e);
            ctx.beginPath();
            ctx.moveTo(p.x, p.y);
        });

        canvas.addEventListener('pointermove', function (e) {
            if (!dibujando) return;
            const p = posicion(e);
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
        });

        function terminar(e) {
            if (!dibujando) return;
            dibujando = false;
            // Guardar la imagen en el campo oculto.
            firmaData.value = canvas.toDataURL('image/png');
            firmaError.textContent = '';
        }
        canvas.addEventListener('pointerup', terminar);
        canvas.addEventListener('pointercancel', terminar);

        limpiar.addEventListener('click', function () {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            firmaData.value = '';
        });

        // Validación al enviar: paciente, fecha y firma son obligatorios.
        // Se desactiva la validación nativa del navegador para mostrar un
        // toast único y hacer scroll al primer campo que falte.
        const form = document.getElementById('examenForm');
        form.setAttribute('novalidate', '');
        form.addEventListener('submit', function (e) {
            const fallos = [];

            const pacHidden = document.getElementById('pacienteIdForm');
            const pacSearch = document.getElementById('buscarPacienteForm');
            if (!pacHidden.value || parseInt(pacHidden.value, 10) <= 0) {
                fallos.push({ el: pacSearch, msg: 'Seleccione un paciente válido de la lista.' });
            }

            const fechaInput = form.querySelector('input[name="fecha_examen"]');
            if (!fechaInput.value) {
                fallos.push({ el: fechaInput, msg: 'La fecha del examen es obligatoria.' });
            }

            if (!firmaData.value) {
                fallos.push({ el: canvas, msg: 'Debe capturar la firma del paciente o representante.' });
            }

            if (fallos.length > 0) {
                e.preventDefault();
                const primero = fallos[0];
                primero.el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                primero.el.focus({ preventScroll: true });
                primero.el.classList.add('is-invalid');
                setTimeout(() => primero.el.classList.remove('is-invalid'), 3500);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: primero.msg,
                        timer: 4000,
                        showConfirmButton: false
                    });
                } else {
                    alert(primero.msg);
                }
            }
        });
    })();
</script>

<script src="<?= e(app_url('/assets/js/paciente-buscador.js')) ?>"></script>
<script>
    PacienteBuscador.init({
        input: '#buscarPacienteForm',
        hidden: '#pacienteIdForm',
        lista: '#coincidenciasForm',
        info: '#coincidenciasInfoForm',
        url: <?= json_encode(app_url('/pacientes/buscar')) ?>
    });
</script>
