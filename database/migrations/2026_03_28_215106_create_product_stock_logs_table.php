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
        Schema::create('product_stock_logs', function (Blueprint $table) {
            $table->id();
 
            // Lidhja me variantin ose produktin direkt
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()
                  ->constrained('product_variants')->nullOnDelete();
 
            // Lloji i ndryshimit
            $table->enum('type', [
                'in',           // Shtim manual (rimbushje)
                'out',          // Zbritje manuale
                'order',        // Porosi e klientit
                'return',       // Kthim porosie
                'adjustment',   // Korrigjim
            ]);
 
            $table->integer('quantity');      // pozitiv ose negativ
            $table->integer('stock_before');  // stoku para
            $table->integer('stock_after');   // stoku pas
            $table->string('note')->nullable(); // arsyeja
 
            // Kush e bëri ndryshimin
            $table->foreignId('created_by')->nullable()
                  ->constrained('users')->nullOnDelete();
 
            // Lidhja me porosinë nëse ka
            $table->unsignedBigInteger('order_id')->nullable();
 
            $table->timestamps();
 
            // Indekse për query të shpejta
            $table->index(['product_id', 'created_at']);
            $table->index(['variant_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stock_logs');
    }
};
