<?php /** @var int $total_pacientes */ ?>
<h2 class="fw-bold mb-4"><i class="bi bi-grid-fill text-primary me-2"></i>Panel de Control</h2>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 card-dash-1">
            <h6 class="text-muted">Pacientes</h6>
            <h3 class="fw-bold"><?= number_format($total_pacientes) ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 card-dash-2">
            <h6 class="text-muted">Exámenes hoy</h6>
            <h3 class="fw-bold">—</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 card-dash-3">
            <h6 class="text-muted">Ventas mes</h6>
            <h3 class="fw-bold">—</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 card-dash-4">
            <h6 class="text-muted">Órdenes pendientes</h6>
            <h3 class="fw-bold">—</h3>
        </div>
    </div>
</div>

<div class="card card-custom p-4">
    <h5 class="mb-3"><i class="bi bi-clock-history me-2"></i>Últimos pacientes</h5>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr><th>Paciente</th><th>Identificación</th><th>Edad</th><th></th></tr>
            </thead>
            <tbody>
            <?php if (!empty($ultimos)): ?>
                <?php foreach ($ultimos as $p): ?>
                    <tr>
                        <td><strong><?= e(trim($p['primer_nombre'] . ' ' . $p['segundo_nombre'] . ' ' . $p['apellido_paterno'] . ' ' . $p['apellido_materno'])) ?></strong></td>
                        <td><?= e($p['identificacion']) ?></td>
                        <td><span class="badge bg-secondary"><?= e(format_edad($p['fecha_nacimiento'])) ?></span></td>
                        <td><a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('/pacientes')) ?>">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-muted text-center py-4">Aún no hay pacientes registrados.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>