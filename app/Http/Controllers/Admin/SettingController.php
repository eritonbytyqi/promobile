<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        if (!empty($settings['company_locations'])) $settings['company_locations'] = json_decode($settings['company_locations'], true);
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except(['_token','section']) as $key => $value) {
            if ($key === 'locations') {
                $locations = collect($value)->map(fn($i) => ['name' => $i['name'] ?? '', 'phone' => $i['phone'] ?? '', 'address_full' => $i['address_full'] ?? '', 'address_short' => $i['address_short'] ?? ''])->filter(fn($i) => !empty($i['name']) || !empty($i['phone']) || !empty($i['address_full']) || !empty($i['address_short']))->values()->toArray();
                Setting::updateOrCreate(['key' => 'company_locations'], ['value' => json_encode($locations, JSON_UNESCAPED_UNICODE)]);
                continue;
            }
            Setting::updateOrCreate(['key' => $key], ['value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value]);
        }
        return back()->with('success', 'Cilësimet u ruajtën!');
    }

public function saveDelivery(Request $request)
{
    $countries = ['kosovo', 'albania', 'macedonia', 'serbia'];
    $fields    = ['free_min', 'cost', 'free_text'];

    foreach ($countries as $country) {
        foreach ($fields as $field) {
            $key = "shipping_{$country}_{$field}";
            Setting::set($key, $request->input($key, ''));
        }
    }

    return back()->with('delivery_success', 'Cilësimet e dërgesës u ruajtën!');
}
}
