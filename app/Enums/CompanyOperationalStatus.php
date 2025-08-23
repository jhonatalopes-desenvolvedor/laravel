<?php

declare(strict_types = 1);

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum CompanyOperationalStatus: int
{
    use HasEnumHelpers;

    case Open   = 1;
    case Paused = 2;
    case Closed = 3;

    /**
     * Retorna o label amigável para o status operacional da empresa (aberto/fechado para clientes).
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Open   => 'Aberto',
            self::Paused => 'Pausado',
            self::Closed => 'Fechado',
        };
    }

    /**
     * Verifica se a empresa está aberta para operações.
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this === self::Open;
    }

    /**
     * Verifica se a empresa está pausada em suas operações.
     *
     * @return bool
     */
    public function isPaused(): bool
    {
        return $this === self::Paused;
    }

    /**
     * Verifica se a empresa está fechada para operações.
     *
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this === self::Closed;
    }
}
