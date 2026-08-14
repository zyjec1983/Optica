<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Repositories/RecordatorioRepository.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Acceso y persistencia de Recordatorio en MySQL (PDO).
 * ==================================================================
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Recordatorio;
use core\Database;
use PDO;

/**
 * Repositorio de recordatorios de aviso (lentes listos para retiro).
 * Los listados unen los datos del paciente (nombre, teléfono) mediante JOIN.
 */
final class RecordatorioRepository
{
    /**
     * Recordatorios pendientes de notificar, ordenados por fecha.
     */
    public function pendientes(int $limit = 20): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT r.*,
                    CONCAT(p.primer_nombre, " ", p.segundo_nombre, " ", p.apellido_paterno, " ", p.apellido_materno) AS paciente_nombre,
                    p.identificacion AS paciente_identificacion,
                    p.telefono AS paciente_telefono
             FROM recordatorios r
             INNER JOIN pacientes p ON p.id = r.paciente_id AND p.deleted_at IS NULL
             WHERE r.deleted_at IS NULL AND r.estado = :estado
             ORDER BY r.fecha_recordatorio ASC, r.id ASC
             LIMIT :lim'
        );
        $stmt->bindValue(':estado', Recordatorio::PENDIENTE);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return array_map(
            static fn(array $row): Recordatorio => Recordatorio::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function findById(int $id): ?Recordatorio
    {
        $stmt = Database::connection()->prepare(
            'SELECT r.*,
                    CONCAT(p.primer_nombre, " ", p.segundo_nombre, " ", p.apellido_paterno, " ", p.apellido_materno) AS paciente_nombre,
                    p.identificacion AS paciente_identificacion,
                    p.telefono AS paciente_telefono
             FROM recordatorios r
             INNER JOIN pacientes p ON p.id = r.paciente_id AND p.deleted_at IS NULL
             WHERE r.id = :id AND r.deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Recordatorio::fromRow($row) : null;
    }

    /**
     * Crea un recordatorio pendiente para un paciente y examen.
     */
    public function crear(int $pacienteId, ?int $examenId, string $mensaje, string $fechaRecordatorio): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO recordatorios (examen_id, paciente_id, mensaje, estado, fecha_recordatorio)
             VALUES (:examen, :paciente, :mensaje, "pendiente", :fecha)'
        );
        $stmt->execute([
            'examen'   => $examenId,
            'paciente' => $pacienteId,
            'mensaje'  => $mensaje,
            'fecha'    => $fechaRecordatorio,
        ]);
        return (int)Database::connection()->lastInsertId();
    }

    /**
     * Marca un recordatorio como notificado (sale del panel).
     */
    public function marcarEnviado(int $id): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE recordatorios SET estado = "enviado"
             WHERE id = :id AND deleted_at IS NULL'
        );
        $stmt->execute(['id' => $id]);
    }

    public function existeParaExamen(int $examenId): bool
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM recordatorios
             WHERE examen_id = :examen AND deleted_at IS NULL'
        );
        $stmt->execute(['examen' => $examenId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function countPendientes(): int
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM recordatorios WHERE deleted_at IS NULL AND estado = :estado'
        );
        $stmt->execute(['estado' => Recordatorio::PENDIENTE]);
        return (int)$stmt->fetchColumn();
    }
}
