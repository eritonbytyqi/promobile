<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function all(): array   { return Session::get('cart', []); }
    public function count(): int   { return count($this->all()); }
    public function total(): float { return collect($this->all())->sum(fn($i) => $i['price'] * $i['quantity']); }
    public function formattedTotal(): string { return number_format($this->total(), 2); }

    public function add(int $productId, ?int $variantId, int $qty = 1, array $extra = []): array

    {
        $product = Product::with(['images', 'variants'])->findOrFail($productId);
        $cart    = $this->all();
        $variant = $variantId ? $product->variants()->findOrFail($variantId) : null;
        $key     = $variant ? "{$productId}_{$variantId}" : (string) $productId;

        if (isset($cart[$key])) { $cart[$key]['quantity'] += $qty; $added = false; }
        else {
               $cart[$key] = [
        'id'         => $product->id,
        'variant_id' => $variant?->id,
        'name'       => $product->name,
        'price'      => $this->resolvePrice($product, $variant),
        // ✅ Nëse JS dërgon imazhin e ngjyrës, përdore atë — përndryshe fallback
        'image'      => $extra['image'] ?? $this->resolveImage($product, $variant),
        'quantity'   => $qty,
        'color'      => $extra['color'] ?? $variant?->color_name,
        'storage'    => $extra['storage'] ?? $variant?->storage,
    ];
    $added = true;
}

        Session::put('cart', $cart);
        return ['added' => $added, 'cart' => $cart];
    }

    public function update(string $key, int $quantity): array
    {
        $cart = $this->all();
        if (isset($cart[$key])) { $cart[$key]['quantity'] = $quantity; Session::put('cart', $cart); }
        return $cart;
    }

    public function remove(string $key): array
    {
        $cart = $this->all();
        unset($cart[$key]);
        Session::put('cart', $cart);
        return $cart;
    }

    public function clear(): void { Session::forget('cart'); }

    private function resolvePrice($product, $variant): float
    {
        if ($variant) return ($variant->sale_price && $variant->sale_price < $variant->price) ? $variant->sale_price : $variant->price;
        return ($product->sale_price && $product->sale_price < $product->price) ? $product->sale_price : $product->price;
    }

    private function resolveImage($product, $variant): ?string
    {
        if ($variant && !empty($variant->image_path)) return asset('storage/' . $variant->image_path);
        $img = $product->images?->firstWhere('is_primary', true) ?? $product->images?->first();
        return $img ? asset('storage/' . $img->image_path) : null;
    }
}
