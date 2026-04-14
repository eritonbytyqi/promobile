<?php

namespace App\Services;

use App\Mail\OrderCancelled;
use App\Mail\OrderConfirmed;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    public function __construct(protected StockService $stock) {}

    public function createFromCheckout(Request $request): Order
    {
        $cart = session('cart', []);
        $total = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $shippingCost = (float) ($request->shipping_cost ?? 0);
        $order = Order::create([
            'order_number'     => 'ORD-' . strtoupper(uniqid()),
            'customer_name'    => $request->customer_name,
            'customer_email'   => $request->customer_email,
            'customer_phone'   => $request->customer_phone,
            'shipping_address' => $request->shipping_address,
            'city'             => $request->city,
            'notes'            => $request->notes,
            'status'           => $request->payment_method === 'bank' ? 'awaiting_payment' : 'pending',
            'payment_method'   => $request->payment_method,
            'total_amount'     => $total + $shippingCost,
        ]);

        foreach ($cart as $item) {
            $order->items()->create([
                'product_id' => $item['id'],
                'variant_id' => $item['variant_id'] ?? null,
                'quantity'   => $item['quantity'],
                'unit_price' => $item['price'],
                'subtotal'   => $item['price'] * $item['quantity'],
            ]);

            if (!empty($item['variant_id'])) {
                $this->stock->deductForOrder($item['variant_id'], $item['quantity'], $order->id);
            } else {
                $this->stock->deductProductForOrder($item['id'], $item['quantity'], $order->id);
            }
        }

        session()->forget('cart');
        $this->sendConfirmationEmail($order);

        return $order;
    }

    public function createManual(Request $request): Order
    {
        $qty = $request->quantity ?? 1;
        $total = $request->price * $qty;

        $order = Order::create([
            'order_number'     => 'ORD-' . strtoupper(uniqid()),
            'customer_name'    => $request->customer_name,
            'customer_email'   => $request->customer_email,
            'customer_phone'   => $request->customer_phone,
            'shipping_address' => $request->shipping_address,
            'city'             => $request->city,
            'notes'            => $request->notes,
            'total_amount'     => $total,
            'status'           => 'pending',
            'payment_method'   => $request->payment_method ?? 'cash',
        ]);

        $order->items()->create([
            'product_id' => $request->product_id,
            'variant_id' => $request->variant_id ?? null,
            'quantity'   => $qty,
            'unit_price' => $request->price,
            'subtotal'   => $total,
        ]);

        if ($request->variant_id) {
            $this->stock->deductForOrder($request->variant_id, $qty, $order->id);
        } else {
            $this->stock->deductProductForOrder($request->product_id, $qty, $order->id);
        }

        return $order;
    }

   public function updateStatus(Order $order, string $newStatus, array $fields = [], bool $skipEmail = false): void
{
    $oldStatus = $order->status;

    $order->update(array_filter(array_merge($fields, ['status' => $newStatus])));

    if (
        !in_array($oldStatus, ['cancelled', 'payment_failed']) &&
        in_array($newStatus, ['cancelled', 'payment_failed'])
    ) {
        $this->restoreStock($order);
    }

    if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled' && !$skipEmail) {
        $this->handleCancellation($order);
    }

    if ($newStatus === 'confirmed' && $oldStatus !== 'confirmed' && !$skipEmail) {
        $this->sendConfirmationEmail($order);
    }
}

    public function delete(Order $order): void
    {
        if (!in_array($order->status, ['cancelled', 'payment_failed'])) {
            $this->restoreStock($order);
        }

        $order->items()->delete();
        $order->delete();
    }

    public function sendConfirmationEmail(Order $order): void
    {
        try {
            Mail::to($order->customer_email)->send(new OrderConfirmed($order->load('items.product')));
        } catch (\Exception $e) {
            Log::error("Email konfirmimi dështoi: " . $e->getMessage());
        }
    }

    public function statusMessage(string $status): string
    {
        return match($status) {
            'pending'         => 'Porosia u vendos në pritje!',
            'confirmed'       => 'Porosia u konfirmua me sukses! ✓',
            'shipped'         => 'Porosia u shënua si e dërguar!',
            'delivered'       => 'Porosia u dorëzua me sukses! ✓',
            'cancelled'       => 'Porosia u anulua dhe stoku u kthye.',
            'payment_failed'  => 'Pagesa dështoi dhe stoku u kthye.',
            default           => 'Porosia u përditësua!',
        };
    }

    private function handleCancellation(Order $order): void
    {
        if ($order->payment_intent_id) {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                \Stripe\Refund::create([
                    'payment_intent' => $order->payment_intent_id
                ]);
            } catch (\Exception $e) {
                Log::error("Refund dështoi #{$order->id}: " . $e->getMessage());
            }
        }

        try {
            Mail::to($order->customer_email)->send(new OrderCancelled($order->load('items.product')));
        } catch (\Exception $e) {
            Log::error("Email anulimi dështoi: " . $e->getMessage());
        }
    }

  private function restoreStock(Order $order): void
{
    foreach ($order->items as $item) {
        if ($item->variant_id) {
            $this->stock->returnFromOrder($item->variant_id, $item->quantity, $order->id);
        } else {
            $this->stock->returnProductFromOrder($item->product_id, $item->quantity, $order->id);
        }
    }
}

}
