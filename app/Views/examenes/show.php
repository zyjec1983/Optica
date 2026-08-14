<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Views/examenes/show.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Vista: detalle de un examen visual con firma.
 * ==================================================================
 */

/** @var \App\Models\Examen $examen */
$fmtRef = function (?string $esf, ?string $cil, ?string $eje): string {
    if ($esf === null && $cil === null && $eje === null) {
        return '—';
    }
    return trim(implode(' / ', array_filter([
        $esf !== null ? 'Esf ' . $esf : '',
        $cil !== null ? 'Cil ' . $cil : '',
        $eje !== null ? 'Eje ' . $eje : '',
    ])));
};
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="fw-bold mb-0"><i class="bi bi-eyedropper me-2"></i>Detalle del examen</h2>
    <div class="d-flex gap-2">
        <a href="<?= e(app_url('/examenes/editar/' . $examen->id)) ?>" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> Editar
        </a>
        <a href="<?= e(app_url('/examenes?paciente_id=' . $examen->paciente_id)) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-clock-history me-1"></i> Historial
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-5">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-person-vcard me-2"></i>Paciente</h6>
            <h4 class="fw-bold"><?= e($examen->paciente_nombre) ?></h4>
            <p class="text-muted mb-1">C.I.: <strong><?= e($examen->paciente_identificacion ?: '—') ?></strong></p>
            <p class="text-muted mb-0">Teléfono: <strong><?= e($examen->paciente_telefono ?: '—') ?></strong></p>
            <hr>
            <p class="mb-0">
                <span class="badge bg-primary"><?= e(date('d/m/Y', strtotime($examen->fecha_examen))) ?></span>
            </p>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-eye me-2"></i>Refracción</h6>
            <div class="row g-3">
                <div class="col-12 col-sm-6">
                    <div class="border rounded p-3 bg-light-subtle">
                        <h6 class="fw-bold text-primary mb-2">OD · Ojo derecho</h6>
                        <p class="mb-0"><?= e($fmtRef($examen->od_esfera, $examen->od_cilindro, $examen->od_eje)) ?></p>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="border rounded p-3 bg-light-subtle">
                        <h6 class="fw-bold text-success mb-2">OS · Ojo izquierdo</h6>
                        <p class="mb-0"><?= e($fmtRef($examen->os_esfera, $examen->os_cilindro, $examen->os_eje)) ?></p>
                    </div>
                </div>
                <div class="col-6 col-sm-4">
                    <label class="text-muted small">DP (distancia pupilar)</label>
                    <div class="fw-semibold"><?= $examen->dp !== null ? e($examen->dp . ' mm') : '—' ?></div>
                </div>
                <div class="col-6 col-sm-4">
                    <label class="text-muted small">ADD (adición)</label>
                    <div class="fw-semibold"><?= $examen->add_value !== null ? e($examen->add_value) : '—' ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-0">
    <div class="col-12 col-lg-7">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-clipboard2-pulse me-2"></i>Diagnóstico</h6>
            <p class="mb-0"><?= e($examen->diagnostico ?: '—') ?></p>
            <hr>
            <h6 class="fw-bold text-muted mb-2">Observaciones</h6>
            <p class="mb-0 text-justify"><?= e($examen->observaciones ?: '—') ?></p>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-pen me-2"></i>Firma del <?= e($examen->firmaLabel()) ?></h6>
            <?php if ($examen->firma): ?>
                <img src="<?= e($examen->firma) ?>" alt="Firma manuscrita" class="img-fluid border rounded bg-white p-2">
            <?php else: ?>
                <p class="text-muted mb-0">Sin firma.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$pruebas = $examen->pruebas ?? [];
$pruebasMostrar = [];
foreach (\App\Models\PruebaExamen::PRUEBAS as $clave => $cfg) {
    $p = $pruebas[$clave] ?? null;
    if ($p === null) {
        continue;
    }
    if ($p->od === null && $p->os === null && $p->resultado === null && $p->normal === null) {
        continue;
    }
    $pruebasMostrar[$clave] = $p;
}
?>
<?php if (count($pruebasMostrar) > 0): ?>
<div class="row g-3 mt-0">
    <div class="col-12">
        <div class="card card-custom p-4">
            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-clipboard2-check me-2"></i>Pruebas de la consulta</h6>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr><th>Prueba</th><th>OD · Derecho</th><th>OS · Izquierdo</th><th>Resultado</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pruebasMostrar as $clave => $p):
                        $tipo = \App\Models\PruebaExamen::PRUEBAS[$clave]['tipo'] ?? 'simple';
                        ?>
                        <tr>
                            <td class="fw-semibold"><?= e(\App\Models\PruebaExamen::PRUEBAS[$clave]['label']) ?></td>
                            <td><?= $tipo === 'ojos' ? e($p->od ?: '—') : '—' ?></td>
                            <td><?= $tipo === 'ojos' ? e($p->os ?: '—') : '—' ?></td>
                            <td><?= $tipo === 'simple' ? e($p->resultado ?: '—') : '—' ?></td>
                            <td>
                                <?php if ($p->normal === null): ?>
                                    <span class="text-muted">—</span>
                                <?php else: ?>
                                    <span class="badge <?= $p->normal ? 'bg-success' : 'bg-danger' ?>"><?= e($p->normalLabel()) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
