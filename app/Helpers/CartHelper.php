<?php

namespace App\Helpers;

class CartHelper
{
    public static function buildJsonResponse(array $cart, string $htmlView): array
    {
        $total = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        return ['count' => count($cart), 'total' => number_format($total, 2), 'html' => $htmlView];
    }
}
