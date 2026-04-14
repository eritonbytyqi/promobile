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
    \DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
        'pending',
        'confirmed',
        'processing',
        'shipped',
        'delivered',
        'cancelled',
        'awaiting_payment',
        'payment_failed'
    ) NOT NULL DEFAULT 'pending'");
}

public function down(): void
{
    \DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
        'pending',
        'confirmed',
        'processing',
        'shipped',
        'delivered',
        'cancelled'
    ) NOT NULL DEFAULT 'pending'");
}
};
