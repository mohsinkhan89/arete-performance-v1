<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px; background: #eef1f5; color: #111827; font-family: Arial, Helvetica, sans-serif; }
        .label-sheet { width: 420px; min-height: 590px; margin: 0 auto; border: 2px solid #111827; background: #fff; }
        .label-head { display: flex; align-items: center; justify-content: space-between; padding: 16px; border-bottom: 2px solid #111827; }
        .label-head h1 { margin: 0; font-size: 25px; letter-spacing: 0; }
        .service { border: 2px solid #111827; padding: 6px 10px; font-weight: 800; }
        .label-section { padding: 16px; border-bottom: 2px solid #111827; }
        .label-section span { display: block; margin-bottom: 8px; color: #4b5563; font-size: 11px; font-weight: 800; letter-spacing: 0; text-transform: uppercase; }
        .address { min-height: 128px; font-size: 18px; font-weight: 800; line-height: 1.45; }
        .barcode { display: grid; place-items: center; gap: 10px; padding: 22px 16px; border-bottom: 2px solid #111827; }
        .bars { display: flex; align-items: stretch; justify-content: center; gap: 3px; width: 100%; height: 82px; color: #000; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        .bars i { display: block; width: 0; min-width: 0; border-left: 4px solid #000; }
        .bars i:nth-child(3n) { border-left-width: 8px; }
        .bars i:nth-child(4n) { border-left-width: 2px; }
        .bars i:nth-child(5n) { margin-right: 2px; }
        .tracking { font-size: 20px; font-weight: 900; letter-spacing: 0; }
        .label-grid { display: grid; grid-template-columns: 1fr 1fr; }
        .label-grid div { min-height: 82px; padding: 14px; border-right: 2px solid #111827; border-bottom: 2px solid #111827; }
        .label-grid div:nth-child(even) { border-right: 0; }
        .label-grid strong { display: block; margin-top: 7px; font-size: 17px; }
        .items { padding: 14px 16px; font-size: 12px; }
        .print-actions { display: flex; justify-content: center; gap: 10px; margin: 18px 0 0; }
        .print-actions button, .print-actions a { border: 0; border-radius: 6px; padding: 11px 14px; color: #fff; background: #111827; font-weight: 800; text-decoration: none; cursor: pointer; }
        @media print {
            body { padding: 0; background: #fff; }
            .label-sheet { margin: 0; border-color: #000; }
            .barcode, .bars, .bars i { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .bars i { border-left-color: #000 !important; }
            .print-actions { display: none; }
        }
    </style>
</head>
<body>
    <main class="label-sheet">
        <header class="label-head">
            <h1>ROYAL MAIL</h1>
            <div class="service">TRACKED 24</div>
        </header>
        <section class="label-section">
            <span>Ship to</span>
            <div class="address">
                {{ $order->customer_name }}<br>
                @if ($order->company){{ $order->company }}<br>@endif
                {{ $order->address }}<br>
                @if ($order->address_2){{ $order->address_2 }}<br>@endif
                {{ $order->city }}, {{ $order->state }}<br>
                {{ $order->zip }}<br>
                {{ $order->country }}
            </div>
        </section>
        <section class="barcode">
            <div class="bars" aria-hidden="true">
                @for ($i = 0; $i < 42; $i++)
                    <i></i>
                @endfor
            </div>
            <div class="tracking">{{ $order->tracking_number }}</div>
        </section>
        <section class="label-grid">
            <div><span>Order</span><strong>#{{ $order->order_number }}</strong></div>
            <div><span>Weight</span><strong>Small Parcel</strong></div>
            <div><span>Postcode</span><strong>{{ $order->zip }}</strong></div>
            <div><span>Total</span><strong>&pound;{{ number_format((float) $order->total, 2) }}</strong></div>
        </section>
        <section class="items">
            <strong>Items:</strong>
            {{ $order->items->map(fn ($item) => $item->product_name . ' x ' . $item->quantity)->implode(', ') }}
        </section>
    </main>
    <div class="print-actions">
        <button type="button" onclick="window.print()">Print label</button>
        <a href="{{ route('backend.orders.show', $order) }}">Back to order</a>
    </div>
</body>
</html>
