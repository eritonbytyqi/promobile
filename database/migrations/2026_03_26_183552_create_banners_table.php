<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_banner')->default(false)->after('is_active');
            $table->string('banner_badge')->nullable()->after('is_banner');
            $table->string('banner_subtitle')->nullable()->after('banner_badge');
            $table->string('banner_btn_primary_text')->nullable()->after('banner_subtitle');
            $table->string('banner_btn_primary_url')->nullable()->after('banner_btn_primary_text');
            $table->string('banner_btn_secondary_text')->nullable()->after('banner_btn_primary_url');
            $table->string('banner_btn_secondary_url')->nullable()->after('banner_btn_secondary_text');
            $table->string('banner_bg_color')->default('#0a0a1a')->after('banner_btn_secondary_url');
            $table->integer('banner_sort')->default(0)->after('banner_bg_color');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'is_banner',
                'banner_badge',
                'banner_subtitle',
                'banner_btn_primary_text',
                'banner_btn_primary_url',
                'banner_btn_secondary_text',
                'banner_btn_secondary_url',
                'banner_bg_color',
                'banner_sort',
            ]);
        });
    }
};