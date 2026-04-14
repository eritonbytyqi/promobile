<?php

namespace App\Helpers;

class PriceHelper
{
    public static function effectivePrice($model): float
    {
        return ($model->sale_price && $model->sale_price < $model->price)
            ? (float) $model->sale_price
            : (float) $model->price;
    }

    public static function format(float $price, string $currency = '€'): string
    {
        return number_format($price, 2) . ' ' . $currency;
    }
}
