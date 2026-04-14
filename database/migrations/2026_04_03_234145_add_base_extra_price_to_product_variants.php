<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up() {
    Schema::table('product_variants', function (Blueprint $table) {
        $table->decimal('base_price', 10, 2)->default(0)->after('price');
        $table->decimal('extra_price', 10, 2)->default(0)->after('base_price');
    });
}

public function down() {
    Schema::table('product_variants', function (Blueprint $table) {
        $table->dropColumn(['base_price', 'extra_price']);
    });
}
};
