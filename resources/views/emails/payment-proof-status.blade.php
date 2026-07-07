<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payment Proof Update</title>
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
                            @if ($decision === 'accepted')
                                <h2 style="margin:0 0 10px;font-size:22px;">Payment verified.</h2>
                                <p style="margin:0 0 22px;color:#5f5f5f;line-height:1.6;">Hi {{ $order->customer_name }}, your payment proof has been accepted. We will now continue processing your order.</p>
                            @else
                                <h2 style="margin:0 0 10px;font-size:22px;">Payment proof needs attention.</h2>
                                <p style="margin:0 0 22px;color:#5f5f5f;line-height:1.6;">Hi {{ $order->customer_name }}, we could not verify the payment proof for your order. Please upload a clearer or correct proof from your tracking page.</p>
                            @endif

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin-bottom:20px;">
                                <tr><td style="padding:8px 0;color:#5f5f5f;border-bottom:1px solid #eeeeee;">Order</td><td align="right" style="padding:8px 0;border-bottom:1px solid #eeeeee;">#{{ $order->order_number }}</td></tr>
                                <tr><td style="padding:8px 0;color:#5f5f5f;border-bottom:1px solid #eeeeee;">Total</td><td align="right" style="padding:8px 0;border-bottom:1px solid #eeeeee;">£{{ number_format((float) $order->total, 2) }}</td></tr>
                                <tr><td style="padding:8px 0;color:#5f5f5f;border-bottom:1px solid #eeeeee;">Payment status</td><td align="right" style="padding:8px 0;border-bottom:1px solid #eeeeee;">{{ str_replace('_', ' ', ucfirst($order->payment_status ?? 'unpaid')) }}</td></tr>
                            </table>

                            @if ($decision !== 'accepted' && $order->admin_note)
                                <div style="margin-bottom:22px;padding:16px;background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;color:#7c2d12;">
                                    <strong style="display:block;margin-bottom:6px;">Admin note</strong>
                                    {{ $order->admin_note }}
                                </div>
                            @endif

                            <a href="{{ route('frontend.track-order', ['order_number' => $order->order_number, 'email' => $order->email]) }}" style="display:inline-block;background:#f5a817;color:#111111;text-decoration:none;font-weight:800;padding:13px 18px;border-radius:6px;">View order status</a>
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
