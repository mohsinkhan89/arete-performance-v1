@extends('backend.layouts.master')

@section('title', 'Dashboard')

@section('body')
    <div class="dashboard-v2">
        <section class="dashboard-hero-panel">
            <div>
                <span class="dashboard-kicker">Live Database</span>
                <h1>Operations Dashboard</h1>
                <p>Compact overview from orders, order items, products, categories, users, and reviews tables.</p>
            </div>
            <div class="dashboard-quick-actions">
                <a href="{{ route('backend.page', 'orders') }}"><i class="fa-solid fa-clipboard-list"></i> Orders</a>
                <a href="{{ route('backend.page', 'reports') }}"><i class="fa-solid fa-chart-column"></i> Reports</a>
                <a href="{{ route('backend.resource.create', ['resource' => 'products']) }}"><i class="fa-solid fa-plus"></i> Product</a>
            </div>
        </section>

        <section class="compact-stats">
            <article class="compact-stat metric-revenue">
                <span>Total Revenue</span>
                <strong>&pound;{{ number_format((float) $totalRevenue, 2) }}</strong>
                <small>&pound;{{ number_format((float) $averageOrderValue, 2) }} average order</small>
                <i class="fa-solid fa-chart-line"></i>
            </article>
            <article class="compact-stat metric-proof">
                <span>Unpaid Orders</span>
                <strong>{{ number_format($unpaidOrders) }}</strong>
                <small>&pound;{{ number_format((float) $pendingRevenue, 2) }} pending</small>
                <i class="fa-solid fa-hourglass-half"></i>
            </article>
            <article class="compact-stat metric-orders">
                <span>Today Orders</span>
                <strong>{{ number_format($todayOrders) }}</strong>
                <small>{{ number_format($totalOrders) }} all time orders</small>
                <i class="fa-solid fa-calendar-day"></i>
            </article>
            <article class="compact-stat metric-stock">
                <span>Stock Units</span>
                <strong>{{ number_format($totalInventory) }}</strong>
                <small>&pound;{{ number_format((float) $inventoryValue, 2) }} inventory</small>
                <i class="fa-solid fa-boxes-stacked"></i>
            </article>
        </section>

        <section class="dashboard-workbench">
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
                                    <td>@include('backend.partials.payment-proof-form', ['order' => $order])</td>
                                    <td>{{ $order->tracking_number ?: 'Pending label' }}</td>
                                    <td>
                                        <div class="action-group">
                                            <a href="{{ route('backend.orders.show', $order) }}" title="View"><i class="fa-regular fa-eye"></i></a>
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
                    <div><span>Order Items</span><strong>{{ number_format($orderItemsCount ?? 0) }}</strong><small>{{ number_format($reviewsCount ?? 0) }} reviews</small></div>
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
                    <h2>Best Sellers</h2>
                    <a href="{{ route('backend.page', 'reports') }}">Reports</a>
                </div>
                <div class="product-list">
                    @forelse ($topProducts as $product)
                        <div class="product-row">
                            <img src="{{ url($product->product_image ?: 'backend/assets/imgs/product-bottle.png') }}" alt="{{ $product->product_name }}">
                            <div><strong>{{ $product->product_name }}</strong><span>{{ $product->product_sku ?: 'Order item' }}</span></div>
                            <em>{{ number_format((int) $product->sold_quantity) }} sold</em>
                        </div>
                    @empty
                        <div class="empty-cell">No sold products yet.</div>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="dashboard-workbench dashboard-workbench-secondary">
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

            <article class="panel ops-snapshot">
                <div class="panel-head">
                    <h2>Revenue Split</h2>
                    <a href="{{ route('backend.page', 'reports') }}">Reports</a>
                </div>
                <div class="snapshot-grid dashboard-mini-grid">
                    <div><span>Paid</span><strong>&pound;{{ number_format((float) $paidRevenue, 2) }}</strong><small>{{ number_format($paidOrders) }} orders</small></div>
                    <div><span>Pending</span><strong>&pound;{{ number_format((float) $pendingRevenue, 2) }}</strong><small>{{ number_format($unpaidOrders) }} orders</small></div>
                </div>
            </article>
        </section>
    </div>
@endsection
