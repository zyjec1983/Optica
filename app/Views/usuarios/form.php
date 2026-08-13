<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Views/usuarios/form.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Vista: presentación HTML responsiva (Bootstrap 5).
 * ==================================================================
 */

 /** @var \App\Models\User|null $usuario */ /** @var array $roles */ ?>
<?php
$isEdit = $usuario !== null;
$title = $isEdit ? 'Editar usuario' : 'Nuevo usuario';
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <h2 class="fw-bold"><i class="bi bi-person-<?= $isEdit ? 'gear' : 'plus' ?>-me-2"></i><?= e($title) ?></h2>
    <a href="<?= e(app_url('/usuarios')) ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card card-custom p-4" style="max-width: 620px;">
    <form method="post" action="<?= e(app_url($isEdit ? '/usuarios/editar/' . $usuario->id : '/usuarios')) ?>">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Nombre completo</label>
            <input type="text" class="form-control" name="name" required
                   value="<?= e(old('name', $usuario?->name ?? '')) ?>" placeholder="Ej: María López">
        </div>

        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" name="email" required
                   value="<?= e(old('email', $usuario?->email ?? '')) ?>" placeholder="correo@ejemplo.com">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Rol</label>
                <select class="form-select" name="role" required>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= e($r) ?>" <?= (old('role', $usuario?->role ?? '') === $r) ? 'selected' : '' ?>>
                            <?= match ($r) {
                                'administrador' => 'Administrador',
                                'optometra' => 'Optómetra',
                                default => 'Cajero',
                            } ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Sexo</label>
                <select class="form-select" name="sexo" required>
                    <option value="M" <?= (old('sexo', $usuario?->sexo ?? 'M') === 'M') ? 'selected' : '' ?>>Hombre</option>
                    <option value="F" <?= (old('sexo', $usuario?->sexo ?? 'M') === 'F') ? 'selected' : '' ?>>Mujer</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">
                <?= $isEdit ? 'Nueva contraseña (dejar en blanco para mantener)' : 'Contraseña' ?>
            </label>
            <input type="password" class="form-control" name="password" <?= $isEdit ? '' : 'required' ?> minlength="6">
            <div class="form-text">Mínimo 6 caracteres.</div>
        </div>

        <?php if ($isEdit): ?>
        <div class="mb-3">
            <label class="form-label">Repetir nueva contraseña</label>
            <input type="password" class="form-control" name="password_confirmation" minlength="6">
        </div>
        <?php endif; ?>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Guardar</button>
            <a href="<?= e(app_url('/usuarios')) ?>" class="btn btn-light">Cancelar</a>
        </div>
    </form>
</div>
