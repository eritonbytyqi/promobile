<?php

namespace App\Services;

use App\Models\Order;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Stripe;

class PaymentService
{
    public function __construct() { Stripe::setApiKey(config('services.stripe.secret')); }

    public function createIntent(Order $order): string
    {
        $intent = PaymentIntent::create(['amount' => (int)($order->total_amount * 100), 'currency' => 'eur', 'metadata' => ['order_id' => $order->id, 'order_number' => $order->order_number]]);
        $order->update(['payment_intent_id' => $intent->id]);
        return $intent->client_secret;
    }

    public function confirmPayment(Order $order, string $paymentIntentId): bool
    {
        $intent = PaymentIntent::retrieve($paymentIntentId);
        if ($intent->status !== 'succeeded') return false;
        $order->update(['status' => 'pending', 'payment_intent_id' => $paymentIntentId]);
        return true;
    }

    public function refundFull(Order $order): void { Refund::create(['payment_intent' => $order->payment_intent_id]); $order->update(['status' => 'cancelled']); }

    public function refundPartial(Order $order, float $amount): string
    {
        if ($amount <= 0) throw new \InvalidArgumentException('Zgjedh të paktën 1 copë për të kthyer!');
        if ($amount > $order->total_amount) throw new \InvalidArgumentException('Shuma nuk mund të jetë më e madhe se totali!');
        Refund::create(['payment_intent' => $order->payment_intent_id, 'amount' => (int)($amount * 100)]);
        $order->update(['status' => $amount >= $order->total_amount ? 'cancelled' : 'confirmed']);
        return number_format($amount, 2);
    }

    public function calculateRefundAmount(array $items): float
    {
        return array_sum(array_map(fn($d) => (int)($d['quantity'] ?? 0) * (float)($d['unit_price'] ?? 0), $items));
    }
}
