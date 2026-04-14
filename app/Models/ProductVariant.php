<?php
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'color_name', 'color_hex',
        'storage', 'price', 'sale_price', 'stock',
        'is_active', 'sort_order', 'image_path',    'base_price',    // ← shto
    'extra_price', 

    ];
 
    protected $casts = [
        'price'      => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active'  => 'boolean',
    ];
 
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
 
    // Çmimi final (sale ose normal)
    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }
    public function stockLogs()
{
    return $this->hasMany(\App\Models\ProductStockLog::class, 'variant_id')
                ->orderByDesc('created_at');
}
}
 