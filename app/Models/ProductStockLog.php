<?php
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class ProductStockLog extends Model
{
    protected $fillable = [
        'product_id',
        'variant_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'note',
        'created_by',
        'order_id',
    ];
 
    protected $casts = [
        'created_at' => 'datetime',
    ];
 
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
 
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
 
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
 
    // Badge ngjyra sipas tipit
    public function getTypeBadgeAttribute(): string
    {
        return match($this->type) {
            'in'         => 'ok',
            'out'        => 'low',
            'order'      => 'out',
            'return'     => 'ok',
            'adjustment' => 'blue',
            default      => 'low',
        };
    }
 
    // Teksti i tipit
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'in'         => 'Rimbushje',
            'out'        => 'Zbritje',
            'order'      => 'Porosi',
            'return'     => 'Kthim',
            'adjustment' => 'Korrigjim',
            default      => $this->type,
        };
    }
 
    // Shenja + ose - para sasisë
    public function getQuantityDisplayAttribute(): string
    {
        $sign = $this->quantity > 0 ? '+' : '';
        return $sign . $this->quantity;
    }
}
 
 