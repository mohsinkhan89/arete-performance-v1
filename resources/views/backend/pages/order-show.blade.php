@extends('backend.layouts.master')

@section('title', $pageTitle)

@section('body')
    @php
        $trackUrl = route('frontend.track-order', ['order_number' => $order->order_number, 'email' => $order->email]);
        $labelUrl = route('backend.orders.royal-mail-label', $order);
        $statusLabels = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];
        $paymentLabels = [
            'unpaid' => 'Unpaid',
            'paid' => 'Paid',
            'failed' => 'Rejected',
            'refunded' => 'Refunded',
        ];
        $trackingLabels = [
            'placed' => 'Placed',
            'processing' => 'Processing',
            'packed' => 'Packed',
            'dispatched' => 'Dispatched',
            'out_for_delivery' => 'Out For Delivery',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];
    @endphp

    <div class="page-heading compact-heading order-detail-hero">
        <div>
            <span class="dashboard-kicker">Order Workspace</span>
            <h1>{{ $pageTitle }}</h1>
            <p>{{ $order->customer_name }} &middot; &pound;{{ number_format((float) $order->total, 2) }} &middot; Royal Mail ID {{ $order->tracking_number ?: 'Pending' }}</p>
        </div>
        <div class="order-hero-side">
            <div class="order-hero-badges">
                <span class="badge payment-status-{{ $order->payment_status ?? 'unpaid' }}">{{ $paymentLabels[$order->payment_status ?? 'unpaid'] ?? ucfirst($order->payment_status ?? 'unpaid') }}</span>
                <span class="badge muted">{{ $trackingLabels[$order->tracking_status ?? 'placed'] ?? ucfirst($order->tracking_status ?? 'placed') }}</span>
            </div>
            <div class="action-group">
                <a href="{{ route('backend.page', 'orders') }}" title="Back"><i class="fa-solid fa-arrow-left"></i></a>
                <a href="{{ $trackUrl }}" target="_blank" rel="noopener" title="Customer tracking page"><i class="fa-solid fa-truck-fast"></i></a>
                <a href="{{ $labelUrl }}" target="_blank" rel="noopener" title="Print Royal Mail label"><i class="fa-solid fa-tag"></i></a>
<a href="{{ route('backend.orders.edit', $order) }}" title="Edit order"><i class="fa-solid fa-pen"></i></a>
            </div>
        </div>
    </div>

    <div class="order-detail-strip">
        <div><span>Total</span><strong>&pound;{{ number_format((float) $order->total, 2) }}</strong></div>
        <div><span>Items</span><strong>{{ $order->items->sum('quantity') }}</strong></div>
        <div><span>Payment</span><strong>{{ $paymentLabels[$order->payment_status ?? 'unpaid'] ?? ucfirst($order->payment_status ?? 'unpaid') }}</strong></div>
        <div><span>Tracking</span><strong>{{ $trackingLabels[$order->tracking_status ?? 'placed'] ?? ucfirst($order->tracking_status ?? 'placed') }}</strong></div>
    </div>

    <div class="order-workspace">
        <article class="panel order-summary-panel">
            <div class="panel-head">
                <h2>Customer</h2>
                <span class="badge payment-status-{{ $order->payment_status ?? 'unpaid' }}">{{ $paymentLabels[$order->payment_status ?? 'unpaid'] ?? ucfirst($order->payment_status ?? 'unpaid') }}</span>
            </div>
            <div class="order-meta-grid">
                <div><span>Email</span><strong>{{ $order->email }}</strong></div>
                <div><span>Phone</span><strong>{{ $order->phone ?? '-' }}</strong></div>
                <div><span>Post Code</span><strong>{{ $order->zip }}</strong></div>
                <div><span>Royal Mail ID</span><strong>{{ $order->tracking_number }}</strong></div>
                <div><span>Tracking</span><strong>{{ $trackingLabels[$order->tracking_status ?? 'placed'] ?? ucfirst($order->tracking_status ?? 'placed') }}</strong></div>
                <div>
                    <span>Payment Proof</span>
                    <strong>
                        @if ($order->payment_proof)
                            <a href="{{ url($order->payment_proof) }}" target="_blank" rel="noopener">View uploaded proof</a>
                        @else
                            No proof uploaded
                        @endif
                    </strong>
                </div>
                <div><span>Label</span><strong><a href="{{ $labelUrl }}" target="_blank" rel="noopener">Print shipping label</a></strong></div>
                <div class="wide"><span>Address</span><strong>{{ $order->address }}@if($order->address_2), {{ $order->address_2 }}@endif, {{ $order->city }}, {{ $order->state }} {{ $order->zip }}, {{ $order->country }}</strong></div>
                <div class="wide"><span>Notes</span><strong>{{ $order->order_notes ?: '-' }}</strong></div>
                <div class="wide"><span>Update Payment</span>@include('backend.partials.payment-proof-form', ['order' => $order])</div>
            </div>
        </article>

    </div>

    <article class="panel resource-panel order-items-panel">
        <div class="panel-head">
            <h2>Items</h2>
            <span class="badge muted">{{ $order->items->count() }} products</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Product</th><th>SKU</th><th>Qty</th><th>Price</th><th>Total</th></tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>
                                <span class="table-media">
                                    <img src="{{ url($item->product_image ?: 'backend/assets/imgs/product-bottle.png') }}" alt="{{ $item->product_name }}">
                                    {{ $item->product_name }}
                                </span>
                            </td>
                            <td>{{ $item->product_sku }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>&pound;{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td>&pound;{{ number_format((float) $item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr><td colspan="4"><strong>Subtotal</strong></td><td>&pound;{{ number_format((float) $order->subtotal, 2) }}</td></tr>
                    <tr><td colspan="4"><strong>Shipping</strong></td><td>&pound;{{ number_format((float) $order->shipping_total, 2) }}</td></tr>
                    <tr><td colspan="4"><strong>Total</strong></td><td><strong>&pound;{{ number_format((float) $order->total, 2) }}</strong></td></tr>
                </tbody>
            </table>
        </div>
    </article>
@endsection
