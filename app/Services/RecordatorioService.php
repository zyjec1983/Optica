<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Services/RecordatorioService.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Casos de uso y reglas de aplicación de Recordatorio.
 * ==================================================================
 */

declare(strict_types=1);

namespace App\Services;

use App\Models\Recordatorio;
use App\Repositories\PacienteRepository;
use App\Repositories\RecordatorioRepository;
use RuntimeException;

/**
 * Recordatorios de aviso al paciente (lentes listos para retiro).
 * El mensaje se configura en .env (WHATSAPP_MENSAJE_LENTES_LISTOS).
 */
final class RecordatorioService
{
    public function __construct(
        private readonly RecordatorioRepository $recordatorios = new RecordatorioRepository(),
        private readonly PacienteRepository $pacientes = new PacienteRepository()
    ) {
    }

    /**
     * @return Recordatorio[]
     */
    public function pendientes(int $limit = 20): array
    {
        return $this->recordatorios->pendientes($limit);
    }

    public function find(int $id): ?Recordatorio
    {
        return $this->recordatorios->findById($id);
    }

    /**
     * Crea el recordatorio de "lentes listos" al registrar un examen.
     * El mensaje por defecto se define en .env y admite {nombre} como
     * variable que se reemplaza por el nombre del paciente.
     *
     * @throws RuntimeException si el paciente no existe
     */
    public function crearParaExamen(int $pacienteId, int $examenId): int
    {
        $paciente = $this->pacientes->findById($pacienteId);
        if ($paciente === null) {
            throw new RuntimeException('No se pudo crear el recordatorio: paciente no encontrado.');
        }

        $mensaje = (string)env('WHATSAPP_MENSAJE_LENTES_LISTOS', '');
        if ($mensaje === '') {
            $mensaje = 'Estimado/a {nombre}, sus lentes ya están listos para retiro en nuestra óptica. ¡Gracias por su preferencia!';
        }
        $mensaje = str_replace('{nombre}', $paciente->nombreCompleto(), $mensaje);

        return $this->recordatorios->crear($pacienteId, $examenId, $mensaje, date('Y-m-d'));
    }

    public function marcarEnviado(int $id): void
    {
        $this->recordatorios->marcarEnviado($id);
    }

    public function totalPendientes(): int
    {
        return $this->recordatorios->countPendientes();
    }
}
