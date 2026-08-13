<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Views/examenes/index.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Vista: listado de exámenes e historial por paciente.
 * ==================================================================
 */

/** @var array $examenes */
/** @var int $pacienteId */
/** @var array $pacientes */
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="fw-bold mb-0"><i class="bi bi-eyedropper me-2"></i>Exámenes</h2>
    <a href="<?= e(app_url('/examenes/nuevo' . ($pacienteId > 0 ? '?paciente_id=' . $pacienteId : ''))) ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nuevo examen
    </a>
</div>

<div class="card card-custom p-4 mb-4">
    <form method="get" action="<?= e(app_url('/examenes')) ?>" class="row g-2 align-items-end">
        <div class="col-12 col-md-8">
            <label class="form-label">Paciente</label>
            <select name="paciente_id" class="form-select" onchange="this.form.submit()">
                <option value="">Todos los pacientes</option>
                <?php foreach ($pacientes as $p):
                    $nombre = trim(($p['primer_nombre'] ?? '') . ' ' . ($p['segundo_nombre'] ?? '')
                        . ' ' . ($p['apellido_paterno'] ?? '') . ' ' . ($p['apellido_materno'] ?? ''));
                    ?>
                    <option value="<?= (int)$p['id'] ?>" <?= $pacienteId === (int)$p['id'] ? 'selected' : '' ?>>
                        <?= e(trim($nombre . ' — ' . ($p['identificacion'] ?? ''))) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-4 d-grid">
            <button class="btn btn-secondary"><i class="bi bi-search me-1"></i> Filtrar</button>
        </div>
    </form>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Paciente</th>
                <th>Fecha</th>
                <th class="d-none d-md-table-cell">OD</th>
                <th class="d-none d-md-table-cell">OS</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($examenes) > 0): ?>
                <?php foreach ($examenes as $ex):
                    $fmt = function (?string $esf, ?string $cil, ?string $eje): string {
                        if ($esf === null && $cil === null) {
                            return '—';
                        }
                        return trim((string)($esf === null ? '' : $esf)
                            . '/' . (string)($cil === null ? '' : $cil)
                            . '/' . (string)($eje === null ? '' : $eje), '/');
                    };
                    ?>
                    <tr>
                        <td>
                            <strong><?= e($ex['paciente_nombre']) ?></strong>
                            <small class="text-muted d-block"><?= e($ex['paciente_identificacion']) ?></small>
                        </td>
                        <td><?= e(date('d/m/Y', strtotime($ex['fecha_examen']))) ?></td>
                        <td class="d-none d-md-table-cell"><?= e($fmt($ex['od_esfera'], $ex['od_cilindro'], $ex['od_eje'])) ?></td>
                        <td class="d-none d-md-table-cell"><?= e($fmt($ex['os_esfera'], $ex['os_cilindro'], $ex['os_eje'])) ?></td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                <a href="<?= e(app_url('/examenes/' . $ex['id'])) ?>" class="btn btn-sm btn-outline-primary" title="Ver"><i class="bi bi-eye"></i></a>
                                <a href="<?= e(app_url('/examenes/editar/' . $ex['id'])) ?>" class="btn btn-sm btn-outline-secondary" title="Editar"><i class="bi bi-pencil"></i></a>
                                <form method="post" action="<?= e(app_url('/examenes/eliminar/' . $ex['id'])) ?>" class="d-inline"
                                      data-confirm="¿Eliminar este examen de <?= e($ex['paciente_nombre']) ?>? Esta acción no se puede deshacer.">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No hay exámenes registrados<?= $pacienteId > 0 ? ' para este paciente' : '' ?>.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
