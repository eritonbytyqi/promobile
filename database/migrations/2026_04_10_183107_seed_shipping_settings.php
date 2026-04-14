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
        DB::table('settings')->insertOrIgnore([
            ['key' => 'shipping_free_min_price', 'value' => '100'],
            ['key' => 'shipping_cost',           'value' => '2'],
            ['key' => 'shipping_free_text',      'value' => 'Dërgesa Falas'],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'shipping_free_min_price',
            'shipping_cost',
            'shipping_free_text',
        ])->delete();
    }
};
