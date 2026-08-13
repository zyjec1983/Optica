<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Middleware/RoleMiddleware.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Middleware de RoleMiddleware.
 * ==================================================================
 */



declare(strict_types=1);

namespace App\Middleware;

final class RoleMiddleware implements Middleware
{
    private array $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public function handle(callable $next): void
    {
        $role = $_SESSION['user']['role'] ?? null;
        if ($role === null || !in_array($role, $this->roles, true)) {
            abort(403);
        }
        $next();
    }
}