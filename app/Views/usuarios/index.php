<?php /** @var array $usuarios */ ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <h2 class="fw-bold"><i class="bi bi-people-fill me-2"></i>Usuarios</h2>
    <a href="<?= e(app_url('/usuarios/nuevo')) ?>" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Nuevo usuario
    </a>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Sexo</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($usuarios) > 0): ?>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?= e(user_avatar($u['avatar'] ?? null, $u['sexo'] ?? null)) ?>"
                                     alt="avatar" width="36" height="36" class="rounded-circle"
                                     style="object-fit:cover; background:#e9ecef;">
                                <strong><?= e($u['name']) ?></strong>
                            </div>
                        </td>
                        <td><?= e($u['email']) ?></td>
                        <td><span class="badge bg-info text-dark text-capitalize"><?= e($u['role']) ?></span></td>
                        <td><?= $u['sexo'] === 'F' ? 'Mujer' : 'Hombre' ?></td>
                        <td>
                            <a href="<?= e(app_url('/usuarios/editar/' . $u['id'])) ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <?php if (($u['id'] ?? 0) !== (\App\Services\AuthService::user()['id'] ?? 0)): ?>
                            <form method="post" action="<?= e(app_url('/usuarios/eliminar/' . $u['id'])) ?>" class="d-inline" onsubmit="return confirm('¿Eliminar este usuario?');">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center text-muted">No hay usuarios registrados.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
