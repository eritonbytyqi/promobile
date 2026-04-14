<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'badge',
        'link_text',
        'url',
        'bg_color',
        'text_color',
        'image',
        'is_active',
        'sort_order',
        'product_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getFinalUrlAttribute()
    {
        if (!empty($this->url)) {
            return $this->url;
        }

        if ($this->product_id) {
            return url('/shop/' . $this->product_id);
        }

        return url('/shop?featured=1');
    }
}