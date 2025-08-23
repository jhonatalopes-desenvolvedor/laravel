<?php

declare(strict_types = 1);

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum CustomerRelationshipType: int
{
    use HasEnumHelpers;

    case Self     = 1;
    case Son      = 2;
    case Daughter = 3;
    case Brother  = 4;
    case Sister   = 5;
    case Father   = 6;
    case Mother   = 7;
    case Nephew   = 8;
    case Niece    = 9;
    case Friend   = 10;
    case Spouse   = 11;
    case Other    = 99;

    /**
     * Retorna o label amigável para o tipo de relacionamento do cliente com o titular da conta.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Self     => 'Titular da Conta',
            self::Son      => 'Filho',
            self::Daughter => 'Filha',
            self::Brother  => 'Irmão',
            self::Sister   => 'Irmã',
            self::Father   => 'Pai',
            self::Mother   => 'Mãe',
            self::Nephew   => 'Sobrinho',
            self::Niece    => 'Sobrinha',
            self::Friend   => 'Amigo(a)',
            self::Spouse   => 'Cônjuge',
            self::Other    => 'Outro',
        };
    }

    /**
     * Verifica se o relacionamento é o próprio titular da conta.
     *
     * @return bool
     */
    public function isSelf(): bool
    {
        return $this === self::Self;
    }
}
