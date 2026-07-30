@extends('backend.layouts.master')

@section('title', 'Dashboard')

@section('body')
<div class="business-dashboard">
    <header class="analytics-heading">
        <div>
            <h1>Business Analytics</h1>
            <p>Sales, customers, products and operations in one place</p>
        </div>
        <div>
            <a class="outline-action" href="{{ route('backend.page', 'orders') }}"><i class="fa-regular fa-rectangle-list"></i> All Orders</a>
            <a class="primary-action" href="{{ route('backend.resource.create', ['resource' => 'products']) }}"><i class="fa-solid fa-plus"></i> Product</a>
        </div>
    </header>

    <section class="analytics-filter-card">
        <form method="GET" action="{{ route('backend.dashboard') }}">
            <label>Date range
                <select name="range">
                    @foreach ([7 => 'Last 7 days', 30 => 'Last 30 days', 90 => 'Last 90 days', 365 => 'Last 12 months'] as $days => $label)
                        <option value="{{ $days }}" @selected($range === $days)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label>Order status
                <select name="order_status">
                    <option value="">All statuses</option>
                    @foreach (['paid', 'unpaid', 'processing', 'dispatched', 'delivered', 'cancelled'] as $value)
                        <option value="{{ $value }}" @selected($orderStatusFilter === $value)>{{ Str::headline($value) }}</option>
                    @endforeach
                </select>
            </label>
            <label>Category
                <select name="category">
                    <option value="">All categories</option>
                    @foreach ($filterCategories as $category)
                        <option value="{{ $category->id }}" @selected($categoryFilter === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Channel
                <select name="channel">
                    <option value="">All channels</option>
                    @foreach ($filterChannels as $channel)
                        <option value="{{ $channel }}" @selected($channelFilter === $channel)>{{ Str::headline($channel) }}</option>
                    @endforeach
                </select>
            </label>
            <div class="filter-actions">
                <a href="{{ route('backend.dashboard') }}">Reset</a>
                <button type="submit"><i class="fa-solid fa-filter"></i> Apply filters</button>
            </div>
        </form>
    </section>

    <section class="analytics-kpis">
        <article class="primary-kpi">
            <span>Revenue in selected period</span>
            <strong>&pound;{{ number_format($rangeRevenue, 2) }}</strong>
            <div><small>Orders <b>{{ number_format($rangeOrders) }}</b></small><small>Items sold <b>{{ number_format($itemsSold) }}</b></small></div>
        </article>
        <article><i class="fa-solid fa-chart-line purple"></i><strong>&pound;{{ number_format($rangeOrders ? $rangeRevenue / $rangeOrders : 0, 2) }}</strong><span>Average order</span></article>
        <article><i class="fa-solid fa-circle-check green"></i><strong>{{ number_format($deliveredOrders) }}</strong><span>Delivered</span></article>
        <article><i class="fa-solid fa-clock orange"></i><strong>{{ number_format($pendingOrders) }}</strong><span>Pending</span></article>
    </section>

    <section class="performance-grid">
        <article class="analytics-card sales-performance-card">
            <div class="analytics-card-head"><div><h2>Sales performance</h2><p>Revenue and order volume over time</p></div></div>
            <div class="business-chart-wrap"><canvas data-dashboard-chart aria-label="Sales performance chart"></canvas></div>
            <script type="application/json" data-dashboard-chart-data>{!! json_encode(['labels' => $chartLabels, 'revenue' => $revenueSeries, 'orders' => $ordersSeries]) !!}</script>
            <div class="chart-legend"><span><i class="purple-dot"></i>Revenue (&pound;)</span><span><i class="green-dot"></i>Orders</span></div>
        </article>
        <article class="analytics-card fulfillment-card">
            <div class="analytics-card-head"><div><h2>Order fulfilment</h2><p>Current filtered split</p></div><a href="{{ route('backend.page', 'orders') }}">View orders</a></div>
            @php $fulfilledTotal = max($deliveredOrders + $pendingOrders, 1); $deliveredPercent = round(($deliveredOrders / $fulfilledTotal) * 100, 1); @endphp
            <div class="fulfillment-donut" style="--delivered: {{ $deliveredPercent }}%">
                <div><span>Orders</span><strong>{{ number_format($rangeOrders) }}</strong></div>
            </div>
            <div class="donut-legend"><span><i></i>Delivered</span><span><i></i>Pending</span></div>
        </article>
    </section>

    <section class="ranking-grid">
        <article class="analytics-card ranking-card">
            <div class="analytics-card-head"><div><h2>Top selling products</h2><p>Ranked by units sold</p></div><a href="{{ route('backend.page', 'products') }}">All products <i class="fa-solid fa-arrow-right"></i></a></div>
            <div class="analytics-table">
                <div class="analytics-table-head"><span>#</span><span>Product</span><span>Units</span><span>Revenue</span><span></span></div>
                @forelse ($topProducts as $index => $product)
                    <a href="{{ $product->product_id ? route('backend.resource.edit', ['resource' => 'products', 'id' => $product->product_id]) : route('backend.page', ['page' => 'products', 'q' => $product->product_name]) }}">
                        <b>{{ $index + 1 }}</b><strong>{{ $product->product_name }}</strong><span>{{ number_format($product->sold_quantity) }}</span><span>&pound;{{ number_format($product->sold_total, 2) }}</span><i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                @empty <div class="empty-cell">No product sales in this period.</div> @endforelse
            </div>
        </article>

        <article class="analytics-card ranking-card customer-ranking">
            <div class="analytics-card-head"><div><h2>Top customers</h2><p>Ranked by total spend</p></div><a href="{{ route('backend.page', 'orders') }}">Customer orders <i class="fa-solid fa-arrow-right"></i></a></div>
            <div class="analytics-table">
                <div class="analytics-table-head"><span>#</span><span>Customer</span><span>Orders</span><span>Spent</span><span></span></div>
                @forelse ($topCustomers as $index => $customer)
                    <a href="{{ route('backend.page', ['page' => 'orders', 'q' => $customer['email']]) }}">
                        <b>{{ $index + 1 }}</b><strong>{{ $customer['name'] }}<small>{{ $customer['email'] }}</small></strong><span>{{ number_format($customer['orders']) }}</span><span>&pound;{{ number_format($customer['spent'], 2) }}</span><i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                @empty <div class="empty-cell">No customer sales in this period.</div> @endforelse
            </div>
        </article>
    </section>

    <section class="insight-grid">
        <article class="analytics-card">
            <div class="analytics-card-head"><div><h2>Category performance</h2><p>Revenue contribution by category</p></div><a href="{{ route('backend.page', 'categories') }}">Manage categories</a></div>
            @php $maxCategoryRevenue = max((float) ($categoryPerformance->max('revenue') ?? 0), 1); @endphp
            <div class="bar-performance-list">
                @forelse ($categoryPerformance as $category)
                    <div><span>{{ $category['name'] }}</span><b><i style="width: {{ ($category['revenue'] / $maxCategoryRevenue) * 100 }}%"></i></b><em>&pound;{{ number_format($category['revenue'], 0) }}</em></div>
                @empty <div class="empty-cell">No category performance data.</div> @endforelse
            </div>
        </article>
        <article class="analytics-card">
            <div class="analytics-card-head"><div><h2>Top acquisition channels</h2><p>Channel order and revenue</p></div><a href="{{ route('backend.page', 'reports') }}">View reports</a></div>
            @php $maxChannelRevenue = max((float) ($channelPerformance->max('revenue') ?? 0), 1); @endphp
            <div class="channel-list">
                @forelse ($channelPerformance as $channel)
                    <div><p><strong>{{ $channel['name'] }}</strong><span>{{ number_format($channel['orders']) }} orders &middot; &pound;{{ number_format($channel['revenue'], 2) }}</span></p><b><i style="width: {{ ($channel['revenue'] / $maxChannelRevenue) * 100 }}%"></i></b></div>
                @empty <div class="empty-cell">No channel data in this period.</div> @endforelse
            </div>
        </article>
    </section>

    <section class="analytics-card module-snapshot-card">
        <div class="analytics-card-head"><div><h2>Module snapshot</h2><p>Quick links to operational areas</p></div></div>
        <div class="snapshot-links">
            <a href="{{ route('backend.page', 'products') }}"><i class="fa-solid fa-box"></i><strong>{{ number_format($totalProducts) }}</strong><span>Products</span><small>{{ number_format($activeProducts) }} active</small></a>
            <a href="{{ route('backend.page', 'categories') }}"><i class="fa-solid fa-layer-group"></i><strong>{{ number_format($totalCategories) }}</strong><span>Categories</span><small>Product catalogue</small></a>
            <a href="{{ route('backend.page', 'users') }}"><i class="fa-solid fa-users"></i><strong>{{ number_format($totalUsers) }}</strong><span>Customers</span><small>Unique buyers</small></a>
            <a href="{{ route('backend.page', 'stock-notifications') }}"><i class="fa-solid fa-bell"></i><strong>{{ number_format($unpaidOrders) }}</strong><span>Stock requests</span><small>Pending actions</small></a>
            <a href="{{ route('backend.page', 'reviews') }}"><i class="fa-solid fa-star"></i><strong>{{ number_format($reviewsCount) }}</strong><span>Reviews</span><small>Customer feedback</small></a>
            <a href="{{ route('backend.page', 'reports') }}"><i class="fa-solid fa-chart-column"></i><strong>{{ number_format($totalOrders) }}</strong><span>Reports</span><small>Sales reporting</small></a>
        </div>
    </section>

    <section class="analytics-card recent-orders-card">
        <div class="analytics-card-head"><div><h2>Recent orders</h2><p>Latest activity matching the filters</p></div><a href="{{ route('backend.page', 'orders') }}">View full order report</a></div>
        <div class="recent-business-orders">
            <div class="recent-business-head"><span>Order</span><span>Customer</span><span>Channel</span><span>Date</span><span>Status</span><span>Total</span></div>
            @forelse ($recentOrders as $order)
                <a href="{{ route('backend.orders.show', $order) }}"><strong>#{{ $order->order_number }}</strong><span>{{ $order->customer_name }}<small>{{ $order->email }}</small></span><span>{{ Str::headline($order->payment_method) }}</span><span>{{ $order->created_at?->format('d M Y, H:i') }}</span><span><b class="order-status-{{ $order->tracking_status }}">{{ Str::headline($order->tracking_status) }}</b></span><strong>&pound;{{ number_format($order->total, 2) }}</strong></a>
            @empty <div class="empty-cell">No recent orders match these filters.</div> @endforelse
        </div>
    </section>

    <footer class="dashboard-footer"><span>Copyright &copy; {{ date('Y') }} Arete Performance. All rights reserved.</span><span>Operations dashboard</span></footer>
</div>
@endsection
