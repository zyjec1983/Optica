<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Controllers/PerfilController.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Controlador del perfil de usuario (avatar y contraseña).
 * ==================================================================
 */



declare(strict_types=1);

namespace App\Controllers;

use core\Controller;
use core\Request;
use App\Repositories\UserRepository;
use App\Services\AuthService;

final class PerfilController extends Controller
{
    public function __construct(
        private readonly UserRepository $users = new UserRepository()
    ) {
    }

    public function avatar(Request $request): void
    {
        if ($request->method() !== 'POST' || !verify_csrf()) {
            abort(403);
        }

        $user = AuthService::user();
        if ($user === null) {
            redirect(app_url('/login'));
        }

        $file = $_FILES['avatar'] ?? null;
        if (empty($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            session_flash('error', 'Seleccione una imagen válida.');
            redirect(app_url('/'));
        }

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowed, true) || $file['size'] > 2 * 1024 * 1024) {
            session_flash('error', 'La imagen debe ser JPG/PNG/GIF/WEBP y menor a 2 MB.');
            redirect(app_url('/'));
        }

        $dir = public_path('uploads/avatars');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ext = match ($file['type']) {
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg',
        };
        $name = 'user_' . $user['id'] . '_' . time() . '.' . $ext;
        $dest = $dir . DIRECTORY_SEPARATOR . $name;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            session_flash('error', 'No se pudo guardar la imagen.');
            redirect(app_url('/'));
        }

        $this->users->updateAvatar((int)$user['id'], $name);
        AuthService::refresh((int)$user['id']);
        session_flash('success', 'Foto de perfil actualizada.');
        redirect(app_url('/'));
    }

    public function password(Request $request): void
    {
        if ($request->method() !== 'POST' || !verify_csrf()) {
            abort(403);
        }

        $user = AuthService::user();
        if ($user === null) {
            redirect(app_url('/login'));
        }

        $current = (string)$request->post('current_password');
        $new = (string)$request->post('password');
        $confirm = (string)$request->post('password_confirmation');

        $model = $this->users->findById((int)$user['id']);
        if ($model === null || !password_verify($current, $model->password)) {
            session_flash('error', 'La contraseña actual es incorrecta.');
            redirect(app_url('/'));
        }

        if (mb_strlen($new) < 6) {
            session_flash('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
            redirect(app_url('/'));
        }

        if ($new !== $confirm) {
            session_flash('error', 'Las contraseñas no coinciden.');
            redirect(app_url('/'));
        }

        $this->users->updatePassword((int)$user['id'], password_hash($new, PASSWORD_DEFAULT));
        AuthService::refresh((int)$user['id']);
        session_flash('success', 'Contraseña actualizada correctamente.');
        redirect(app_url('/'));
    }
}
