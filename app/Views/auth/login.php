<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Views/auth/login.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Vista: presentación HTML responsiva (Bootstrap 5).
 * ==================================================================
 */

 $appName = env('APP_NAME', 'Opticenter'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · <?= e($appName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: linear-gradient(135deg,#212529 0%,#0d6efd 100%); min-height: 100vh; }
        .login-card { max-width: 400px; border-radius: 20px; }
    </style>
</head>
<body>
<div class="d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="card login-card shadow-lg w-100 mx-3">
        <div class="card-body p-4 p-md-5">
            <h1 class="h3 fw-bold mb-1 text-center"><?= e($appName) ?><span style="color:#0d6efd">.</span></h1>
            <p class="text-muted text-center mb-4">Sistema de Gestión para Óptica</p>

            <form method="post" action="<?= e(app_url('/login')) ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control form-control-lg"
                           value="<?= e(old('email')) ?>" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control form-control-lg" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
                </button>
            </form>
        </div>
    </div>
</div>
<?php $loginError = $_SESSION['_flash']['error'] ?? null;
unset($_SESSION['_flash']['error']); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const loginError = <?= json_encode($loginError) ?>;
    if (loginError) {
        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: loginError,
            showConfirmButton: false, timer: 4500, timerProgressBar: true });
    }
</script>
</body>
</html>