<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Repositories/CitaRepository.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Acceso y persistencia de CitaRepository en MySQL (PDO).
 * ==================================================================
 */



declare(strict_types=1);

namespace App\Repositories;

use App\Models\Cita;
use core\Database;
use PDO;

final class CitaRepository
{
    public function findAll(?string $fecha = null, ?string $estado = null): array
    {
        $sql = 'SELECT c.*,
                       CONCAT(p.primer_nombre, " ", p.segundo_nombre, " ", p.apellido_paterno, " ", p.apellido_materno) AS paciente_nombre,
                       p.identificacion AS paciente_identificacion,
                       p.telefono AS paciente_telefono
                FROM citas c
                INNER JOIN pacientes p ON p.id = c.paciente_id AND p.deleted_at IS NULL
                WHERE c.deleted_at IS NULL';

        $where = [];
        $params = [];
        if ($fecha !== null && $fecha !== '') {
            $where[] = 'c.fecha = :fecha';
            $params['fecha'] = $fecha;
        }
        if ($estado !== null && $estado !== '' && in_array($estado, Cita::ESTADOS, true)) {
            $where[] = 'c.estado = :estado';
            $params['estado'] = $estado;
        }
        if ($where !== []) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY c.fecha ASC, c.hora ASC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?Cita
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM citas WHERE id = :id AND deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Cita::fromRow($row) : null;
    }

    public function create(Cita $c): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO citas (paciente_id, user_id, fecha, hora, motivo, notas, estado)
             VALUES (:paciente, :user, :fecha, :hora, :motivo, :notas, :estado)'
        );
        $stmt->execute([
            'paciente' => $c->paciente_id,
            'user'     => $c->user_id,
            'fecha'    => $c->fecha,
            'hora'     => $c->hora,
            'motivo'   => $c->motivo,
            'notas'    => $c->notas,
            'estado'   => $c->estado,
        ]);
        return (int)Database::connection()->lastInsertId();
    }

    public function update(Cita $c): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE citas SET paciente_id = :paciente, fecha = :fecha, hora = :hora,
                motivo = :motivo, notas = :notas, estado = :estado
             WHERE id = :id'
        );
        $stmt->execute([
            'paciente' => $c->paciente_id,
            'fecha'    => $c->fecha,
            'hora'     => $c->hora,
            'motivo'   => $c->motivo,
            'notas'    => $c->notas,
            'estado'   => $c->estado,
            'id'       => $c->id,
        ]);
    }

    public function updateEstado(int $id, string $estado): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE citas SET estado = :estado WHERE id = :id'
        );
        $stmt->execute(['estado' => $estado, 'id' => $id]);
    }

    /**
     * Soft-delete: solo marca `deleted_at`; la fila permanece en la base.
     */
    public function softDelete(int $id): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE citas SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL'
        );
        $stmt->execute(['id' => $id]);
    }

    public function countByEstado(string $estado): int
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM citas WHERE estado = :estado AND deleted_at IS NULL'
        );
        $stmt->execute(['estado' => $estado]);
        return (int)$stmt->fetchColumn();
    }

    public function deHoy(): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT c.*,
                    CONCAT(p.primer_nombre, " ", p.segundo_nombre, " ", p.apellido_paterno, " ", p.apellido_materno) AS paciente_nombre
             FROM citas c
             INNER JOIN pacientes p ON p.id = c.paciente_id AND p.deleted_at IS NULL
             WHERE c.fecha = CURDATE() AND c.estado <> "cancelada" AND c.deleted_at IS NULL
             ORDER BY c.hora ASC'
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
