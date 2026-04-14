<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
    'user_id',
    'address_line_1',
    'address_line_2',
    'city',
    'postal_code',
    'country',
    'is_default',
    'full_name',
    'phone',
];
   public function user()
{
    return $this->belongsTo(User::class);
}
}
