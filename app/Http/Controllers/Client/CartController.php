<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function index()    {
            $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
 return view('shop.order.checkout', ['cart' => $this->cart->all(),
  'total' => $this->cart->total(),
  'settings' => $settings,
  ]);
   }
    public function checkout() { 
    $settings = \App\Models\Setting::pluck('value', 'key')->toArray();    
    return view('shop.order.checkout',
     ['cart' => $this->cart->all(),
      'total' => $this->cart->total(),
      'settings' => $settings
      ]);
       }
    public function sidebar()  { return response()->json($this->buildResponse($this->cart->all())); }

public function add(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'variant_id' => 'nullable|exists:product_variants,id',
        'quantity'   => 'nullable|integer|min:1|max:99',
    ]);

    // ✅ Shto këto 3 rreshta
    $extra = [
        'image'   => $request->input('image'),
        'color'   => $request->input('color'),
        'storage' => $request->input('storage'),
    ];

    ['added' => $added, 'cart' => $cart] = $this->cart->add(
        $request->product_id,
        $request->variant_id,
        $request->quantity ?? 1,
        $extra   // ✅ kalon te CartService
    );

    return response()->json([
        ...$this->buildResponse($cart),
        'success' => true,
        'message' => $added ? 'Produkti u shtua!' : 'Sasia u rrit!',
    ]);
}
    public function update(Request $request)
    {
        $request->validate(['key' => 'required|string', 'quantity' => 'required|integer|min:1|max:99']);
        return response()->json([...$this->buildResponse($this->cart->update($request->key, $request->quantity)), 'success' => true]);
    }

    public function remove(Request $request)
    {
        $request->validate(['key' => 'required|string']);
        return response()->json([...$this->buildResponse($this->cart->remove($request->key)), 'success' => true]);
    }

    public function clear() { $this->cart->clear(); return response()->json(['success' => true, 'count' => 0, 'total' => '0.00']); }

    private function buildResponse(array $cart): array
    {
        $total = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        return ['count' => count($cart), 'total' => number_format($total, 2), 'html' => view('shop.partials.cart-sidebar-items', compact('cart'))->render()];
    }
}
