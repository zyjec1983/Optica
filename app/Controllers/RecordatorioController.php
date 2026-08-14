<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Controllers/RecordatorioController.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Controlador del módulo de recordatorios.
 * ==================================================================
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Services\RecordatorioService;
use core\Controller;
use core\Request;
use RuntimeException;

/**
 * Controlador de recordatorios de aviso (lentes listos vía WhatsApp).
 */
final class RecordatorioController extends Controller
{
    public function __construct(
        private readonly RecordatorioService $servicio = new RecordatorioService()
    ) {
    }

    /**
     * Marca un recordatorio como notificado (sale del panel).
     */
    public function marcar(Request $request, int $id): void
    {
        if (!verify_csrf()) {
            abort(403);
        }
        try {
            $this->servicio->marcarEnviado($id);
            session_flash('success', 'Recordatorio marcado como notificado.');
        } catch (RuntimeException $ex) {
            session_flash('error', $ex->getMessage());
        }
        redirect(app_url('/'));
    }
}
