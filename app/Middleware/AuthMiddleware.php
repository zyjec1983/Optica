<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Middleware/AuthMiddleware.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Middleware de AuthMiddleware.
 * ==================================================================
 */



declare(strict_types=1);

namespace App\Middleware;

final class AuthMiddleware implements Middleware
{
    public function handle(callable $next): void
    {
        if (empty($_SESSION['user'])) {
            redirect(app_url('/login'));
        }
        $next();
    }
}