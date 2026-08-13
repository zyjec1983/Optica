<?php

declare(strict_types=1);

namespace App\Middleware;

use core\Request;

interface Middleware
{
    public function handle(callable $next): void;
}