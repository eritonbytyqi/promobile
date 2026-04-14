<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('subtitle')->nullable()->change();
            $table->string('badge_text')->nullable()->change();
            $table->string('image')->nullable()->change();
            $table->string('video')->nullable()->change();
            $table->string('image_position')->nullable()->change();
            $table->string('bg_color')->nullable()->change();
            $table->string('btn_primary_text')->nullable()->change();
            $table->string('btn_primary_url')->nullable()->change();
            $table->string('btn_secondary_text')->nullable()->change();
            $table->string('btn_secondary_url')->nullable()->change();
            $table->unsignedBigInteger('product_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
            $table->string('subtitle')->nullable(false)->change();
            $table->string('badge_text')->nullable(false)->change();
            $table->string('image')->nullable(false)->change();
            $table->string('video')->nullable(false)->change();
            $table->string('image_position')->nullable(false)->change();
            $table->string('bg_color')->nullable(false)->change();
            $table->string('btn_primary_text')->nullable(false)->change();
            $table->string('btn_primary_url')->nullable(false)->change();
            $table->string('btn_secondary_text')->nullable(false)->change();
            $table->string('btn_secondary_url')->nullable(false)->change();
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });
    }
};