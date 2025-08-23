<?php

declare(strict_types = 1);

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum CustomerArrivalStatus: int
{
    use HasEnumHelpers;

    case OnSite    = 1;
    case OffSite   = 2;
    case InTransit = 3;

    /**
     * Retorna o label amigável para o status de chegada do cliente.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::OnSite    => 'No local',
            self::OffSite   => 'Fora do local',
            self::InTransit => 'A caminho',
        };
    }

    /**
     * Verifica se o cliente está no local da barbearia.
     *
     * @return bool
     */
    public function isOnSite(): bool
    {
        return $this === self::OnSite;
    }

    /**
     * Verifica se o cliente está fora do local da barbearia.
     *
     * @return bool
     */
    public function isOffSite(): bool
    {
        return $this === self::OffSite;
    }

    /**
     * Verifica se o cliente está a caminho da barbearia.
     *
     * @return bool
     */
    public function isInTransit(): bool
    {
        return $this === self::InTransit;
    }
}
