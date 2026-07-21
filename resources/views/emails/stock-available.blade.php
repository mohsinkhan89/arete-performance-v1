<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $stockNotification->product?->name }} is available</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Good news, {{ $stockNotification->customer_name }}.</h2>
    <p>{{ $stockNotification->product?->name }} is now available at Arete Performance.</p>
    <p>You asked to be notified for {{ $stockNotification->quantity }} item(s).</p>
    <p>
        <a href="{{ route('frontend.product-details', $stockNotification->product?->slug ?? $stockNotification->product_id) }}" style="display: inline-block; background: #d6a84f; color: #111827; padding: 12px 18px; text-decoration: none; font-weight: 700;">
            View Product
        </a>
    </p>
    <p>Thank you,<br>Arete Performance</p>
</body>
</html>
