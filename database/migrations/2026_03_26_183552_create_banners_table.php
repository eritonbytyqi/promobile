<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
      Schema::create('banners', function (Blueprint $table) {
    $table->id();
    $table->string('title')->nullable();
    $table->string('subtitle')->nullable();
    $table->string('badge_text')->nullable();
    $table->decimal('price', 10, 2)->nullable();
    $table->string('image')->nullable();
    $table->string('video')->nullable();
    $table->string('image_position')->nullable();
    $table->string('bg_color')->nullable();
    $table->string('btn_primary_text')->nullable();
    $table->string('btn_primary_url')->nullable();
    $table->string('btn_secondary_text')->nullable();
    $table->string('btn_secondary_url')->nullable();
    $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
    $table->boolean('active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};