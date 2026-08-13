<?php

declare(strict_types=1);

namespace App\Controllers;

use core\Controller;
use core\Request;
use App\Repositories\UserRepository;
use App\Services\AuthService;

final class UserController extends Controller
{
    public function __construct(
        private readonly UserRepository $users = new UserRepository()
    ) {
    }

    private function requireAdmin(): array
    {
        $user = AuthService::user();
        if (($user['role'] ?? '') !== 'administrador') {
            abort(403);
        }
        return $user;
    }

    public function index(Request $request): void
    {
        $this->requireAdmin();
        $users = $this->users->findAll();
        $this->viewWithLayout('layouts/app', 'usuarios/index', [
            'usuarios' => $users,
        ]);
    }

    public function create(Request $request): void
    {
        $this->requireAdmin();
        $this->viewWithLayout('layouts/app', 'usuarios/form', [
            'usuario' => null,
            'roles' => \App\Models\User::ROLES,
        ]);
    }

    public function store(Request $request): void
    {
        $this->requireAdmin();
        if (!verify_csrf()) {
            abort(403);
        }

        $name = trim((string)$request->post('name'));
        $email = trim((string)$request->post('email'));
        $password = (string)$request->post('password');
        $role = (string)$request->post('role');
        $sexo = strtoupper((string)$request->post('sexo')) === 'F' ? 'F' : 'M';

        $errors = [];
        if ($name === '') {
            $errors['name'][] = 'El nombre es obligatorio.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Correo inválido.';
        }
        if (mb_strlen($password) < 6) {
            $errors['password'][] = 'La contraseña debe tener al menos 6 caracteres.';
        }
        if (!in_array($role, \App\Models\User::ROLES, true)) {
            $errors['role'][] = 'Rol inválido.';
        }
        if ($this->users->findByEmail($email) !== null) {
            $errors['email'][] = 'Ya existe un usuario con ese correo.';
        }

        if ($errors !== []) {
            $this->fail($errors);
        }

        $this->users->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'sexo' => $sexo,
            'avatar' => null,
        ]);

        session_flash('success', 'Usuario creado correctamente.');
        redirect(app_url('/usuarios'));
    }

    public function edit(Request $request, int $id): void
    {
        $this->requireAdmin();
        $usuario = $this->users->findById($id);
        if ($usuario === null) {
            abort(404);
        }
        $this->viewWithLayout('layouts/app', 'usuarios/form', [
            'usuario' => $usuario,
            'roles' => \App\Models\User::ROLES,
        ]);
    }

    public function update(Request $request, int $id): void
    {
        $this->requireAdmin();
        if (!verify_csrf()) {
            abort(403);
        }

        $usuario = $this->users->findById($id);
        if ($usuario === null) {
            abort(404);
        }

        $name = trim((string)$request->post('name'));
        $email = trim((string)$request->post('email'));
        $role = (string)$request->post('role');
        $sexo = strtoupper((string)$request->post('sexo')) === 'F' ? 'F' : 'M';
        $password = (string)$request->post('password');

        $errors = [];
        if ($name === '') {
            $errors['name'][] = 'El nombre es obligatorio.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Correo inválido.';
        }
        if (!in_array($role, \App\Models\User::ROLES, true)) {
            $errors['role'][] = 'Rol inválido.';
        }
        $existing = $this->users->findByEmail($email);
        if ($existing !== null && $existing->id !== $id) {
            $errors['email'][] = 'Ya existe otro usuario con ese correo.';
        }

        $data = [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'sexo' => $sexo,
            'avatar' => $usuario->avatar,
        ];

        if ($password !== '') {
            if (mb_strlen($password) < 6) {
                $errors['password'][] = 'La contraseña debe tener al menos 6 caracteres.';
            } elseif ($password !== (string)$request->post('password_confirmation')) {
                $errors['password'][] = 'Las contraseñas no coinciden.';
            } else {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
        }

        if ($errors !== []) {
            $this->fail($errors);
        }

        $this->users->update($data);
        session_flash('success', 'Usuario actualizado correctamente.');
        redirect(app_url('/usuarios'));
    }

    public function destroy(Request $request, int $id): void
    {
        $this->requireAdmin();
        if (!verify_csrf()) {
            abort(403);
        }

        $me = AuthService::user();
        if (($me['id'] ?? 0) === $id) {
            session_flash('error', 'No puede eliminarse a sí mismo.');
            redirect(app_url('/usuarios'));
        }

        $this->users->delete($id);
        session_flash('success', 'Usuario eliminado correctamente.');
        redirect(app_url('/usuarios'));
    }
}
