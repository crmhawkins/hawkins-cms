<?php

namespace App\Support;

class Tax
{
    public const IVA_RATE = 0.21;

    public static function apply(int $priceInCents): int
    {
        return (int) round($priceInCents * (1 + self::IVA_RATE));
    }

    public static function calculate(int $priceInCents): int
    {
        return (int) round($priceInCents * self::IVA_RATE);
    }

    public static function format(int $cents): string
    {
        return number_format($cents / 100, 2, ',', '.') . ' €';
    }
}
