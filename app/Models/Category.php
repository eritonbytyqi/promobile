<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
         'icon', 
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_category')
            ->withTimestamps();
    }

    public function scopeAccessories($query)
    {
        $keywords = [
            'accessor',
            'accessories',
            'accesor',
            'aksesor',
            'akesor',
            'Aksesoret',
        ];

        return $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $word) {
                $q->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($word) . '%']);
            }
        });
    }
    public function subcategories()
{
    return $this->hasMany(CategorySubcategory::class)->orderBy('sort_order');
}
}