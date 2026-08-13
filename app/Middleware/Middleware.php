<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Middleware/Middleware.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Middleware de Middleware.
 * ==================================================================
 */



declare(strict_types=1);

namespace App\Middleware;

use core\Request;

interface Middleware
{
    public function handle(callable $next): void;
}