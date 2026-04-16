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
  if ($order->customer_email) {
        $this->sendReceivedEmail($order);
    }
        return $order;
    }
public function sendReceivedEmail(Order $order): void
{
    try {
        $emailHtml = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#f4f6fb;font-family:Inter,Arial,sans-serif;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:32px 0;">
        <tr><td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
          <tr>
            <td style="background:linear-gradient(135deg,#1a7a4a 0%,#34c759 100%);padding:32px 40px;text-align:center;">
              <div style="font-size:40px;margin-bottom:8px;">✅</div>
              <h1 style="color:#ffffff;margin:0;font-size:24px;font-weight:800;">Porosia u Pranua!</h1>
              <p style="color:rgba(255,255,255,0.8);margin:6px 0 0;font-size:14px;">#' . $order->order_number . '</p>
            </td>
          </tr>
          <tr>
            <td style="padding:32px 40px;">
              <p style="font-size:15px;color:#414753;margin-bottom:24px;line-height:1.6;">
                Përshëndetje <strong>' . $order->customer_name . '</strong>,<br>
                Porosia juaj është pranuar dhe po procesohet. Do ju njoftojmë kur të dërgohet.
              </p>
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9ff;border-radius:14px;overflow:hidden;margin-bottom:24px;">
                <tr><td style="padding:14px 20px;border-bottom:1px solid #eef0f8;">
                  <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Numri i Porosisë</span><br>
                  <span style="font-size:15px;font-weight:700;color:#1a1c1d;">' . $order->order_number . '</span>
                </td></tr>
                <tr><td style="padding:14px 20px;border-bottom:1px solid #eef0f8;">
                  <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Adresa e Dërgimit</span><br>
                  <span style="font-size:15px;font-weight:700;color:#1a1c1d;">' . ($order->shipping_address ?? 'N/A') . ', ' . ($order->city ?? 'N/A') . '</span>
                </td></tr>
                <tr><td style="padding:14px 20px;">
                  <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Totali</span><br>
                  <span style="font-size:24px;font-weight:800;color:#1a7a4a;">' . number_format($order->total_amount, 2) . ' €</span>
                </td></tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="background:#f8f9ff;padding:20px 40px;text-align:center;border-top:1px solid #eef0f8;">
              <p style="color:#8b95b0;font-size:12px;margin:0;">ProMobile Store · Faleminderit për blerjen tuaj!</p>
            </td>
          </tr>
        </table>
        </td></tr></table>
        </body></html>';

        \Mail::html($emailHtml, fn($m) => $m
            ->to($order->customer_email)
            ->subject('✅ Porosia juaj u pranua — #' . $order->order_number)
        );
    } catch (\Exception $e) {
        \Log::error('Email pranimi dështoi: ' . $e->getMessage());
    }
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
