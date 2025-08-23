<?php

declare(strict_types = 1);

namespace App\Traits;

trait HasEnumHelpers
{
    /**
     * Retorna um array de todos os casos do enum com seus labels e valores.
     *
     * @return array<int, array{label: string, value: int}>
     */
    public static function toArray(): array
    {
        $array = [];

        foreach (self::cases() as $case) {
            $array[] = [
                'label' => $case->label(),
                'value' => $case->value,
            ];
        }

        return $array;
    }
}
