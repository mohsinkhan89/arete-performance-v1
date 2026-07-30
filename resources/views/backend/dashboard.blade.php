@extends('backend.layouts.master')

@section('title', 'Analytics')

@section('body')
<div class="vx-dashboard">
    <div class="vx-page-head">
        <div><h1>Analytics Dashboard</h1><p>Welcome back, {{ auth()->user()->name ?? 'Admin' }}. Here is what is happening with your store.</p></div>
        <form method="GET" action="{{ route('backend.dashboard') }}">
            <select name="range" onchange="this.form.submit()">
                @foreach ([7 => 'Last 7 days', 30 => 'Last 30 days', 90 => 'Last 90 days', 365 => 'Last year'] as $days => $label)
                    <option value="{{ $days }}" @selected($range === $days)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <section class="vx-grid vx-grid-top">
        <article class="vx-card vx-analytics-hero">
            <div>
                <h2>Store Analytics</h2>
                <p>Total {{ number_format($rangeOrders) }} orders in the selected period</p>
                <div class="vx-hero-metrics">
                    <span><b>{{ number_format($rangeOrders) }}</b>Orders</span>
                    <span><b>&pound;{{ number_format($rangeRevenue, 0) }}</b>Revenue</span>
                    <span><b>{{ number_format($itemsSold) }}</b>Items</span>
                    <span><b>{{ number_format($deliveredOrders) }}</b>Delivered</span>
                </div>
                <a href="{{ route('backend.page', 'reports') }}">View Reports</a>
            </div>
            <i class="fa-solid fa-chart-pie"></i>
        </article>

        <article class="vx-card vx-daily-sales">
            <span>Average Daily Sales</span>
            <p>Total sales in this period</p>
            <strong>&pound;{{ number_format($rangeRevenue / max($range, 1), 2) }}</strong>
            <div class="vx-mini-bars">
                @foreach ($revenueSeries->take(-12) as $value)
                    <i style="height: {{ max(8, ($value / max((float) $revenueSeries->max(), 1)) * 60) }}px"></i>
                @endforeach
            </div>
        </article>

        <article class="vx-card vx-sales-overview">
            <div class="vx-card-head"><div><span>Sales Overview</span><strong>&pound;{{ number_format($rangeRevenue, 1) }}</strong></div><em>+{{ number_format($rangeOrders ? ($deliveredOrders / $rangeOrders) * 100 : 0, 1) }}%</em></div>
            <div class="vx-overview-pair">
                <span><i class="fa-solid fa-cart-shopping"></i><b>{{ number_format($rangeOrders) }}</b><small>Orders</small></span>
                <strong>VS</strong>
                <span><i class="fa-solid fa-eye"></i><b>{{ number_format($totalUsers) }}</b><small>Customers</small></span>
            </div>
            <div class="vx-progress"><i style="width: {{ $rangeOrders ? ($deliveredOrders / $rangeOrders) * 100 : 0 }}%"></i></div>
        </article>
    </section>

    <section class="vx-grid vx-grid-middle">
        <article class="vx-card vx-earning-report">
            <div class="vx-card-head"><div><span>Earning Reports</span><p>Selected period overview</p></div><button type="button"><i class="fa-solid fa-ellipsis-vertical"></i></button></div>
            <div class="vx-earning-total"><strong>&pound;{{ number_format($rangeRevenue, 2) }}</strong><em>{{ number_format($rangeOrders) }} orders</em></div>
            <div class="vx-report-chart"><canvas data-dashboard-chart aria-label="Earning report chart"></canvas></div>
            <script type="application/json" data-dashboard-chart-data>{!! json_encode(['labels' => $chartLabels, 'revenue' => $revenueSeries, 'orders' => $ordersSeries]) !!}</script>
            <div class="vx-report-stats">
                <span><i class="fa-solid fa-pound-sign"></i><small>Revenue</small><b>&pound;{{ number_format($rangeRevenue, 0) }}</b></span>
                <span><i class="fa-solid fa-chart-line"></i><small>Average</small><b>&pound;{{ number_format($rangeOrders ? $rangeRevenue / $rangeOrders : 0, 0) }}</b></span>
                <span><i class="fa-solid fa-receipt"></i><small>Pending</small><b>&pound;{{ number_format($pendingRevenue, 0) }}</b></span>
            </div>
        </article>

        <article class="vx-card vx-support-tracker">
            <div class="vx-card-head"><div><span>Order Tracker</span><p>Last {{ $range }} days</p></div><button type="button"><i class="fa-solid fa-ellipsis-vertical"></i></button></div>
            <div class="vx-tracker-body">
                <div><strong>{{ number_format($rangeOrders) }}</strong><span>Total Orders</span>
                    <p><i class="green"></i>Delivered <b>{{ number_format($deliveredOrders) }}</b></p>
                    <p><i class="orange"></i>Pending <b>{{ number_format($pendingOrders) }}</b></p>
                    <p><i class="purple"></i>Paid <b>{{ number_format($paidOrders) }}</b></p>
                </div>
                @php $completePercent = $rangeOrders ? round(($deliveredOrders / $rangeOrders) * 100) : 0; @endphp
                <div class="vx-radial" style="--percent: {{ $completePercent }}%"><span><b>{{ $completePercent }}%</b>Completed</span></div>
            </div>
        </article>
    </section>

    <section class="vx-grid vx-grid-lists">
        <article class="vx-card vx-country-sales">
            <div class="vx-card-head"><div><span>Top Customers</span><p>Highest spend in selected period</p></div><a href="{{ route('backend.page', 'orders') }}">View all</a></div>
            <div class="vx-list">
                @forelse ($topCustomers->take(6) as $customer)
                    <a href="{{ route('backend.page', ['page' => 'orders', 'q' => $customer['email']]) }}"><i>{{ strtoupper(substr($customer['name'], 0, 2)) }}</i><span><b>{{ $customer['name'] }}</b><small>{{ $customer['orders'] }} orders</small></span><strong>&pound;{{ number_format($customer['spent'], 0) }}</strong></a>
                @empty <div class="empty-cell">No customer data.</div> @endforelse
            </div>
        </article>

        <article class="vx-card vx-total-earning">
            <div class="vx-card-head"><div><span>Total Earning</span><p>Paid and pending revenue</p></div><button type="button"><i class="fa-solid fa-ellipsis-vertical"></i></button></div>
            <div class="vx-earning-ring" style="--percent: {{ $totalRevenue ? ($paidRevenue / $totalRevenue) * 100 : 0 }}%"><div><strong>{{ number_format($totalRevenue ? ($paidRevenue / $totalRevenue) * 100 : 0, 0) }}%</strong><span>Paid</span></div></div>
            <div class="vx-earning-lines">
                <p><i class="fa-solid fa-wallet"></i><span><b>Total Revenue</b><small>Client payments</small></span><strong>&pound;{{ number_format($paidRevenue, 0) }}</strong></p>
                <p><i class="fa-solid fa-arrow-rotate-left"></i><span><b>Pending</b><small>Unpaid orders</small></span><strong>&pound;{{ number_format($pendingRevenue, 0) }}</strong></p>
            </div>
        </article>

        <article class="vx-card vx-campaign-state">
            <div class="vx-card-head"><div><span>Inventory State</span><p>{{ number_format($totalInventory) }} available units</p></div><a href="{{ route('backend.page', 'products') }}">Products</a></div>
            <div class="vx-list compact">
                @forelse ($lowStockProducts as $product)
                    <a href="{{ route('backend.resource.edit', ['resource' => 'products', 'id' => $product->id]) }}"><i class="fa-solid fa-box"></i><span><b>{{ $product->name }}</b><small>{{ $product->category?->name ?? 'Product' }}</small></span><strong>{{ $product->stock }} left</strong></a>
                @empty <div class="empty-cell">No low-stock products.</div> @endforelse
            </div>
        </article>
    </section>

    <section class="vx-grid vx-grid-bottom">
        <article class="vx-card vx-source-visits">
            <div class="vx-card-head"><div><span>Top Selling Products</span><p>Products ranked by units sold</p></div><a href="{{ route('backend.page', 'products') }}">View all</a></div>
            <div class="vx-source-grid">
                @forelse ($topProducts as $product)
                    <a href="{{ $product->product_id ? route('backend.resource.edit', ['resource' => 'products', 'id' => $product->product_id]) : '#' }}"><i class="fa-solid fa-cube"></i><span><b>{{ $product->product_name }}</b><small>{{ $product->sold_quantity }} units sold</small></span><strong>&pound;{{ number_format($product->sold_total, 0) }}</strong></a>
                @empty <div class="empty-cell">No product sales.</div> @endforelse
            </div>
        </article>

        <article class="vx-card vx-recent-orders">
            <div class="vx-card-head"><div><span>Recent Orders</span><p>Latest store transactions</p></div><a href="{{ route('backend.page', 'orders') }}">All orders</a></div>
            <div class="vx-table">
                <div><b>Order</b><b>Customer</b><b>Status</b><b>Total</b></div>
                @forelse ($recentOrders as $order)
                    <a href="{{ route('backend.orders.show', $order) }}"><strong>#{{ $order->order_number }}</strong><span>{{ $order->customer_name }}<small>{{ $order->email }}</small></span><em>{{ Str::headline($order->tracking_status) }}</em><strong>&pound;{{ number_format($order->total, 2) }}</strong></a>
                @empty <div class="empty-cell">No recent orders.</div> @endforelse
            </div>
        </article>
    </section>
</div>
@endsection
