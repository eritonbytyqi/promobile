<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
public function allPaginated(int $perPage = 25, ?string $search = null, ?string $category = null, ?string $brand = null, ?string $status = null): LengthAwarePaginator
{
    return Product::with(['category', 'brand', 'images'])
        ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
        ->when($category, fn($q) => $q->whereHas('category', fn($q) => $q->where('name', $category)))
        ->when($brand, fn($q) => $q->whereHas('brand', fn($q) => $q->where('name', $brand)))
        ->when($status === 'aktiv', fn($q) => $q->where('is_active', true))
        ->when($status === 'joaktiv', fn($q) => $q->where('is_active', false))
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
