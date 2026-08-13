<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Views/layouts/app.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Vista (layout): estructura base de las páginas autenticadas.
 * ==================================================================
 */


/** @var string $_content Vista interna a renderizar */
/** @var array $_data Datos de la vista interna */
$user = \App\Services\AuthService::user();
$current = $_SERVER['REQUEST_URI'] ?? '/';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(env('APP_NAME', 'Opticenter')) ?> · Gestión Visual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 260px; }
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f4f6f9; }
        .sidebar {
            width: var(--sidebar-width); height: 100vh; position: fixed; top: 0; left: 0;
            z-index: 1050; background: #212529; color: rgba(255,255,255,.8);
            transition: transform .3s ease; overflow-y: auto;
            padding: 1.2rem .8rem; display: flex; flex-direction: column;
        }
        .sidebar .logo-area {
            display: flex; align-items: center; gap: 10px;
            padding: 0 .5rem 1.2rem; border-bottom: 1px solid #444; margin-bottom: 1.2rem;
        }
        .sidebar .logo-area .brand { font-size: 1.4rem; font-weight: 700; color: #fff; letter-spacing: -.5px; }
        .sidebar .logo-area .brand span { color: #0d6efd; }
        .sidebar .nav-section {
            font-size: .7rem; text-transform: uppercase; letter-spacing: .5px; color: #888;
            margin: 1rem .5rem .3rem;
        }
        .sidebar .nav-link-custom {
            display: flex; align-items: center; gap: 12px; padding: .6rem 1rem;
            border-radius: 8px; color: rgba(255,255,255,.7); text-decoration: none;
            transition: .2s; margin-bottom: 2px; font-weight: 500;
        }
        .sidebar .nav-link-custom:hover, .sidebar .nav-link-custom.active { background: #0d6efd; color: #fff; }
        .sidebar .nav-link-custom i { font-size: 1.2rem; width: 24px; text-align: center; }
        .sidebar .nav-link-custom.disabled { opacity: .45; pointer-events: none; }
        .main-content { margin-left: var(--sidebar-width); padding: 1.5rem; min-height: 100vh; }
        .card-custom {
            border: none; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,.04); background: #fff;
        }
        .card-dash-1 { border-left: 4px solid #0d6efd; }
        .card-dash-2 { border-left: 4px solid #198754; }
        .card-dash-3 { border-left: 4px solid #ffc107; }
        .card-dash-4 { border-left: 4px solid #dc3545; }
        .sidebar-toggle { display: none; background: transparent; border: none; color: #333; font-size: 1.8rem; padding: 0 .5rem; }
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show-sidebar { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .sidebar-toggle { display: inline-block; }
        }
    </style>
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="logo-area">
        <div class="brand"><?= e(env('APP_NAME', 'Opticenter')) ?><span>.</span></div>
    </div>
    <nav>
        <div class="nav-section">General</div>
        <a href="<?= e(app_url('/')) ?>" class="nav-link-custom <?= $current === '/' || str_starts_with($current, '/dashboard') ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <div class="nav-section">Pacientes</div>
        <a href="<?= e(app_url('/pacientes')) ?>" class="nav-link-custom <?= str_starts_with($current, '/pacientes') ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Pacientes
        </a>
        <div class="nav-section">Clínica</div>
        <a href="<?= e(app_url('/examenes')) ?>" class="nav-link-custom <?= str_starts_with($current, '/examenes') ? 'active' : '' ?>">
            <i class="bi bi-eyedropper"></i> Exámenes
        </a>
        <div class="nav-section">Agenda</div>
        <a href="<?= e(app_url('/citas')) ?>" class="nav-link-custom <?= str_starts_with($current, '/citas') ? 'active' : '' ?>">
            <i class="bi bi-calendar2-week"></i> Citas
        </a>
        <div class="nav-section">Ventas</div>
        <a href="#" class="nav-link-custom disabled"><i class="bi bi-receipt-cutoff"></i> Facturar</a>
        <a href="#" class="nav-link-custom disabled"><i class="bi bi-file-earmark-text"></i> Recibos</a>
        <div class="nav-section">Documentos</div>
        <a href="#" class="nav-link-custom disabled"><i class="bi bi-file-pdf"></i> Proformas</a>
        <div class="nav-section">Configuración</div>
        <?php if (($user['role'] ?? '') === 'administrador'): ?>
        <a href="<?= e(app_url('/usuarios')) ?>" class="nav-link-custom <?= str_starts_with($current, '/usuarios') ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i> Usuarios
        </a>
        <?php endif; ?>
        <a href="#" class="nav-link-custom disabled"><i class="bi bi-gear"></i> Configuración</a>
    </nav>
    <div class="mt-auto pt-3 border-top border-secondary">
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
               data-bs-toggle="dropdown" aria-expanded="false" id="userMenu">
                <img src="<?= e(user_avatar($user['avatar'] ?? null, $user['sexo'] ?? null)) ?>"
                     alt="avatar" width="38" height="38" class="rounded-circle me-2"
                     style="object-fit:cover; background:#fff;">
                <div class="lh-sm">
                    <div class="fw-semibold"><?= e($user['name'] ?? '') ?></div>
                    <div class="small text-info text-capitalize"><?= e($user['role'] ?? '') ?></div>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="userMenu">
                <li>
                    <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#perfilModal">
                        <i class="bi bi-person-circle me-2"></i> Perfil
                    </button>
                </li>
                <li>
                    <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#passwordModal">
                        <i class="bi bi-key me-2"></i> Cambiar contraseña
                    </button>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="post" action="<?= e(app_url('/logout')) ?>" id="logoutForm">
                        <?= csrf_field() ?>
                        <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Salir</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</aside>

<main class="main-content" id="mainContent">
    <div class="d-flex align-items-center mb-3 d-lg-none">
        <button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('show-sidebar')">
            <i class="bi bi-list"></i>
        </button>
        <span class="fw-bold ms-2"><?= e(env('APP_NAME', 'Opticenter')) ?></span>
    </div>

    <?php
    // Mensajes de sesión (guardado correcto / errores) se muestran con SweetAlert2.
    $flashSuccess = $_SESSION['_flash']['success'] ?? null;
    $flashError = $_SESSION['_flash']['error'] ?? null;
    $flashFieldErrors = [];
    if (!empty($_SESSION['_errors'])) {
        foreach ($_SESSION['_errors'] as $field => $msgs) {
            foreach ($msgs as $m) {
                $flashFieldErrors[] = $m;
            }
        }
    }
    unset($_SESSION['_flash'], $_SESSION['_errors']);

    if (!empty($_content)) {
        $file = base_path('app/Views/' . $_content . '.php');
        if (is_file($file)) {
            extract($_data, EXTR_SKIP);
            require $file;
        } else {
            echo '<div class="alert alert-warning">Vista no encontrada: ' . e($_content) . '</div>';
        }
    }
    ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Modal Perfil (subir foto) -->
<div class="modal fade" id="perfilModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= e(app_url('/perfil')) ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-circle me-2"></i> Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="<?= e(user_avatar($user['avatar'] ?? null, $user['sexo'] ?? null)) ?>"
                         alt="avatar" width="96" height="96" class="rounded-circle mb-3"
                         style="object-fit:cover; background:#e9ecef;" id="perfilPreview">
                    <div class="mb-3 text-start">
                        <label class="form-label">Foto de perfil</label>
                        <input type="file" class="form-control" name="avatar" accept="image/*" id="perfilFile">
                    </div>
                    <div class="text-start small text-muted">
                        <div><strong>Nombre:</strong> <?= e($user['name'] ?? '') ?></div>
                        <div><strong>Correo:</strong> <?= e($user['email'] ?? '') ?></div>
                        <div><strong>Rol:</strong> <span class="text-capitalize"><?= e($user['role'] ?? '') ?></span></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar foto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cambiar contraseña -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= e(app_url('/perfil/password')) ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-key me-2"></i> Cambiar contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Contraseña actual</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña</label>
                        <input type="password" class="form-control" name="password" minlength="6" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Repetir nueva contraseña</label>
                        <input type="password" class="form-control" name="password_confirmation" minlength="6" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('perfilFile')?.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => document.getElementById('perfilPreview').src = e.target.result;
    reader.readAsDataURL(file);
});
</script>
<script>
    (function () {
        // Escapa texto para mostrarlo de forma segura en ventanas SweetAlert2
        // (el título se renderiza como HTML; se previene cualquier XSS).
        function esc(s) {
            return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // Notificación al guardar (éxito) o ante errores de la operación.
        const success = <?= json_encode($flashSuccess) ?>;
        const error = <?= json_encode($flashError) ?>;
        if (success) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: esc(success),
                showConfirmButton: false, timer: 3000, timerProgressBar: true });
        }
        if (error) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: esc(error),
                showConfirmButton: false, timer: 4500, timerProgressBar: true });
        }

        // Errores de validación de formularios.
        const fieldErrors = <?= json_encode($flashFieldErrors) ?>;
        if (fieldErrors.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Revise el formulario',
                html: '<ul class="text-start ps-3 mb-0">'
                    + fieldErrors.map(function (m) { return '<li>' + esc(m) + '</li>'; }).join('')
                    + '</ul>'
            });
        }

        // Confirmaciones: cualquier formulario con data-confirm pide confirmación.
        document.addEventListener('submit', function (e) {
            const form = e.target;
            const msg = form.getAttribute('data-confirm');
            if (!msg) return;
            e.preventDefault();
            Swal.fire({
                title: '¿Está seguro?',
                text: msg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    })();
</script>
</body>
</html>