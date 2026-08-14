<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Models/Recordatorio.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Modelo / entidad de dominio Recordatorio (sin SQL).
 * ==================================================================
 */

declare(strict_types=1);

namespace App\Models;

/**
 * Recordatorio de aviso al paciente (p. ej. "sus lentes están listos
 * para retiro"), que se muestra en el panel y se notifica por WhatsApp.
 */
final class Recordatorio
{
    public ?int $id = null;
    public ?int $examen_id = null;
    public ?int $paciente_id = null;
    public ?string $mensaje = null;
    public string $estado = 'pendiente';
    public ?string $fecha_recordatorio = null;
    public string $created_at = '';

    public string $paciente_nombre = '';
    public string $paciente_identificacion = '';
    public string $paciente_telefono = '';

    public const PENDIENTE = 'pendiente';
    public const ENVIADO = 'enviado';

    public function estaPendiente(): bool
    {
        return $this->estado === self::PENDIENTE;
    }

    public static function fromRow(array $row): self
    {
        $r = new self();
        $r->id = (int)$row['id'];
        $r->examen_id = $row['examen_id'] !== null ? (int)$row['examen_id'] : null;
        $r->paciente_id = $row['paciente_id'] !== null ? (int)$row['paciente_id'] : null;
        $r->mensaje = $row['mensaje'] ?? null;
        $r->estado = $row['estado'];
        $r->fecha_recordatorio = $row['fecha_recordatorio'] ?? null;
        $r->created_at = $row['created_at'] ?? '';
        $r->paciente_nombre = $row['paciente_nombre'] ?? '';
        $r->paciente_identificacion = $row['paciente_identificacion'] ?? '';
        $r->paciente_telefono = $row['paciente_telefono'] ?? '';
        return $r;
    }
}
