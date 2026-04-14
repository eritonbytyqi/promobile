<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $payment) {}

    // Stripe payment page — UUID në URL
    public function page(string $uuid)
    {
        $order = Order::where('uuid', $uuid)->with('items.product')->firstOrFail();
        return view('shop.order.stripe-payment', compact('order'));
    }

    public function createIntent(Request $request)
    {
        // order_id tani është uuid
        $request->validate(['order_id' => 'required|exists:orders,uuid']);
        try {
            $order = Order::where('uuid', $request->order_id)->firstOrFail();
            return response()->json(['client_secret' => $this->payment->createIntent($order)]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function confirm(Request $request)
    {
        try {
            $order = Order::where('uuid', $request->order_id)->firstOrFail();
            if (!$this->payment->confirmPayment($order, $request->payment_intent_id)) {
                return response()->json(['success' => false, 'message' => 'Pagesa nuk u krye.'], 400);
            }
            return response()->json(['success' => true]);
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
            $refunded = $this->payment->refundPartial($order, $this->payment->calculateRefundAmount($request->items));
            return back()->with('success', "U kthyen {$refunded} € te klienti me sukses!");
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Gabim gjatë kthimit: ' . $e->getMessage());
        }
    }
}