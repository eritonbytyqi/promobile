<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
     protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'stock',
        'is_active',
         'subcategory',
           'featured',
         'uuid',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

public function images()
{
    return $this->hasMany(ProductImage::class)->orderBy('sort_order');
}

public function primaryImage()
{
    return $this->hasOne(ProductImage::class)->where('is_primary', true);
}

 
public function variants()
{
    return $this->hasMany(\App\Models\ProductVariant::class)
                ->orderBy('sort_order');
}
// ProductSpec relacioni (nëse nuk e ke):
public function specs()
{
    return $this->hasMany(\App\Models\ProductSpec::class);
}
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function stockLogs()
{
    return $this->hasMany(\App\Models\ProductStockLog::class)
                ->orderByDesc('created_at');
}
public function variantImages()
{
    return $this->hasMany(ProductVariantImage::class)->orderBy('sort_order');
}
public function accessories()
    {
        return $this->belongsToMany(
            Product::class,
            'product_accessories',
            'product_id',
            'accessory_id'
        )->withPivot('sort_order')
         ->orderBy('product_accessories.sort_order');
    }
 
    // ── PRODUKTET QË E KANË KËTË SI AKSESOR (reverse) ───────────
    // Opsional — nëse do të dish te akesori me cilin produkt lidhet
    public function accessoryOf()
    {
        return $this->belongsToMany(
            Product::class,
            'product_accessories',
            'accessory_id',
            'product_id'
        );
    }
public function scopeActive($query)
{
    return $query->where('is_active', 1);
}

protected static function boot(): void
{
    parent::boot();
    static::creating(fn($m) => $m->uuid ??= Str::uuid());
}

public function getRouteKeyName(): string
{
    return 'uuid';
}
}
