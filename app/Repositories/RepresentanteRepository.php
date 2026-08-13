<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Repositories/RepresentanteRepository.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Acceso y persistencia de RepresentanteRepository en MySQL (PDO).
 * ==================================================================
 */



declare(strict_types=1);

namespace App\Repositories;

use core\Database;
use PDO;

final class RepresentanteRepository
{
    public function create(string $parentesco, string $nombres, string $cedula, string $telefono): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO representantes (parentesco, nombres, cedula, telefono)
             VALUES (:parentesco, :nombres, :cedula, :telefono)'
        );
        $stmt->execute([
            'parentesco' => $parentesco,
            'nombres'    => $nombres,
            'cedula'     => $cedula,
            'telefono'   => $telefono,
        ]);
        return (int)Database::connection()->lastInsertId();
    }

    public function findByCedula(string $cedula): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM representantes WHERE cedula = :cedula AND deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute(['cedula' => $cedula]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function update(int $id, string $parentesco, string $nombres, string $cedula, string $telefono): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE representantes SET parentesco = :parentesco, nombres = :nombres,
                cedula = :cedula, telefono = :telefono WHERE id = :id'
        );
        $stmt->execute([
            'parentesco' => $parentesco,
            'nombres'    => $nombres,
            'cedula'     => $cedula,
            'telefono'   => $telefono,
            'id'         => $id,
        ]);
    }

    public function all(): array
    {
        return Database::connection()
            ->query('SELECT * FROM representantes WHERE deleted_at IS NULL ORDER BY nombres')
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}