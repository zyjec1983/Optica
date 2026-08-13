<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\PacienteService;
use core\Controller;

final class DashboardController extends Controller
{
    public function __construct(
        private readonly PacienteService $pacientes = new PacienteService()
    ) {
    }

    public function index(): void
    {
        $stats = $this->pacientes->dashboardStats();
        $this->viewWithLayout('layouts/app', 'dashboard/index', $stats);
    }
}