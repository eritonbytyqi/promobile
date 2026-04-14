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
        if (!Schema::hasColumn('orders', 'uuid')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            });
        }
 
        \DB::table('orders')->whereNull('uuid')->orderBy('id')->each(function ($order) {
            \DB::table('orders')->where('id', $order->id)->update(['uuid' => Str::uuid()->toString()]);
        });
    }
 
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'uuid')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropUnique(['uuid']);
                $table->dropColumn('uuid');
            });
        }
    }
};
