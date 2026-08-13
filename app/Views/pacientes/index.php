<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Views/pacientes/index.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Vista: presentación HTML responsiva (Bootstrap 5).
 * ==================================================================
 */

 /** @var array $pacientes */ /** @var string $busqueda */ ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <h2 class="fw-bold"><i class="bi bi-people-fill me-2"></i>Pacientes</h2>
    <a href="<?= e(app_url('/pacientes/nuevo')) ?>" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Nuevo
    </a>
</div>

<div class="card card-custom p-4">
    <form method="get" action="<?= e(app_url('/pacientes')) ?>" class="row g-2 mb-3">
        <div class="col-md-9">
            <input type="text" name="q" class="form-control" placeholder="Buscar por nombre o cédula..." value="<?= e($busqueda) ?>">
        </div>
        <div class="col-md-3 d-grid">
            <button class="btn btn-secondary"><i class="bi bi-search"></i> Filtrar</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Paciente</th>
                <th>Cédula</th>
                <th>Edad</th>
                <th>Teléfono</th>
                <th>Tutor</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($pacientes) > 0): ?>
                <?php foreach ($pacientes as $p):
                    $nombre = trim($p['primer_nombre'] . ' ' . $p['segundo_nombre'] . ' ' . $p['apellido_paterno'] . ' ' . $p['apellido_materno']);
                    $edad = format_edad($p['fecha_nacimiento']);
                    $mayor = calcular_edad($p['fecha_nacimiento'])['mayor'];
                    ?>
                    <tr>
                        <td><strong><?= e($nombre) ?></strong>
                            <small class="text-muted d-block"><?= e($p['email'] ?: '') ?></small>
                        </td>
                        <td><?= e($p['identificacion']) ?></td>
                        <td>
                            <span class="badge <?= $mayor ? 'bg-secondary' : 'bg-danger' ?>"
                                  title="Nacido: <?= e($p['fecha_nacimiento']) ?>">
                                <?= e($edad) ?>
                            </span>
                        </td>
                        <td><?= e($p['telefono'] ?: '—') ?></td>
                        <td>
                            <?php if (!empty($p['rep_nombres'])): ?>
                                <span class="badge bg-warning text-dark"><?= e($p['rep_nombres']) ?> (<?= e($p['rep_parentesco']) ?>)</span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('/pacientes/editar/' . $p['id'])) ?>" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a class="btn btn-sm btn-outline-info" href="<?= e(app_url('/examenes?paciente_id=' . $p['id'])) ?>" title="Historial de exámenes">
                                <i class="bi bi-clock-history"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="<?= (int)$p['id'] ?>" data-nombre="<?= e($nombre) ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-muted text-center py-4">
                    No se encontraron pacientes<?= $busqueda !== '' ? ' con "' . e($busqueda) . '"' : '' ?>.
                </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (count($pacientes) > 0): ?>
<!-- Modal eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Eliminar paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Seguro que desea eliminar a <strong id="deleteNombre"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="post" id="deleteForm" action="">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        document.getElementById('deleteNombre').textContent = btn.dataset.nombre;
        document.getElementById('deleteForm').action = '<?= e(app_url('/pacientes/eliminar')) ?>/' + btn.dataset.id;
    });
</script>
<?php endif; ?>