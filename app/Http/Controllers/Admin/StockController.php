<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStockLog;
use App\Models\ProductVariant;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(protected StockService $stock) {}

    public function index(Request $request)
    {
        $search   = $request->input('search');
        $filter   = $request->input('filter');
        $products = Product::with(['category','brand','images','variants'])->when($search, fn($q) => $q->where('name','like',"%{$search}%"))->latest()->get();

        $stats = ['total_products' => $products->count(), 'out_of_stock' => 0, 'low_stock' => 0, 'total_variants' => 0];
        foreach ($products as $p) {
            if ($p->variants->count() > 0) { foreach ($p->variants as $v) { $stats['total_variants']++; if ($v->stock <= 0) $stats['out_of_stock']++; elseif ($v->stock <= 3) $stats['low_stock']++; } }
            else { if (($p->stock ?? 0) <= 0) $stats['out_of_stock']++; elseif (($p->stock ?? 0) <= 3) $stats['low_stock']++; }
        }

        if ($filter === 'out') $products = $products->filter(fn($p) => $p->variants->count() > 0 ? $p->variants->contains(fn($v) => $v->stock <= 0) : ($p->stock ?? 0) <= 0);
        elseif ($filter === 'low') $products = $products->filter(fn($p) => $p->variants->count() > 0 ? $p->variants->contains(fn($v) => $v->stock > 0 && $v->stock <= 3) : (($p->stock ?? 0) > 0 && ($p->stock ?? 0) <= 3));

        return view('admin.stock.index', compact('products','stats','search','filter'));
    }

    public function history($productId)
    {
        $product = Product::with(['variants','images'])->findOrFail($productId);
        $logs    = ProductStockLog::with(['variant','creator'])->where('product_id',$productId)->orderByDesc('created_at')->paginate(30);
        return view('admin.stock.history', compact('product','logs'));
    }

    public function updateVariantStock(Request $request, $id)
    {
        $request->validate(['stock' => 'required|integer|min:0', 'note' => 'nullable|string|max:255']);
        $variant = ProductVariant::findOrFail($id);
        $variant = $this->stock->updateVariant($variant, $request->stock, $request->stock > $variant->stock ? 'in' : 'out', $request->note ?? '');
        return response()->json(['success' => true, 'stock' => $variant->stock]);
    }

    public function updateProductStock(Request $request, $id)
    {
        $request->validate(['stock' => 'required|integer|min:0', 'note' => 'nullable|string|max:255']);
        $product = Product::findOrFail($id);
        $product = $this->stock->updateProduct($product, $request->stock, $request->stock > ($product->stock ?? 0) ? 'in' : 'out', $request->note ?? '');
        return response()->json(['success' => true, 'stock' => $product->stock]);
    }

    public function lowStock()
    {
        return response()->json(['low' => ProductVariant::with(['product.images'])->where('stock','>',0)->where('stock','<=',3)->orderBy('stock')->get(), 'out' => ProductVariant::with(['product.images'])->where('stock','<=',0)->get()]);
    }
}
