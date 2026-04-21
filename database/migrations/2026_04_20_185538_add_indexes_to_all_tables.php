<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
{
    $indexes = [
        'products' => [
            ['is_active'],
            ['category_id'],
            ['brand_id'],
            ['featured'],
            ['created_at'],
            ['uuid'],
            ['is_active', 'category_id'],
            ['is_active', 'brand_id'],
            ['is_active', 'featured'],
        ],
        'orders' => [
            ['uuid'],
            ['status'],
            ['customer_email'],
            ['created_at'],
            ['payment_method'],
            ['status', 'created_at'],
        ],
        'order_items' => [
            ['order_id'],
            ['product_id'],
            ['variant_id'],
        ],
        'product_images' => [
            ['product_id'],
            ['is_primary'],
            ['product_id', 'is_primary'],
        ],
        'product_variants' => [
            ['product_id'],
            ['color_name'],
            ['product_id', 'color_name'],
        ],
        'product_variant_images' => [
            ['product_id'],
            ['color_hex'],
            ['product_id', 'color_hex'],
        ],
        'product_specs'       => [['product_id']],
        'product_stock_logs'  => [['product_id'],['variant_id'],['order_id'],['created_at']],
        'product_accessories' => [['product_id'],['accessory_id']],
        'categories'          => [['name']],
        'brands'              => [['name']],
        'brand_category'      => [['brand_id'],['category_id']],
        'users'               => [['role'],['device_token']],
        'user_addresses'      => [['user_id'],['is_default'],['user_id','is_default']],
        'banners'             => [['active'],['sort_order']],
        'offers'              => [['is_active'],['sort_order']],
        'category_subcategories' => [['category_id'],['sort_order']],
    ];

    foreach ($indexes as $table => $cols) {
        foreach ($cols as $columns) {
            try {
                Schema::table($table, function (Blueprint $t) use ($columns) {
                    $t->index($columns);
                });
            } catch (\Exception $e) {
                // Index ekziston tashmë — skip
            }
        }
    }
}

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['brand_id']);
            $table->dropIndex(['featured']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['uuid']);
            $table->dropIndex(['is_active', 'category_id']);
            $table->dropIndex(['is_active', 'brand_id']);
            $table->dropIndex(['is_active', 'featured']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['uuid']);
            $table->dropIndex(['status']);
            $table->dropIndex(['customer_email']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['payment_method']);
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['product_id']);
            $table->dropIndex(['variant_id']);
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['is_primary']);
            $table->dropIndex(['product_id', 'is_primary']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['color_name']);
            $table->dropIndex(['product_id', 'color_name']);
        });

        Schema::table('product_variant_images', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['color_hex']);
            $table->dropIndex(['product_id', 'color_hex']);
        });

        Schema::table('product_specs', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });

        Schema::table('product_stock_logs', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['variant_id']);
            $table->dropIndex(['order_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('product_accessories', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['accessory_id']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('brand_category', function (Blueprint $table) {
            $table->dropIndex(['brand_id']);
            $table->dropIndex(['category_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['device_token']);
        });

        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['is_default']);
            $table->dropIndex(['user_id', 'is_default']);
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropIndex(['active']);
            $table->dropIndex(['sort_order']);
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['sort_order']);
        });

        Schema::table('category_subcategories', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
            $table->dropIndex(['sort_order']);
        });
    }
};