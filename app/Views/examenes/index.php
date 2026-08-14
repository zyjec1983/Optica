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
/** @var \App\Models\Paciente|null $pacienteSel */
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="fw-bold mb-0"><i class="bi bi-eyedropper me-2"></i>Exámenes</h2>
    <a href="<?= e(app_url('/examenes/nuevo' . ($pacienteId > 0 ? '?paciente_id=' . $pacienteId : ''))) ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nuevo examen
    </a>
</div>

<div class="card card-custom p-4 mb-4">
    <label class="form-label fw-semibold">Buscar paciente</label>
    <div class="position-relative">
        <input type="text" id="buscarPacienteLista" class="form-control form-control-lg"
               placeholder="Escriba cédula, nombres o apellidos... ej: Christian, Rodriguez, 0920018736"
               value="<?= $pacienteSel ? e(trim($pacienteSel->nombreCompleto() . ' — ' . $pacienteSel->identificacion)) : '' ?>"
               autocomplete="off" minlength="2">
        <div class="list-group position-absolute w-100 shadow" id="coincidenciasExamenes" style="z-index:1030; display:none;"></div>
    </div>
    <div class="form-text" id="coincidenciasInfoExamenes"></div>
</div>

<?php if ($pacienteSel !== null): ?>
<div class="card card-custom p-4 mb-4 border-start border-4 border-primary">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="mb-1"><i class="bi bi-person-vcard me-2"></i><?= e($pacienteSel->nombreCompleto()) ?></h5>
            <div class="text-muted">
                <span class="badge bg-light text-dark border"><?= e($pacienteSel->identificacion) ?></span>
                <?php if ($pacienteSel->telefono !== ''): ?>
                    <span class="badge bg-light text-dark border"><i class="bi bi-telephone"></i> <?= e($pacienteSel->telefono) ?></span>
                <?php endif; ?>
                <span class="badge bg-light text-dark border"><?= e(format_edad($pacienteSel->fecha_nacimiento)) ?></span>
            </div>
            <div class="mt-2"><span class="badge bg-primary">Historial de exámenes</span></div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= e(app_url('/examenes/nuevo?paciente_id=' . $pacienteSel->id)) ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nuevo examen
            </a>
            <a href="<?= e(app_url('/examenes')) ?>" class="btn btn-outline-secondary">Ver todos</a>
        </div>
    </div>
</div>
<?php endif; ?>

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
                                      data-confirm="¿Eliminar el examen de <?= e($ex['paciente_nombre']) ?>? Se marcará como eliminado; la información no se borra del sistema.">
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

<script src="<?= e(app_url('/assets/js/paciente-buscador.js')) ?>"></script>
<script>
    PacienteBuscador.init({
        input: '#buscarPacienteLista',
        lista: '#coincidenciasExamenes',
        info: '#coincidenciasInfoExamenes',
        url: <?= json_encode(app_url('/pacientes/buscar')) ?>,
        onSelect: function (p) {
            window.location.href = <?= json_encode(app_url('/examenes')) ?> + '?paciente_id=' + p.id;
        }
    });
</script>
