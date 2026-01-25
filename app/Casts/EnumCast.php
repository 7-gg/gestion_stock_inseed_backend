<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

/**
 * Generic cast for backed enums.
 * Usage: implement Castable on your enum and return new EnumCast(YourEnum::class) from castUsing().
 */
class EnumCast implements CastsAttributes
{
    protected string $enumClass;

    public function __construct(string $enumClass)
    {
        if (!enum_exists($enumClass)) {
            throw new InvalidArgumentException("Provided class [{$enumClass}] is not an enum.");
        }

        $this->enumClass = $enumClass;
    }

    /**
     * Cast the raw database value to an enum instance (or null).
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        return $this->enumClass::from($value);
    }

    /**
     * Prepare the value to be stored in the database.
     * Accepts enum instances or raw backed values.
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        // Backed enum instance
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        // Otherwise assume it's a string/number valid for the enum; let DB/validation handle correctness
        return $value;
    }
}
