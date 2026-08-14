<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Controllers/DashboardController.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Controlador del panel principal (dashboard).
 * ==================================================================
 */



declare(strict_types=1);

namespace App\Controllers;

use App\Services\PacienteService;
use App\Services\RecordatorioService;
use core\Controller;

final class DashboardController extends Controller
{
    public function __construct(
        private readonly PacienteService $pacientes = new PacienteService(),
        private readonly RecordatorioService $recordatorios = new RecordatorioService()
    ) {
    }

    public function index(): void
    {
        $stats = $this->pacientes->dashboardStats();
        $stats['recordatorios_pendientes'] = $this->recordatorios->pendientes(20);
        $this->viewWithLayout('layouts/app', 'dashboard/index', $stats);
    }
}