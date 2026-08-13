<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Views/citas/form.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Vista: presentación HTML responsiva (Bootstrap 5).
 * ==================================================================
 */


/** @var \App\Models\Cita $cita */
/** @var bool $esEdicion */
/** @var array $pacientes */
$pv = fn(string $key, string $fallback = '') => old($key, $fallback);
$pacienteId = (int)$pv('paciente_id', (string)($cita->paciente_id ?? ''));
$fecha = $pv('fecha', (string)$cita->fecha);
$hora = $pv('hora', $cita->hora !== '' ? date('H:i', strtotime($cita->hora)) : '');
$motivo = $pv('motivo', $cita->motivo);
$notas = $pv('notas', (string)$cita->notas);
$estado = $pv('estado', $cita->estado);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">
        <i class="bi bi-calendar2-plus me-2"></i><?= $esEdicion ? 'Editar cita' : 'Nueva cita' ?>
    </h2>
    <a href="<?= e(app_url('/citas')) ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="card card-custom p-4" style="max-width: 720px;">
    <form method="post"
          action="<?= e(app_url($esEdicion ? '/citas/editar/' . $cita->id : '/citas')) ?>">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="fw-semibold">Paciente</label>
            <select name="paciente_id" class="form-select" required>
                <option value="">— Seleccione un paciente —</option>
                <?php foreach ($pacientes as $p):
                    $nombre = trim(($p['primer_nombre'] ?? '') . ' ' . ($p['segundo_nombre'] ?? '')
                        . ' ' . ($p['apellido_paterno'] ?? '') . ' ' . ($p['apellido_materno'] ?? ''));
                    ?>
                    <option value="<?= (int)$p['id'] ?>" <?= $pacienteId === (int)$p['id'] ? 'selected' : '' ?>>
                        <?= e(trim($nombre . ' — ' . ($p['identificacion'] ?? ''))) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="form-text">¿El paciente no existe? <a href="<?= e(app_url('/pacientes/nuevo')) ?>">Regístrelo aquí</a>.</div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Fecha</label>
                <input type="date" class="form-control" name="fecha" value="<?= e($fecha) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Hora</label>
                <input type="time" class="form-control" name="hora" value="<?= e($hora) ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="fw-semibold">Motivo</label>
            <input type="text" class="form-control" name="motivo" value="<?= e($motivo) ?>"
                   placeholder="Ej: Examen de vista, control de lentes, adaptación de lentes de contacto...">
        </div>

        <div class="mb-3">
            <label class="fw-semibold">Notas</label>
            <textarea class="form-control" name="notas" rows="3" placeholder="Notas adicionales (opcional)"><?= e($notas) ?></textarea>
        </div>

        <?php if ($esEdicion): ?>
        <div class="mb-3">
            <label class="fw-semibold">Estado</label>
            <select name="estado" class="form-select">
                <?php foreach (\App\Models\Cita::ESTADOS as $e): ?>
                    <option value="<?= e($e) ?>" <?= $estado === $e ? 'selected' : '' ?>><?= e(ucfirst($e)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check-lg"></i> <?= $esEdicion ? 'Actualizar cita' : 'Guardar cita' ?>
            </button>
            <a href="<?= e(app_url('/citas')) ?>" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>
