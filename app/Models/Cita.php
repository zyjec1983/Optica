<?php

declare(strict_types=1);

namespace App\Models;

final class Cita
{
    public ?int $id = null;
    public ?int $paciente_id = null;
    public ?int $user_id = null;
    public ?string $fecha = null;
    public string $hora = '08:00';
    public string $motivo = '';
    public ?string $notas = null;
    public string $estado = 'pendiente';
    public string $created_at = '';

    public const ESTADOS = ['pendiente', 'confirmada', 'atendida', 'cancelada'];

    public function estadoLabel(): string
    {
        return match ($this->estado) {
            'confirmada' => 'Confirmada',
            'atendida'   => 'Atendida',
            'cancelada'  => 'Cancelada',
            default      => 'Pendiente',
        };
    }

    public function estadoBadge(): string
    {
        return match ($this->estado) {
            'confirmada' => 'bg-primary',
            'atendida'   => 'bg-success',
            'cancelada'  => 'bg-danger',
            default      => 'bg-secondary',
        };
    }

    public static function fromRow(array $row): self
    {
        $c = new self();
        $c->id = (int)$row['id'];
        $c->paciente_id = $row['paciente_id'] !== null ? (int)$row['paciente_id'] : null;
        $c->user_id = $row['user_id'] !== null ? (int)$row['user_id'] : null;
        $c->fecha = $row['fecha'];
        $c->hora = $row['hora'];
        $c->motivo = $row['motivo'] ?? '';
        $c->notas = $row['notas'] ?? null;
        $c->estado = $row['estado'];
        $c->created_at = $row['created_at'] ?? '';
        return $c;
    }
}
