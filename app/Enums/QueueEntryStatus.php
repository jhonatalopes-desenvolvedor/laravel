<?php

declare(strict_types = 1);

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum QueueEntryStatus: int
{
    use HasEnumHelpers;

    case Entered  = 1;
    case Canceled = 2;
    case Finished = 3;
    case NoShow   = 4;

    /**
     * Retorna o label amigável para o status de uma entrada na fila.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Entered  => 'Entrou na Fila',
            self::Canceled => 'Cancelado',
            self::Finished => 'Finalizado',
            self::NoShow   => 'Não Compareceu',
        };
    }

    /**
     * Verifica se a entrada na fila está no status 'Entered'.
     *
     * @return bool
     */
    public function isEntered(): bool
    {
        return $this === self::Entered;
    }

    /**
     * Verifica se a entrada na fila está no status 'Canceled'.
     *
     * @return bool
     */
    public function isCanceled(): bool
    {
        return $this === self::Canceled;
    }

    /**
     * Verifica se a entrada na fila está no status 'Finished'.
     *
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this === self::Finished;
    }

    /**
     * Verifica se a entrada na fila está no status 'NoShow'.
     *
     * @return bool
     */
    public function isNoShow(): bool
    {
        return $this === self::NoShow;
    }
}
