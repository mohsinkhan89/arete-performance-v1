@extends('backend.layouts.master')

@section('title', $pageTitle)

@section('body')
    @php
        $phone = preg_replace('/\D+/', '', $order->phone ?? '');
        $trackUrl = route('frontend.track-order', ['order_number' => $order->order_number, 'email' => $order->email]);
        $proofMessage = "Hi {$order->customer_name},\n\nPlease send/upload your payment proof for Arete Performance order #{$order->order_number}.\nTotal: £" . number_format((float) $order->total, 2) . "\n\nUpload here: {$trackUrl}\n\nOnce submitted, we will verify the payment and process your order.";
        $proofWaUrl = 'https://wa.me/' . $phone . '?text=' . rawurlencode($proofMessage);
        $statusLabels = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];
        $paymentLabels = [
            'unpaid' => 'Unpaid',
            'proof_submitted' => 'Proof Submitted',
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
        $proofExtension = $order->payment_proof ? strtolower(pathinfo($order->payment_proof, PATHINFO_EXTENSION)) : null;
        $proofIsImage = in_array($proofExtension, ['jpg', 'jpeg', 'png', 'webp'], true);
    @endphp

    <div class="page-heading compact-heading">
        <div>
            <h1>{{ $pageTitle }}</h1>
            <p>{{ $order->customer_name }} · £{{ number_format((float) $order->total, 2) }} · {{ $order->created_at?->format('M d, Y') }}</p>
        </div>
        <div class="action-group">
            <a href="{{ route('backend.page', 'orders') }}" title="Back"><i class="fa-solid fa-arrow-left"></i></a>
            <a href="{{ $proofWaUrl }}" target="_blank" rel="noopener" title="Ask for payment proof"><i class="fa-brands fa-whatsapp"></i></a>
            <a href="{{ $trackUrl }}" target="_blank" rel="noopener" title="Customer tracking page"><i class="fa-solid fa-truck-fast"></i></a>
        </div>
    </div>

    <div class="order-workspace">
        <article class="panel order-summary-panel">
            <div class="panel-head">
                <h2>Customer</h2>
                <span class="badge {{ ($order->payment_status ?? 'unpaid') === 'paid' ? 'green' : (($order->payment_status ?? 'unpaid') === 'failed' ? 'red' : 'yellow') }}">{{ $paymentLabels[$order->payment_status ?? 'unpaid'] ?? ucfirst($order->payment_status ?? 'unpaid') }}</span>
            </div>
            <div class="order-meta-grid">
                <div><span>Email</span><strong>{{ $order->email }}</strong></div>
                <div><span>Phone</span><strong>{{ $order->phone ?? '-' }}</strong></div>
                <div><span>Post Code</span><strong>{{ $order->zip }}</strong></div>
                <div><span>Tracking</span><strong>{{ $trackingLabels[$order->tracking_status ?? 'placed'] ?? ucfirst($order->tracking_status ?? 'placed') }}</strong></div>
                <div class="wide"><span>Address</span><strong>{{ $order->address }}@if($order->address_2), {{ $order->address_2 }}@endif, {{ $order->city }}, {{ $order->state }} {{ $order->zip }}, {{ $order->country }}</strong></div>
                <div class="wide"><span>Notes</span><strong>{{ $order->order_notes ?: '-' }}</strong></div>
            </div>
        </article>

        <article class="panel proof-panel">
            <div class="panel-head">
                <h2>Payment Proof</h2>
                @if ($order->payment_proof)
                    <a href="{{ url($order->payment_proof) }}" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i> Open</a>
                @endif
            </div>
            @if ($order->payment_proof)
                <div class="proof-preview">
                    @if ($proofIsImage)
                        <img src="{{ url($order->payment_proof) }}" alt="Payment proof for order {{ $order->order_number }}">
                    @else
                        <div class="proof-file">
                            <i class="fa-regular fa-file-pdf"></i>
                            <strong>{{ strtoupper($proofExtension ?? 'FILE') }} proof uploaded</strong>
                            <a href="{{ url($order->payment_proof) }}" target="_blank" rel="noopener">View file</a>
                        </div>
                    @endif
                </div>
                <p class="proof-time">Submitted {{ $order->payment_proof_submitted_at?->format('M d, Y h:i A') ?? 'recently' }}</p>
            @else
                <div class="proof-empty">
                    <i class="fa-regular fa-file-lines"></i>
                    <strong>No proof submitted yet</strong>
                    <span>Use WhatsApp to ask the customer for payment proof.</span>
                </div>
            @endif
        </article>

        <article class="panel order-decision-panel">
            <div class="panel-head">
                <h2>Payment Decision</h2>
            </div>
            <div class="decision-actions">
                <form action="{{ route('backend.orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="processing">
                    <input type="hidden" name="payment_status" value="paid">
                    <input type="hidden" name="tracking_status" value="{{ $order->tracking_status ?? 'processing' }}">
                    <input type="hidden" name="tracking_number" value="{{ $order->tracking_number }}">
                    <input type="hidden" name="tracking_note" value="{{ $order->tracking_note }}">
                    <input type="hidden" name="admin_note" value="{{ $order->admin_note }}">
                    <button class="decision-btn accept" type="submit"><i class="fa-solid fa-check"></i> Accept proof</button>
                </form>
                <form action="{{ route('backend.orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $order->status }}">
                    <input type="hidden" name="payment_status" value="failed">
                    <input type="hidden" name="tracking_status" value="{{ $order->tracking_status ?? 'placed' }}">
                    <input type="hidden" name="tracking_number" value="{{ $order->tracking_number }}">
                    <input type="hidden" name="tracking_note" value="{{ $order->tracking_note }}">
                    <input type="hidden" name="admin_note" value="{{ $order->admin_note ?: 'Payment proof could not be verified. Please upload a clearer proof.' }}">
                    <button class="decision-btn reject" type="submit"><i class="fa-solid fa-xmark"></i> Reject proof</button>
                </form>
            </div>
            <p>Accept or reject sends an email to the customer automatically.</p>
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
                        <span>Tracking Number</span>
                        <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="Courier tracking number">
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
