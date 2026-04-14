<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorySubcategory extends Model
{
    protected $fillable = ['category_id', 'name', 'sort_order'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}