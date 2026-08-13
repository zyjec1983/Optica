<?php
/** @var array $citas */
/** @var string $fecha */
/** @var string $estado */
/** @var array $estados */
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <h2 class="fw-bold"><i class="bi bi-calendar2-week me-2"></i>Citas</h2>
    <div class="d-flex gap-2">
        <a href="<?= e(app_url('/citas/nueva')) ?>" class="btn btn-primary">
            <i class="bi bi-calendar-plus me-1"></i> Nueva cita
        </a>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <form method="get" action="<?= e(app_url('/citas')) ?>" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Fecha</label>
            <input type="date" class="form-control" name="fecha" value="<?= e($fecha) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="">Todos</option>
                <?php foreach ($estados as $e):
                    $label = match ($e) {
                        'confirmada' => 'Confirmada',
                        'atendida'   => 'Atendida',
                        'cancelada'  => 'Cancelada',
                        default      => 'Pendiente',
                    }; ?>
                    <option value="<?= e($e) ?>" <?= $estado === $e ? 'selected' : '' ?>>
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 d-grid">
            <button class="btn btn-secondary"><i class="bi bi-search"></i> Filtrar</button>
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
                <th>Hora</th>
                <th>Motivo</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($citas) > 0): ?>
                <?php foreach ($citas as $c): ?>
                    <tr>
                        <td>
                            <strong><?= e($c['paciente_nombre']) ?></strong>
                            <small class="text-muted d-block"><?= e($c['paciente_identificacion']) ?></small>
                        </td>
                        <td><?= e(date('d/m/Y', strtotime($c['fecha']))) ?></td>
                        <td><?= e(date('H:i', strtotime($c['hora']))) ?></td>
                        <td><?= e($c['motivo'] ?: '—') ?></td>
                        <td>
                            <?php $badge = match ($c['estado']) {
                                'confirmada' => 'bg-primary',
                                'atendida'   => 'bg-success',
                                'cancelada'  => 'bg-danger',
                                default      => 'bg-secondary',
                            }; ?>
                            <span class="badge <?= $badge ?> text-capitalize"><?= e($c['estado']) ?></span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <?php if ($c['estado'] === 'pendiente'): ?>
                                    <form method="post" action="<?= e(app_url('/citas/estado/' . $c['id'])) ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="estado" value="confirmada">
                                        <button class="btn btn-sm btn-outline-primary" title="Confirmar"><i class="bi bi-check-lg"></i></button>
                                    </form>
                                <?php endif; ?>
                                <?php if (in_array($c['estado'], ['pendiente', 'confirmada'], true)): ?>
                                    <form method="post" action="<?= e(app_url('/citas/estado/' . $c['id'])) ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="estado" value="atendida">
                                        <button class="btn btn-sm btn-outline-success" title="Marcar atendida"><i class="bi bi-check2-circle"></i></button>
                                    </form>
                                    <form method="post" action="<?= e(app_url('/citas/estado/' . $c['id'])) ?>" class="d-inline" onsubmit="return confirm('¿Cancelar esta cita?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="estado" value="cancelada">
                                        <button class="btn btn-sm btn-outline-danger" title="Cancelar"><i class="bi bi-x-lg"></i></button>
                                    </form>
                                <?php endif; ?>
                                <a href="<?= e(app_url('/citas/editar/' . $c['id'])) ?>" class="btn btn-sm btn-outline-secondary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="post" action="<?= e(app_url('/citas/eliminar/' . $c['id'])) ?>" class="d-inline" onsubmit="return confirm('¿Eliminar esta cita?');">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No hay citas para los filtros seleccionados.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
