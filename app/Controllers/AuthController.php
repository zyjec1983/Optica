<?php

declare(strict_types=1);

namespace App\Controllers;

use core\Controller;
use core\Request;
use App\Services\AuthService;

final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $auth = new AuthService()
    ) {
    }

    public function showLogin(): void
    {
        if (AuthService::user() !== null) {
            redirect(app_url('/'));
        }
        $this->view('auth/login');
    }

    public function login(Request $request): void
    {
        if ($request->method() !== 'POST') {
            redirect(app_url('/login'));
        }

        if (!verify_csrf()) {
            session_flash('error', 'Sesión expirada, reintente.');
            redirect(app_url('/login'));
        }

        $email = trim((string)$request->post('email'));
        $password = (string)$request->post('password');

        if ($email === '' || $password === '') {
            session_flash('error', 'Ingrese su correo y contraseña.');
            redirect(app_url('/login'));
        }

        if ($this->auth->attempt($email, $password)) {
            redirect(app_url('/'));
        }

        session_flash('error', 'Credenciales incorrectas.');
        redirect(app_url('/login'));
    }

    public function logout(Request $request): void
    {
        if (!verify_csrf()) {
            abort(403);
        }
        $this->auth->logout();
        redirect(app_url('/login'));
    }
}