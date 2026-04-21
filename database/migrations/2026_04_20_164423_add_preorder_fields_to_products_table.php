<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('products', function (Blueprint $table) {
        $table->boolean('allow_preorder')->default(false)->after('stock');
        $table->string('preorder_note')->nullable()->after('allow_preorder');
        $table->integer('low_stock_threshold')->default(10)->after('preorder_note');
    });
}

public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn(['allow_preorder', 'preorder_note', 'low_stock_threshold']);
    });
}
};
