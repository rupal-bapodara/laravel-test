<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;

class OrderPlacedEmail extends Mailable
{
    public function __construct(public Order $order) {}

    public function build(): self
    {
        return $this->subject('Order placed successfully')
            ->view('emails.order_placed');
    }
}
