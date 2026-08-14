<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Views/dashboard/index.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Vista: presentación HTML responsiva (Bootstrap 5).
 * ==================================================================
 */

 /** @var int $total_pacientes */ ?>
<h2 class="fw-bold mb-4"><i class="bi bi-grid-fill text-primary me-2"></i>Panel de Control</h2>

<?php
/** @var array $recordatorios_pendientes */
$recPendientes = $recordatorios_pendientes ?? [];
if (count($recPendientes) > 0): ?>
<div class="card card-custom p-4 mb-4 border-start border-4 border-success">
    <h5 class="mb-3"><i class="bi bi-whatsapp text-success me-2"></i>Lentes listos para retiro
        <span class="badge bg-success ms-1"><?= count($recPendientes) ?></span>
    </h5>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr><th>Paciente</th><th>Teléfono</th><th>Fecha</th><th>Mensaje</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
            <?php foreach ($recPendientes as $rec):
                $nombre = trim($rec->paciente_nombre);
                $tel = $rec->paciente_telefono;
                $mensaje = $rec->mensaje ?? '';
                $wa = whatsapp_link($tel, $mensaje);
                ?>
                <tr>
                    <td><strong><?= e($nombre) ?></strong>
                        <small class="text-muted d-block"><?= e($rec->paciente_identificacion) ?></small>
                    </td>
                    <td>
                        <?php if ($tel !== ''): ?>
                            <a href="<?= e(whatsapp_link($tel, $mensaje !== '' ? $mensaje : 'Sus lentes están listos')) ?>"
                               class="text-success text-decoration-none" target="_blank" rel="noopener">
                                <i class="bi bi-telephone"></i> <?= e($tel) ?>
                            </a>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Sin teléfono</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-nowrap"><?= e($rec->fecha_recordatorio ?? '—') ?></td>
                    <td><small class="text-muted"><?= e(mb_strimwidth($mensaje, 0, 80, '…')) ?></small></td>
                    <td class="text-end text-nowrap">
                        <?php if ($wa !== ''): ?>
                            <a class="btn btn-sm btn-success" href="<?= e($wa) ?>" target="_blank" rel="noopener"
                               title="Notificar por WhatsApp">
                                <i class="bi bi-whatsapp me-1"></i> WhatsApp
                            </a>
                        <?php endif; ?>
                        <form method="post" action="<?= e(app_url('/recordatorios/marcar/' . $rec->id)) ?>" class="d-inline"
                              data-confirm="¿Marcar como notificado al paciente <?= e($nombre) ?>? Dejará de aparecer en este panel.">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-secondary" title="Marcar como notificado">
                                <i class="bi bi-check2-circle"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

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
                        <td><strong><?= e(trim($p['primer_nombre'] . ' ' . $p['segundo_nombre'] . ' ' . $p['apellido_paterno'] . ' ' . $p['apellido_materno'])) ?></strong>
                            <?php if (!empty($p['telefono'])): ?>
                                <small class="text-muted d-block"><i class="bi bi-telephone"></i> <?= e($p['telefono']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= e($p['identificacion']) ?></td>
                        <td><span class="badge bg-secondary"><?= e(format_edad($p['fecha_nacimiento'])) ?></span></td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('/examenes?paciente_id=' . $p['id'])) ?>">
                                <i class="bi bi-clock-history"></i> Historial
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-muted text-center py-4">Aún no hay pacientes registrados.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>