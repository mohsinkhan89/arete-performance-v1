@extends('backend.layouts.master')

@section('title', 'Dashboard')

@section('body')
    <div class="dashboard-v2">
        <section class="dashboard-hero-panel">
            <div>
                <span class="dashboard-kicker">Live Database</span>
                <h1>Dashboard</h1>
                <p>Every number below is calculated from orders, products, categories, users, and reviews tables.</p>
            </div>
            <div class="dashboard-quick-actions">
                <a href="{{ route('backend.page', 'orders') }}"><i class="fa-solid fa-clipboard-list"></i> Orders</a>
                <a href="{{ route('backend.page', 'reports') }}"><i class="fa-solid fa-chart-column"></i> Reports</a>
                <a href="{{ route('backend.resource.create', ['resource' => 'products']) }}"><i class="fa-solid fa-plus"></i> Product</a>
            </div>
        </section>

        <section class="compact-stats">
            <article class="compact-stat is-gold">
                <span>Total Revenue</span>
                <strong>&pound;{{ number_format((float) $totalRevenue, 2) }}</strong>
                <small>&pound;{{ number_format((float) $averageOrderValue, 2) }} average order</small>
                <i class="fa-solid fa-chart-line"></i>
            </article>
            <article class="compact-stat is-warning">
                <span>Proofs To Review</span>
                <strong>{{ number_format($proofSubmittedOrders) }}</strong>
                <small>&pound;{{ number_format((float) $proofSubmittedTotal, 2) }} waiting</small>
                <i class="fa-solid fa-receipt"></i>
            </article>
            <article class="compact-stat">
                <span>Today Orders</span>
                <strong>{{ number_format($todayOrders) }}</strong>
                <small>{{ number_format($totalOrders) }} all time orders</small>
                <i class="fa-solid fa-calendar-day"></i>
            </article>
            <article class="compact-stat">
                <span>Stock Units</span>
                <strong>{{ number_format($totalInventory) }}</strong>
                <small>&pound;{{ number_format((float) $inventoryValue, 2) }} inventory</small>
                <i class="fa-solid fa-boxes-stacked"></i>
            </article>
        </section>

        <section class="dashboard-workbench">
            <article class="panel proof-queue-panel">
                <div class="panel-head">
                    <h2>Payment Proof Queue</h2>
                    <a href="{{ route('backend.page', 'reports') }}">View report</a>
                </div>
                <div class="queue-list">
                    @forelse ($recentPaymentProofs as $order)
                        <a class="queue-item" href="{{ route('backend.orders.show', $order) }}">
                            <span class="queue-icon"><i class="fa-solid fa-receipt"></i></span>
                            <div>
                                <strong>#{{ $order->order_number }}</strong>
                                <small>{{ $order->customer_name }} &middot; {{ $order->payment_proof_submitted_at?->diffForHumans() ?? 'recently' }}</small>
                            </div>
                            <b>&pound;{{ number_format((float) $order->total, 2) }}</b>
                        </a>
                    @empty
                        <div class="empty-queue">
                            <i class="fa-solid fa-circle-check"></i>
                            <strong>No proofs waiting</strong>
                            <span>New uploads will appear here.</span>
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="panel orders-panel compact-orders-panel">
                <div class="panel-head">
                    <h2>Recent Orders</h2>
                    <a href="{{ route('backend.page', 'orders') }}">View all</a>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>Order</th><th>Customer</th><th>Total</th><th>Payment</th><th>Royal Mail ID</th><th></th></tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>&pound;{{ number_format((float) $order->total, 2) }}</td>
                                    <td><span class="badge payment-status-{{ $order->payment_status ?? 'unpaid' }}">{{ str_replace('_', ' ', ucfirst($order->payment_status ?? 'unpaid')) }}</span></td>
                                    <td>{{ $order->tracking_number ?: 'Pending label' }}</td>
                                    <td>
                                        @php
                                            $phone = preg_replace('/\D+/', '', $order->phone ?? '');
                                            $trackUrl = route('frontend.track-order', ['order_number' => $order->order_number, 'email' => $order->email]);
                                            $message = "Hi {$order->customer_name},\n\nPlease send/upload your payment proof for Arete Performance order #{$order->order_number}.\nTotal: £" . number_format((float) $order->total, 2) . "\n\nUpload here: {$trackUrl}\n\nOnce submitted, we will verify the payment and process your order.";
                                        @endphp
                                        <div class="action-group">
                                            <a href="{{ route('backend.orders.show', $order) }}" title="View"><i class="fa-regular fa-eye"></i></a>
                                            <a href="https://wa.me/{{ $phone }}?text={{ rawurlencode($message) }}" target="_blank" rel="noopener" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="empty-cell">No orders found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </section>

        <section class="dashboard-workbench dashboard-workbench-secondary">
            <article class="panel ops-snapshot">
                <div class="panel-head">
                    <h2>Database Snapshot</h2>
                    <a href="{{ route('backend.page', 'products') }}">Manage</a>
                </div>
                <div class="snapshot-grid">
                    <div><span>Products</span><strong>{{ number_format($totalProducts) }}</strong><small>{{ number_format($activeProducts) }} active / {{ number_format($inactiveProducts) }} inactive</small></div>
                    <div><span>Categories</span><strong>{{ number_format($totalCategories) }}</strong><small>{{ number_format($activeCategories) }} active / {{ number_format($inactiveCategories) }} inactive</small></div>
                    <div><span>Users</span><strong>{{ number_format($totalUsers) }}</strong><small>{{ number_format($activeUsers) }} active</small></div>
                    <div><span>Reviews</span><strong>{{ number_format($reviewsCount ?? 0) }}</strong><small>customer feedback</small></div>
                </div>
            </article>

            <article class="panel ops-snapshot">
                <div class="panel-head">
                    <h2>Order Status</h2>
                    <a href="{{ route('backend.page', 'orders') }}">Orders</a>
                </div>
                <div class="status-breakdown">
                    @foreach ($trackingBreakdown as $status => $count)
                        <div><span>{{ str_replace('_', ' ', ucfirst($status)) }}</span><strong>{{ number_format($count) }}</strong></div>
                    @endforeach
                </div>
            </article>
        </section>

        <section class="dashboard-workbench dashboard-workbench-secondary">
            <article class="panel ops-snapshot">
                <div class="panel-head">
                    <h2>Payment Status</h2>
                    <a href="{{ route('backend.page', 'reports') }}">Reports</a>
                </div>
                <div class="status-breakdown">
                    @foreach ($paymentBreakdown as $status => $count)
                        <div><span>{{ str_replace('_', ' ', ucfirst($status)) }}</span><strong>{{ number_format($count) }}</strong></div>
                    @endforeach
                </div>
            </article>

            <article class="panel products-panel compact-products-panel">
                <div class="panel-head">
                    <h2>Low Stock</h2>
                    <a href="{{ route('backend.page', 'products') }}">Products</a>
                </div>
                <div class="product-list">
                    @forelse ($lowStockProducts as $product)
                        <div class="product-row">
                            <img src="{{ url($product->image ?: 'backend/assets/imgs/product-bottle.png') }}" alt="{{ $product->name }}">
                            <div><strong>{{ $product->name }}</strong><span>{{ $product->category?->name ?? 'Product' }}</span></div>
                            <em>{{ number_format($product->stock) }} left</em>
                        </div>
                    @empty
                        <div class="empty-cell">No low-stock products.</div>
                    @endforelse
                </div>
            </article>
        </section>
    </div>
@endsection
