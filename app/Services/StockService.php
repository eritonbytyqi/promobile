<?php
namespace App\Services;
 
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductStockLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
 
class StockService
{
    // ── Ndrysho stokun e variantit ──
    public function updateVariant(
        ProductVariant $variant,
        int $newStock,
        string $type = 'adjustment',
        string $note = '',
        ?int $orderId = null
    ): ProductVariant {
        return DB::transaction(function () use ($variant, $newStock, $type, $note, $orderId) {
            $before = $variant->stock;
            $qty    = $newStock - $before; // diferenca
 
            $variant->update(['stock' => $newStock]);
 
            ProductStockLog::create([
                'product_id'   => $variant->product_id,
                'variant_id'   => $variant->id,
                'type'         => $type,
                'quantity'     => $qty,
                'stock_before' => $before,
                'stock_after'  => $newStock,
                'note'         => $note ?: $this->defaultNote($type),
                'created_by'   => Auth::id(),
                'order_id'     => $orderId,
            ]);
 
            return $variant->fresh();
        });
    }
 
    // ── Zbrit stok kur bëhet porosi ──
   public function deductForOrder(
    int $variantId,
    int $quantity,
    int $orderId
): bool {
    return DB::transaction(function () use ($variantId, $quantity, $orderId) {
        $variant = ProductVariant::lockForUpdate()->findOrFail($variantId);

        if ($variant->stock < $quantity) {
            return false;
        }

        $before = $variant->stock;
        $after = $before - $quantity;

        $variant->update(['stock' => $after]);

        ProductStockLog::create([
            'product_id'   => $variant->product_id,
            'variant_id'   => $variant->id,
            'type'         => 'order',
            'quantity'     => -$quantity,
            'stock_before' => $before,
            'stock_after'  => $after,
            'note'         => "Porosi #{$orderId}",
            'created_by'   => null,
            'order_id'     => $orderId,
        ]);

        return true;
    });
}

 
    // ── Zbrit stok produkti pa variante ──
  public function deductProductForOrder(
    int $productId,
    int $quantity,
    int $orderId
): bool {
    return DB::transaction(function () use ($productId, $quantity, $orderId) {
        $product = Product::lockForUpdate()->findOrFail($productId);

        if (($product->stock ?? 0) < $quantity) {
            return false;
        }

        $before = $product->stock ?? 0;
        $after = $before - $quantity;

        $product->update(['stock' => $after]);

        ProductStockLog::create([
            'product_id'   => $productId,
            'variant_id'   => null,
            'type'         => 'order',
            'quantity'     => -$quantity,
            'stock_before' => $before,
            'stock_after'  => $after,
            'note'         => "Porosi #{$orderId}",
            'created_by'   => null,
            'order_id'     => $orderId,
        ]);

        return true;
    });
}

 
    // ── Kthe stok kur cancellohet porosi ──
    public function returnFromOrder(
        int $variantId,
        int $quantity,
        int $orderId
    ): void {
        $variant = ProductVariant::findOrFail($variantId);
        $before  = $variant->stock;
        $after   = $before + $quantity;
 
        $variant->update(['stock' => $after]);
 
        ProductStockLog::create([
            'product_id'   => $variant->product_id,
            'variant_id'   => $variant->id,
            'type'         => 'return',
            'quantity'     => +$quantity,
            'stock_before' => $before,
            'stock_after'  => $after,
            'note'         => "Kthim porosi #{$orderId}",
            'created_by'   => Auth::id(),
            'order_id'     => $orderId,
        ]);
    }
 
    // ── Ndrysho stok produkti pa variante (nga admin) ──
    public function updateProduct(
        Product $product,
        int $newStock,
        string $type = 'adjustment',
        string $note = ''
    ): Product {
        return DB::transaction(function () use ($product, $newStock, $type, $note) {
            $before = $product->stock ?? 0;
            $qty    = $newStock - $before;
 
            $product->update(['stock' => $newStock]);
 
            ProductStockLog::create([
                'product_id'   => $product->id,
                'variant_id'   => null,
                'type'         => $type,
                'quantity'     => $qty,
                'stock_before' => $before,
                'stock_after'  => $newStock,
                'note'         => $note ?: $this->defaultNote($type),
                'created_by'   => Auth::id(),
            ]);
 
            return $product->fresh();
        });
    }
 
    private function defaultNote(string $type): string
    {
        return match($type) {
            'in'         => 'Rimbushje stoku',
            'out'        => 'Zbritje stoku',
            'adjustment' => 'Korrigjim manual',
            default      => 'Ndryshim stoku',
        };
    }

    public function returnProductFromOrder(
    int $productId,
    int $quantity,
    int $orderId
): void {
    $product = Product::findOrFail($productId);
    $before = $product->stock ?? 0;
    $after = $before + $quantity;

    $product->update(['stock' => $after]);

    ProductStockLog::create([
        'product_id'   => $productId,
        'variant_id'   => null,
        'type'         => 'return',
        'quantity'     => $quantity,
        'stock_before' => $before,
        'stock_after'  => $after,
        'note'         => "Kthim porosi #{$orderId}",
        'created_by'   => Auth::id(),
        'order_id'     => $orderId,
    ]);
}

}