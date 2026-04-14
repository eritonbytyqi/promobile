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
    Schema::create('product_variant_images', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        $table->string('color_hex', 20)->nullable();
        $table->string('color_name')->nullable();
        $table->string('image_path');
        $table->boolean('is_primary')->default(false);
        $table->integer('sort_order')->default(0);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('product_variant_images');
}
};
