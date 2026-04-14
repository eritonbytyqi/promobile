<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository
{
    public function allPaginated(int $perPage = 20): LengthAwarePaginator
    {
        return Order::latest()->paginate($perPage);
    }

    public function findWithItems(int $id): Order
    {
        return Order::with('items.product')->findOrFail($id);
    }

    public function findWithStock(int $id): Order
    {
        return Order::with('items.product.variants')->findOrFail($id);
    }

    public function byCustomerEmail(string $email, int $perPage = 15): LengthAwarePaginator
    {
        return Order::where('customer_email', $email)->orderByDesc('created_at')->paginate($perPage);
    }
}
