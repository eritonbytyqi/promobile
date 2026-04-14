<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
     public function allPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Product::with(['category', 'brand', 'images'])
            ->where('is_active', true)
            ->latest()
            ->paginate($perPage);
    }

    public function findWithRelations(int $id): Product
    {
        return Product::with(['images', 'variants', 'variantImages', 'specs'])->findOrFail($id);
    }

    public function findActive(int $id): Product
    {
        return Product::with(['category:id,name', 'brand:id,name', 'images', 'variants', 'variantImages', 'specs'])
            ->where('is_active', true)
            ->findOrFail($id);
    }
}
