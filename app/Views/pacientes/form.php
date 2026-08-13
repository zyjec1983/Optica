<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Views/pacientes/form.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Vista: presentación HTML responsiva (Bootstrap 5).
 * ==================================================================
 */


/** @var \App\Models\Paciente $paciente */
/** @var bool $esEdicion */
$pv = fn(string $key, string $fallback = '') => old($key, $fallback);
$tipoId = $pv('tipo_identificacion', $paciente->tipo_identificacion);
$ident  = $pv('identificacion', $paciente->identificacion);
$apa    = $pv('apellido_paterno', $paciente->apellido_paterno);
$ama    = $pv('apellido_materno', $paciente->apellido_materno);
$pn     = $pv('primer_nombre', $paciente->primer_nombre);
$sn     = $pv('segundo_nombre', $paciente->segundo_nombre);
$sexo   = $pv('sexo', $paciente->sexo);
$fechaN = $pv('fecha_nacimiento', (string)$paciente->fecha_nacimiento);
$tel    = $pv('telefono', $paciente->telefono);
$email  = $pv('email', $paciente->email);
$repParentesco = $pv('rep_parentesco', $paciente->rep_parentesco);
$repNombres    = $pv('rep_nombres', $paciente->rep_nombres);
$repCedula     = $pv('rep_cedula', $paciente->rep_cedula);
$repTelefono   = $pv('rep_telefono', $paciente->rep_telefono);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">
        <i class="bi bi-person-plus-fill me-2"></i><?= $esEdicion ? 'Editar paciente' : 'Nuevo paciente' ?>
    </h2>
    <a href="<?= e(app_url('/pacientes')) ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="card card-custom p-4">
    <form method="post"
          action="<?= e(app_url($esEdicion ? '/pacientes/editar/' . $paciente->id : '/pacientes')) ?>">
        <?= csrf_field() ?>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="fw-semibold">Tipo identificación</label>
                <select name="tipo_identificacion" class="form-select">
                    <?php foreach (\App\Models\Paciente::IDENTIFICACIONES as $t): ?>
                        <option value="<?= e($t) ?>" <?= $tipoId === $t ? 'selected' : '' ?>>
                            <?= e(ucfirst($t)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="fw-semibold">Número</label>
                <input class="form-control" name="identificacion" value="<?= e($ident) ?>"
                       placeholder="0923456789" required>
            </div>

            <div class="col-md-3">
                <label>Apellido paterno</label>
                <input class="form-control" name="apellido_paterno" value="<?= e($apa) ?>" required>
            </div>
            <div class="col-md-3">
                <label>Apellido materno</label>
                <input class="form-control" name="apellido_materno" value="<?= e($ama) ?>">
            </div>
            <div class="col-md-3">
                <label>Primer nombre</label>
                <input class="form-control" name="primer_nombre" value="<?= e($pn) ?>" required>
            </div>
            <div class="col-md-3">
                <label>Segundo nombre</label>
                <input class="form-control" name="segundo_nombre" value="<?= e($sn) ?>">
            </div>

            <div class="col-md-4">
                <label>Sexo</label>
                <select name="sexo" class="form-select">
                    <option value="M" <?= $sexo === 'M' ? 'selected' : '' ?>>Masculino</option>
                    <option value="F" <?= $sexo === 'F' ? 'selected' : '' ?>>Femenino</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Fecha de nacimiento</label>
                <input type="date" class="form-control" name="fecha_nacimiento" id="paciente-fecha-nac"
                       value="<?= e($fechaN) ?>" onchange="calcularEdadFront()" required
                       max="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-4">
                <label>Edad calculada</label>
                <input type="text" class="form-control" id="paciente-edad" readonly placeholder="años, meses, días">
            </div>

            <div class="col-md-6">
                <label>Teléfono</label>
                <input class="form-control" name="telefono" value="<?= e($tel) ?>" placeholder="0999999999">
            </div>
            <div class="col-md-6">
                <label>Correo electrónico</label>
                <input type="email" class="form-control" name="email" value="<?= e($email) ?>">
            </div>
        </div>

        <div id="representante-container" class="mt-3 border p-3 rounded bg-light" style="display:none;">
            <h6 class="fw-bold text-danger">
                <i class="bi bi-exclamation-triangle-fill"></i> Menor de edad - Representante obligatorio
            </h6>
            <div class="row g-2">
                <div class="col-md-3">
                    <label>Parentesco</label>
                    <select name="rep_parentesco" class="form-select">
                        <option value="Madre" <?= $repParentesco === 'Madre' ? 'selected' : '' ?>>Madre</option>
                        <option value="Padre" <?= $repParentesco === 'Padre' ? 'selected' : '' ?>>Padre</option>
                        <option value="Tutor" <?= $repParentesco === 'Tutor' ? 'selected' : '' ?>>Tutor</option>
                        <option value="Otro" <?= $repParentesco === 'Otro' ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Nombres completos</label>
                    <input class="form-control" name="rep_nombres" value="<?= e($repNombres) ?>" placeholder="Nombre del representante">
                </div>
                <div class="col-md-3">
                    <label>Cédula</label>
                    <input class="form-control" name="rep_cedula" value="<?= e($repCedula) ?>" placeholder="Número de cédula">
                </div>
                <div class="col-md-3">
                    <label>Teléfono</label>
                    <input class="form-control" name="rep_telefono" value="<?= e($repTelefono) ?>" placeholder="0999999999">
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check-lg"></i> <?= $esEdicion ? 'Actualizar paciente' : 'Guardar paciente' ?>
            </button>
            <a href="<?= e(app_url('/pacientes')) ?>" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
    function validarCedulaEC(cedula) {
        cedula = cedula.replace(/\D/g, '');
        if (cedula.length !== 10) return false;
        const provincia = parseInt(cedula.substring(0, 2), 10);
        if (provincia < 1 || provincia > 24) return false;
        if (parseInt(cedula[2], 10) >= 6) return false;
        let suma = 0;
        const coef = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        for (let i = 0; i < 9; i++) {
            let v = parseInt(cedula[i], 10) * coef[i];
            if (v >= 10) v -= 9;
            suma += v;
        }
        const verificador = (10 - (suma % 10)) % 10;
        return verificador === parseInt(cedula[9], 10);
    }

    function validarRucEC(ruc) {
        ruc = ruc.replace(/\D/g, '');
        if (ruc.length !== 13) return false;
        if (ruc.substring(10) !== '001') return false;
        return validarCedulaEC(ruc.substring(0, 10));
    }

    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form.matches('form[action*="/pacientes"]')) return;

        // Evitar doble validación (form.matches evalúa ambos por defecto)
        if (!form.hasAttribute('data-validando')) {
            const tipo = form.querySelector('[name="tipo_identificacion"]').value;
            const numero = form.querySelector('[name="identificacion"]').value;
            let ok = true;
            let msg = '';
            if (tipo === 'cedula') {
                if (!validarCedulaEC(numero)) {
                    ok = false;
                    msg = 'La cédula ingresada no es válida. Verifique el número.';
                }
            } else if (tipo === 'ruc') {
                if (!validarRucEC(numero)) {
                    ok = false;
                    msg = 'El RUC ingresado no es válido.';
                }
            }
            if (!ok) {
                e.preventDefault();
                form.setAttribute('data-validando', '1');
                const campo = form.querySelector('[name="identificacion"]');
                campo.classList.add('is-invalid');
                let fb = campo.parentElement.querySelector('.invalid-feedback');
                if (!fb) {
                    fb = document.createElement('div');
                    fb.className = 'invalid-feedback';
                    campo.parentElement.appendChild(fb);
                }
                fb.textContent = msg;
                setTimeout(() => form.removeAttribute('data-validando'), 300);
                campo.focus();
                return;
            }
        }
    });

    function calcularEdadFront() {
        const dob = document.getElementById('paciente-fecha-nac').value;
        const edadInput = document.getElementById('paciente-edad');
        const rep = document.getElementById('representante-container');
        if (!dob) { edadInput.value = ''; rep.style.display = 'none'; return; }
        const birth = new Date(dob);
        const today = new Date();
        let years = today.getFullYear() - birth.getFullYear();
        let months = today.getMonth() - birth.getMonth();
        let days = today.getDate() - birth.getDate();
        if (days < 0) { months--; days += new Date(today.getFullYear(), today.getMonth(), 0).getDate(); }
        if (months < 0) { years--; months += 12; }
        edadInput.value = years + ' años, ' + months + ' meses, ' + days + ' días';
        rep.style.display = years < 18 ? 'block' : 'none';
    }
    <?php if ($esEdicion && $paciente->fecha_nacimiento): ?>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if ($paciente->esMenor()): ?>
        document.getElementById('representante-container').style.display = 'block';
        <?php endif; ?>
        const fn = document.getElementById('paciente-fecha-nac').value;
        if (fn) calcularEdadFront();
    });
    <?php endif; ?>
</script>