<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantImage extends Model
{
    protected $fillable = [
        'product_id',
        'color_hex',
        'color_name',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}