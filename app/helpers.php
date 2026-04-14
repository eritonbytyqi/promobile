<?php

if (!function_exists('setting')) {
    function setting(string $key, string $default = ''): string
    {
        return \App\Models\Setting::find($key)?->value ?? $default;
    }
}