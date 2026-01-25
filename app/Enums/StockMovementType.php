<?php

namespace App\Enums;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

enum StockMovementType: string implements Castable
{
    case ENTREE = 'ENTREE';
    case SORTIE = 'SORTIE';

    /**
     * Return an array of values for validation rules
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn(self $c) => $c->value, self::cases());
    }

    /**
     * Provide a custom cast that stores the backed value and returns enum instances
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new \App\Casts\EnumCast(self::class);
    }
}
