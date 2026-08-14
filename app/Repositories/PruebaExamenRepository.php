<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Repositories/PruebaExamenRepository.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Acceso y persistencia de PruebaExamen en MySQL (PDO).
 * ==================================================================
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PruebaExamen;
use core\Database;
use PDO;

/**
 * Repositorio de pruebas complementarias de la consulta.
 * Una prueba por fila; se indexan por clave (agudeza_visual, etc.).
 */
final class PruebaExamenRepository
{
    /**
     * @return array<string, PruebaExamen> Pruebas del examen indexadas por clave.
     */
    public function porExamen(int $examenId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM pruebas_examen WHERE examen_id = :examen AND deleted_at IS NULL'
        );
        $stmt->execute(['examen' => $examenId]);
        $result = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[$row['prueba']] = PruebaExamen::fromRow($row);
        }
        return $result;
    }

    /**
     * Reemplaza las pruebas de un examen: elimina las filas previas y
     * guarda las recibidas (se envían solo las que tienen algún valor).
     */
    public function reemplazar(int $examenId, array $pruebas): void
    {
        $conn = Database::connection();
        $conn->prepare('DELETE FROM pruebas_examen WHERE examen_id = :examen')->execute(['examen' => $examenId]);

        $insert = $conn->prepare(
            'INSERT INTO pruebas_examen (examen_id, prueba, od, os, resultado, normal)
             VALUES (:examen, :prueba, :od, :os, :resultado, :normal)'
        );
        foreach ($pruebas as $clave => $p) {
            $insert->execute([
                'examen'    => $examenId,
                'prueba'    => $clave,
                'od'        => $p['od'] ?? null,
                'os'        => $p['os'] ?? null,
                'resultado' => $p['resultado'] ?? null,
                'normal'    => $p['normal'] ?? null,
            ]);
        }
    }
}
