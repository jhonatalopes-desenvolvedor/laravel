<?php

declare(strict_types = 1);

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum CompanySaaSStatus: int
{
    use HasEnumHelpers;

    case Active    = 1;
    case Suspended = 2;
    case Cancelled = 3;

    /**
     * Retorna o label amigável para o status de assinatura SaaS da empresa.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Active    => 'Ativo',
            self::Suspended => 'Suspendido',
            self::Cancelled => 'Cancelado',
        };
    }

    /**
     * Verifica se o status SaaS da empresa está ativo.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this === self::Active;
    }

    /**
     * Verifica se o status SaaS da empresa está suspenso.
     *
     * @return bool
     */
    public function isSuspended(): bool
    {
        return $this === self::Suspended;
    }

    /**
     * Verifica se o status SaaS da empresa está cancelado.
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this === self::Cancelled;
    }
}
