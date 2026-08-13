<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Controllers/ExamenController.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Controlador del módulo de exámenes visuales e historial.
 * ==================================================================
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\ExamenService;
use core\Controller;
use core\Request;
use RuntimeException;

/**
 * Controlador de exámenes visuales.
 *
 * Expone el listado/historial, el formulario de registro con firma
 * manuscrita electrónica (Canvas), el detalle y las acciones de
 * edición y eliminación.
 */
final class ExamenController extends Controller
{
    public function __construct(
        private readonly ExamenService $servicio = new ExamenService()
    ) {
    }

    /**
     * Listado de exámenes. Si llega el parámetro `paciente_id` muestra
     * el historial cronológico de ese paciente.
     */
    public function index(Request $request): void
    {
        $pacienteId = (int)$request->get('paciente_id', 0);

        $this->viewWithLayout('layouts/app', 'examenes/index', [
            'examenes'   => $this->servicio->list($pacienteId > 0 ? $pacienteId : null),
            'pacienteId' => $pacienteId,
            'pacientes'  => $this->servicio->pacientesParaSelector(),
        ]);
    }

    /**
     * Formulario de nuevo examen. Acepta `paciente_id` para preseleccionar.
     */
    public function create(Request $request): void
    {
        $this->viewWithLayout('layouts/app', 'examenes/form', [
            'examen'    => new \App\Models\Examen(),
            'esEdicion' => false,
            'pacientes' => $this->servicio->pacientesParaSelector(),
            'pacienteId' => (int)$request->get('paciente_id', 0),
        ]);
    }

    public function store(Request $request): void
    {
        if (!verify_csrf()) {
            session_flash('error', 'Sesión expirada, reintente.');
            redirect(app_url('/examenes'));
        }

        try {
            $user = AuthService::user();
            $userId = ($user['id'] ?? null) !== null ? (int)$user['id'] : null;
            $id = $this->servicio->registrar($request->all(), $userId);
            session_flash('success', 'Examen registrado correctamente.');
            redirect(app_url('/examenes/' . $id));
        } catch (RuntimeException $ex) {
            session_flash('error', $ex->getMessage());
            $_SESSION['_old'] = $request->all();
        }

        redirect(app_url('/examenes/nuevo'));
    }

    /**
     * Detalle del examen (prescripción completa y firma).
     */
    public function show(int $id): void
    {
        $examen = $this->servicio->find($id);
        if ($examen === null) {
            abort(404);
        }
        $this->viewWithLayout('layouts/app', 'examenes/show', [
            'examen' => $examen,
        ]);
    }

    public function edit(int $id): void
    {
        $examen = $this->servicio->find($id);
        if ($examen === null) {
            abort(404);
        }
        $this->viewWithLayout('layouts/app', 'examenes/form', [
            'examen'    => $examen,
            'esEdicion' => true,
            'pacientes' => $this->servicio->pacientesParaSelector(),
            'pacienteId' => (int)$examen->paciente_id,
        ]);
    }

    public function update(Request $request, int $id): void
    {
        if (!verify_csrf()) {
            session_flash('error', 'Sesión expirada, reintente.');
            redirect(app_url('/examenes'));
        }

        try {
            $this->servicio->actualizar($id, $request->all());
            session_flash('success', 'Examen actualizado correctamente.');
            redirect(app_url('/examenes/' . $id));
        } catch (RuntimeException $ex) {
            session_flash('error', $ex->getMessage());
            $_SESSION['_old'] = $request->all();
        }

        redirect(app_url('/examenes/editar/' . $id));
    }

    public function destroy(Request $request, int $id): void
    {
        if (!verify_csrf()) {
            abort(403);
        }
        $this->servicio->eliminar($id);
        session_flash('success', 'Examen eliminado.');
        redirect(app_url('/examenes'));
    }
}
