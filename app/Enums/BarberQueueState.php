<?php

declare(strict_types = 1);

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum BarberQueueState: int
{
    use HasEnumHelpers;

    case Open   = 1;
    case Paused = 2;
    case Closed = 3;

    /**
     * Retorna o label amigável para o estado da fila do barbeiro.
     *
     * @return string
     */
    private function label(): string
    {
        return match ($this) {
            self::Open   => 'Aberta',
            self::Paused => 'Pausada',
            self::Closed => 'Fechada',
        };
    }

    /**
     * Verifica se a fila do barbeiro está aberta.
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this === self::Open;
    }

    /**
     * Verifica se a fila do barbeiro está pausada.
     *
     * @return bool
     */
    public function isPaused(): bool
    {
        return $this === self::Paused;
    }

    /**
     * Verifica se a fila do barbeiro está fechada.
     *
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this === self::Closed;
    }
}
