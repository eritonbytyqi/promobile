<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up(): void
{
    $countries = ['kosovo', 'albania', 'macedonia', 'serbia'];
    $defaults  = [
        'kosovo'   => ['free_min' => 100, 'cost' => 2,  'free_text' => 'Dërgesa Falas'],
        'albania'  => ['free_min' => 150, 'cost' => 5,  'free_text' => 'Dërgesa Falas'],
        'macedonia'=> ['free_min' => 200, 'cost' => 8,  'free_text' => 'Dërgesa Falas'],
        'serbia'   => ['free_min' => 200, 'cost' => 10, 'free_text' => 'Dërgesa Falas'],
    ];

    foreach ($countries as $country) {
        foreach (['free_min', 'cost', 'free_text'] as $field) {
            DB::table('settings')->insertOrIgnore([
                'key'   => "shipping_{$country}_{$field}",
                'value' => $defaults[$country][$field],
            ]);
        }
    }
}

public function down(): void
{
    DB::table('settings')->where('key', 'like', 'shipping_%')->delete();
}
};
