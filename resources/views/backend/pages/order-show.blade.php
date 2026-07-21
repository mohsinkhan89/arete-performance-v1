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

        <article class="panel order-update-panel">
            <div class="panel-head">
                <h2>Update Tracking</h2>
            </div>
            <form class="admin-order-form compact-order-form" action="{{ route('backend.orders.update', $order) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-grid">
                    <label>
                        <span>Customer Name</span>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" placeholder="Customer name">
                    </label>
                    <label>
                        <span>Email</span>
                        <input type="email" name="email" value="{{ old('email', $order->email) }}" placeholder="Customer email">
                    </label>
                    <label>
                        <span>Phone</span>
                        <input type="text" name="phone" value="{{ old('phone', $order->phone) }}" placeholder="Phone">
                    </label>
                    <label>
                        <span>Post Code</span>
                        <input type="text" name="zip" value="{{ old('zip', $order->zip) }}" placeholder="Post code">
                    </label>
                    <label class="form-wide">
                        <span>Address</span>
                        <input type="text" name="address" value="{{ old('address', $order->address) }}" placeholder="Address line 1">
                    </label>
                    <label>
                        <span>Address 2</span>
                        <input type="text" name="address_2" value="{{ old('address_2', $order->address_2) }}" placeholder="Address line 2">
                    </label>
                    <label>
                        <span>City</span>
                        <input type="text" name="city" value="{{ old('city', $order->city) }}" placeholder="City">
                    </label>
                    <label>
                        <span>County/State</span>
                        <input type="text" name="state" value="{{ old('state', $order->state) }}" placeholder="County or state">
                    </label>
                    <label>
                        <span>Order Status</span>
                        <select name="status" required>
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>Payment Status</span>
                        <select name="payment_status" required>
                            @foreach ($paymentLabels as $value => $label)
                                <option value="{{ $value }}" @selected(($order->payment_status ?? 'unpaid') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>Tracking Status</span>
                        <select name="tracking_status" required>
                            @foreach ($trackingLabels as $value => $label)
                                <option value="{{ $value }}" @selected(($order->tracking_status ?? 'placed') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>Royal Mail ID</span>
                        <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="Royal Mail tracking ID">
                    </label>
                    <label class="form-wide">
                        <span>Customer Tracking Note</span>
                        <textarea name="tracking_note" rows="3" placeholder="Visible on customer tracking page">{{ old('tracking_note', $order->tracking_note) }}</textarea>
                    </label>
                    <label class="form-wide">
                        <span>Admin Note</span>
                        <textarea name="admin_note" rows="3" placeholder="Internal note">{{ old('admin_note', $order->admin_note) }}</textarea>
                    </label>
                </div>
                <div class="form-actions">
                    <button type="submit"><i class="fa-solid fa-floppy-disk"></i> Save update</button>
                </div>
            </form>
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
