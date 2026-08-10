<p>Hi {{ $order->user->name }},</p>
<p>Your order for <strong>{{ $order->product_name }}</strong> has been placed successfully.</p>
<p>Quantity: {{ $order->quantity }}</p>
<p>Total: ${{ number_format($order->total, 2) }}</p>
<p>We will notify you when your order status changes.</p>
