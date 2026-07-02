@extends('backend.layouts.master')

@section('title', $pageTitle)

@section('body')
    <div class="page-heading">
        <h1>{{ $pageTitle }}</h1>
        <p>Complete order details and customer information.</p>
    </div>

    <article class="panel resource-panel">
        <div class="panel-head">
            <h2>Order Details</h2>
            <a href="{{ route('backend.page', 'orders') }}"><i class="fa-solid fa-arrow-left"></i> Back to orders</a>
        </div>

        <div class="order-view-grid">
            <div><span>Customer</span><strong>{{ $order->customer_name }}</strong></div>
            <div><span>Email</span><strong>{{ $order->email }}</strong></div>
            <div><span>Phone</span><strong>{{ $order->phone ?? '-' }}</strong></div>
            <div><span>Post Code</span><strong>{{ $order->zip }}</strong></div>
            <div class="wide"><span>Address</span><strong>{{ $order->address }}@if($order->address_2), {{ $order->address_2 }}@endif, {{ $order->city }}, {{ $order->state }} {{ $order->zip }}, {{ $order->country }}</strong></div>
            <div><span>Payment</span><strong>{{ ucfirst($order->payment_method) }}</strong></div>
            <div><span>Shipping</span><strong>{{ ucfirst($order->shipping_method) }}</strong></div>
            <div><span>Status</span><strong><span class="badge green">{{ ucfirst($order->status) }}</span></strong></div>
            <div class="wide"><span>Order Notes</span><strong>{{ $order->order_notes ?: '-' }}</strong></div>
        </div>
    </article>

    <article class="panel resource-panel">
        <div class="panel-head">
            <h2>Items</h2>
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
                            <td>£{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td>£{{ number_format((float) $item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr><td colspan="4"><strong>Subtotal</strong></td><td>£{{ number_format((float) $order->subtotal, 2) }}</td></tr>
                    <tr><td colspan="4"><strong>Shipping</strong></td><td>£{{ number_format((float) $order->shipping_total, 2) }}</td></tr>
                    <tr><td colspan="4"><strong>Total</strong></td><td><strong>£{{ number_format((float) $order->total, 2) }}</strong></td></tr>
                </tbody>
            </table>
        </div>
    </article>
@endsection
