<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'city',
        'total_amount',
        'status',
        'notes',
        'payment_method',
         'payment_intent_id','uuid',
         'shipping_cost',
         'country'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
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