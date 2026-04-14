<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
protected $fillable = [
    'title', 'subtitle', 'badge_text', 'price',
    'image', 'video', 'image_position', 'bg_color',
    'btn_primary_text', 'btn_primary_url',
    'btn_secondary_text', 'btn_secondary_url',
    'sort_order', 'active', 'product_id',
];

    protected $casts = [
        'active' => 'boolean',
        'price'  => 'decimal:2',
    ];
    public function product()
{
    return $this->belongsTo(\App\Models\Product::class);
}
}