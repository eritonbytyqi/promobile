<?php

namespace App\Helpers;

use App\Models\Setting;
use Stevebauman\Location\Facades\Location;

class ShippingHelper
{
    public static function getCountryKey(): string
    {
        $ip       = request()->ip();
        $position = Location::get($ip);
        $code     = strtolower($position?->countryCode ?? 'xk');

        return match($code) {
            'al'    => 'albania',
            'mk'    => 'macedonia',
            'rs'    => 'serbia',
            default => 'kosovo', // XK = Kosovë, ose çdo IP tjetër
        };
    }

    public static function getShipping(): array
    {
        $country  = self::getCountryKey();
        $freeMin  = (float) Setting::get("shipping_{$country}_free_min", 100);
        $cost     = (float) Setting::get("shipping_{$country}_cost", 2);
        $freeText = Setting::get("shipping_{$country}_free_text", 'Dërgesa Falas');

        return [
            'country'  => $country,
            'free_min' => $freeMin,
            'cost'     => $cost,
            'free_text'=> $freeText,
        ];
    }
}