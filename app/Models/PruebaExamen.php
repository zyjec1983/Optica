<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Models/PruebaExamen.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Modelo / entidad de dominio PruebaExamen (sin SQL).
 * ==================================================================
 */

declare(strict_types=1);

namespace App\Models;

/**
 * Prueba complementaria de una consulta/examen visual (agudeza, visión
 * de cerca/lejos, binocular, movimientos oculares, color, contraste,
 * presión intraocular, campo visual).
 *
 * La refracción y el astigmatismo se guardan en las columnas del examen
 * (od_esfera/os_esfera, cilindro y eje); las demás pruebas van en esta
 * tabla normalizada (1 examen -> N pruebas).
 */
final class PruebaExamen
{
    public ?int $id = null;
    public ?int $examen_id = null;
    public string $prueba = '';
    public ?string $od = null;
    public ?string $os = null;
    public ?string $resultado = null;
    public ?bool $normal = null;

    /**
     * Catálogo de pruebas. `tipo`:
     *  - 'ojos'   : se captura un valor por ojo (od/os).
     *  - 'simple' : un solo resultado + estado normal/anormal.
     */
    public const PRUEBAS = [
        'agudeza_visual' => [
            'label' => 'Agudeza visual',
            'tipo'  => 'ojos',
            'placeholder' => '20/20',
        ],
        'vision_cercana' => [
            'label' => 'Visión cercana',
            'tipo'  => 'simple',
            'placeholder' => 'N6 / OK',
        ],
        'vision_lejana' => [
            'label' => 'Visión lejana',
            'tipo'  => 'simple',
            'placeholder' => '20/20',
        ],
        'vision_binocular' => [
            'label' => 'Visión binocular',
            'tipo'  => 'simple',
            'placeholder' => 'Normal / Estereopsis',
        ],
        'movimientos_oculares' => [
            'label' => 'Movimientos oculares',
            'tipo'  => 'simple',
            'placeholder' => 'Normal / Alterados',
        ],
        'vision_color' => [
            'label' => 'Visión del color',
            'tipo'  => 'simple',
            'placeholder' => 'Normal / Daltonismo',
        ],
        'sensibilidad_contraste' => [
            'label' => 'Sensibilidad al contraste',
            'tipo'  => 'simple',
            'placeholder' => '1:10',
        ],
        'presion_intraocular' => [
            'label' => 'Presión intraocular',
            'tipo'  => 'ojos',
            'placeholder' => '15',
        ],
        'campo_visual' => [
            'label' => 'Campo visual',
            'tipo'  => 'simple',
            'placeholder' => 'Normal / Restringido',
        ],
    ];

    public function tipo(): string
    {
        return self::PRUEBAS[$this->prueba]['tipo'] ?? 'simple';
    }

    public function etiqueta(): string
    {
        return self::PRUEBAS[$this->prueba]['label'] ?? $this->prueba;
    }

    public function normalLabel(): string
    {
        if ($this->normal === null) {
            return '—';
        }
        return $this->normal ? 'Normal' : 'Anormal';
    }

    public static function fromRow(array $row): self
    {
        $p = new self();
        $p->id = (int)$row['id'];
        $p->examen_id = $row['examen_id'] !== null ? (int)$row['examen_id'] : null;
        $p->prueba = $row['prueba'];
        $p->od = $row['od'] ?? null;
        $p->os = $row['os'] ?? null;
        $p->resultado = $row['resultado'] ?? null;
        $p->normal = $row['normal'] === null ? null : (bool)$row['normal'];
        return $p;
    }
}
