<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order Confirmation</title>
</head>
<body style="margin:0;background:#f4f4f4;font-family:Arial,Helvetica,sans-serif;color:#161616;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f4f4;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="620" cellspacing="0" cellpadding="0" style="max-width:620px;width:100%;background:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e6e6e6;">
                    <tr>
                        <td style="background:#050505;padding:26px 30px;color:#ffffff;">
                            <h1 style="margin:0;font-size:26px;line-height:1.15;text-transform:uppercase;">Arete Performance</h1>
                            <p style="margin:8px 0 0;color:#f5a817;font-weight:700;">Order #{{ $order->order_number }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 30px;">
                            <h2 style="margin:0 0 10px;font-size:22px;">Thanks, {{ $order->customer_name }}.</h2>
                            <p style="margin:0 0 22px;color:#5f5f5f;line-height:1.6;">Your order has been received. Here are your order details.</p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td style="padding:14px 0;border-bottom:1px solid #ececec;">
                                            <strong style="display:block;">{{ $item->product_name }}</strong>
                                            <span style="color:#707070;font-size:13px;">Qty: {{ $item->quantity }} x £{{ number_format((float) $item->unit_price, 2) }}</span>
                                        </td>
                                        <td align="right" style="padding:14px 0;border-bottom:1px solid #ececec;font-weight:700;">£{{ number_format((float) $item->line_total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:18px;">
                                <tr><td style="padding:6px 0;color:#5f5f5f;">Subtotal</td><td align="right" style="padding:6px 0;">£{{ number_format((float) $order->subtotal, 2) }}</td></tr>
                                <tr><td style="padding:6px 0;color:#5f5f5f;">Shipping</td><td align="right" style="padding:6px 0;">£{{ number_format((float) $order->shipping_total, 2) }}</td></tr>
                                <tr><td style="padding:12px 0 0;font-size:18px;font-weight:800;">Total</td><td align="right" style="padding:12px 0 0;font-size:18px;font-weight:800;color:#f5a817;">£{{ number_format((float) $order->total, 2) }}</td></tr>
                            </table>

                            <div style="margin-top:24px;padding:18px;background:#fafafa;border:1px solid #eeeeee;border-radius:8px;">
                                <strong style="display:block;margin-bottom:8px;">Shipping Address</strong>
                                <p style="margin:0;color:#5f5f5f;line-height:1.55;">
                                    @if ($order->company){{ $order->company }}<br>@endif
                                    {{ $order->address }}<br>
                                    @if ($order->address_2){{ $order->address_2 }}<br>@endif
                                    {{ $order->city }}, {{ $order->state }} {{ $order->zip }}<br>{{ $order->country }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 30px;background:#050505;color:#bdbdbd;font-size:12px;">
                            Premium performance solutions designed to help you reach your full potential.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
