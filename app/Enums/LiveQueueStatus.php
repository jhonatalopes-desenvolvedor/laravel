<?php

declare(strict_types = 1);

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum LiveQueueStatus: int
{
    use HasEnumHelpers;

    case InQueue     = 1;
    case BeingCalled = 2;
    case InService   = 3;

    /**
     * Retorna o label amigável para o status da fila em tempo real.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::InQueue     => 'Na fila',
            self::BeingCalled => 'Sendo chamado',
            self::InService   => 'Em atendimento',
        };
    }

    /**
     * Verifica se o cliente está na fila.
     *
     * @return bool
     */
    public function isInQueue(): bool
    {
        return $this === self::InQueue;
    }

    /**
     * Verifica se o cliente está sendo chamado para atendimento.
     *
     * @return bool
     */
    public function isBeingCalled(): bool
    {
        return $this === self::BeingCalled;
    }

    /**
     * Verifica se o cliente está em atendimento.
     *
     * @return bool
     */
    public function isInService(): bool
    {
        return $this === self::InService;
    }
}
