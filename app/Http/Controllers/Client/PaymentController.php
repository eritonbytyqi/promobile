<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $payment) {}

    // ✅ Krijo PaymentIntent nga sesioni — pa order
    public function createIntent(Request $request)
    {
        $pending = session('pending_checkout');

        if (!$pending) {
            return response()->json(['error' => 'Sesioni skadoi. Kthehu te checkout.'], 422);
        }

        try {
            $cart         = session('cart', []);
            $total        = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
            $shippingCost = (float) ($pending['shipping_cost'] ?? 0);
            $totalAmount  = $total + $shippingCost;

            // Krijo PaymentIntent me Stripe
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $intent = \Stripe\PaymentIntent::create([
                'amount'   => (int) round($totalAmount * 100), // në cents
                'currency' => 'eur',
                'metadata' => [
                    'customer_name'  => $pending['customer_name'] ?? '',
                    'customer_email' => $pending['customer_email'] ?? '',
                ],
            ]);

            return response()->json(['client_secret' => $intent->client_secret]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function refund(Request $request, string $uuid)
    {
        $order = Order::where('uuid', $uuid)->firstOrFail();

        if (!$order->payment_intent_id) {
            return back()->with('error', 'Kjo porosi nuk ka pagesë online.');
        }

        try {
            if ($request->refund_all || !$request->has('items')) {
                $this->payment->refundFull($order);
                return back()->with('success', 'U kthye e gjithë pagesa prej ' . number_format($order->total_amount, 2) . ' € te klienti!');
            }

            $refunded = $this->payment->refundPartial(
                $order,
                $this->payment->calculateRefundAmount($request->items)
            );

            return back()->with('success', "U kthyen {$refunded} € te klienti me sukses!");

        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Gabim gjatë kthimit: ' . $e->getMessage());
        }
    }
}