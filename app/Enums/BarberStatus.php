<?php

declare(strict_types = 1);

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum BarberStatus: int
{
    use HasEnumHelpers;

    case Available             = 1;
    case OnBreak               = 2;
    case Unavailable           = 3;
    case ServingTheCustomer    = 4;
    case WaitingForTheCustomer = 5;

    /**
     * Retorna o label amigável para o status do barbeiro.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Available             => 'Disponível',
            self::OnBreak               => 'Em Pausa',
            self::Unavailable           => 'Indisponível',
            self::ServingTheCustomer    => 'Atendendo o cliente',
            self::WaitingForTheCustomer => 'Aguardando o cliente',
        };
    }

    /**
     * Verifica se o barbeiro está disponível para iniciar um novo atendimento.
     *
     * @return bool
     */
    public function isAvailableForNewService(): bool
    {
        return $this === self::Available || $this === self::WaitingForTheCustomer;
    }

    /**
     * Verifica se o barbeiro está em pausa.
     *
     * @return bool
     */
    public function isOnBreak(): bool
    {
        return $this === self::OnBreak;
    }

    /**
     * Verifica se o barbeiro está aguardando o cliente (livre na cadeira).
     *
     * @return bool
     */
    public function isWaitingForTheCustomer(): bool
    {
        return $this === self::WaitingForTheCustomer;
    }

    /**
     * Verifica se o barbeiro está atendendo um cliente.
     *
     * @return bool
     */
    public function isServingTheCustomer(): bool
    {
        return $this === self::ServingTheCustomer;
    }

    /**
     * Verifica se o barbeiro está indisponível (fora de serviço).
     *
     * @return bool
     */
    public function isUnavailable(): bool
    {
        return $this === self::Unavailable;
    }
}
