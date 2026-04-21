<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class OrderRepository
{
    public function allPaginated(int $perPage = 20, ?string $search = null, ?string $status = null): LengthAwarePaginator
    {
        return Order::with(['items.product'])
            ->when($search, fn($q) => $q
                ->where('customer_name',  'like', "%{$search}%")
                ->orWhere('customer_email', 'like', "%{$search}%")
                ->orWhere('customer_phone', 'like', "%{$search}%")
                ->orWhere('order_number',   'like', "%{$search}%")
            )
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    public function findWithItems(int $id): Order
    {
        return Order::with('items.product.images')->findOrFail($id);
    }

    public function findWithStock(int $id): Order
    {
        return Order::with('items.product.variants')->findOrFail($id);
    }

    public function byCustomerEmail(string $email, int $perPage = 15): LengthAwarePaginator
    {
        return Order::where('customer_email', $email)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}