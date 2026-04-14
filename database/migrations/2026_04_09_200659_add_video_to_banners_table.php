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
    Schema::table('banners', function (Blueprint $table) {
        $table->string('video')->nullable()->after('image');
        $table->string('image_position')->nullable()->default('center center')->after('video');
    });
}

public function down(): void
{
    Schema::table('banners', function (Blueprint $table) {
        $table->dropColumn(['video', 'image_position']);
    });
}
};
