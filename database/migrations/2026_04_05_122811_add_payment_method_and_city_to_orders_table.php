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
    Schema::table('orders', function (Blueprint $table) {
        if (!Schema::hasColumn('orders', 'payment_method')) {
            $table->string('payment_method')->default('cash')->after('status');
        }
        if (!Schema::hasColumn('orders', 'city')) {
            $table->string('city')->nullable()->after('shipping_address');
        }
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['payment_method', 'city']);
    });
}
};
