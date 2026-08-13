<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Services/AuthService.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Casos de uso y reglas de aplicación de AuthService.
 * ==================================================================
 */



declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;
use core\Session;
use RuntimeException;

final class AuthService
{
    public function __construct(
        private readonly UserRepository $users = new UserRepository()
    ) {
    }

    /**
     * @throws RuntimeException si las credenciales son inválidas
     */
    public function attempt(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email);
        if ($user === null || !password_verify($password, $user->password)) {
            return false;
        }

        Session::regenerate();
        Session::set('user', [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
            'sexo'  => $user->sexo,
            'avatar' => $user->avatar,
        ]);
        return true;
    }

    public static function refresh(int $id): void
    {
        $users = new UserRepository();
        $user = $users->findById($id);
        if ($user === null) {
            return;
        }
        Session::set('user', [
            'id'     => $user->id,
            'name'   => $user->name,
            'email'  => $user->email,
            'role'   => $user->role,
            'sexo'   => $user->sexo,
            'avatar' => $user->avatar,
        ]);
    }

    public function logout(): void
    {
        Session::destroy();
    }

    public static function user(): ?array
    {
        return Session::get('user');
    }
}