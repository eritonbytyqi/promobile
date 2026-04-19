<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderShipped extends Mailable
{
    use SerializesModels;

    public function __construct(public Order $order) {}

    public function build(): static
    {
        return $this
            ->subject('🚚 Porosia juaj u dërgua — #' . $this->order->order_number)
            ->view('emails.order-shipped');
    }
}