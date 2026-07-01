@extends('backend.layouts.master')

@section('title', 'Dashboard')

@section('body')
    <div class="page-heading">
        <h1>Dashboard</h1>
        <p>Welcome back, Admin! Here's what's happening with your store today.</p>
    </div>

    <div class="stats-grid">
        <article class="stat-card">
            <div>
                <span>Inventory Value</span>
                <strong>${{ number_format((float) $inventoryValue, 2) }}</strong>
                <small class="up"><i class="fa-solid fa-boxes-stacked"></i> {{ number_format($totalInventory) }}</small>
                <em>total stock units</em>
            </div>
            <i class="stat-icon fa-solid fa-coins"></i>
            <svg viewBox="0 0 180 42" aria-hidden="true"><path d="M2 33 C22 31 34 18 52 23 S82 34 102 20 S132 17 150 22 S171 17 178 10"/></svg>
        </article>
        <article class="stat-card">
            <div>
                <span>Categories</span>
                <strong>{{ number_format($totalCategories) }}</strong>
                <small class="up"><i class="fa-solid fa-toggle-on"></i> {{ number_format($activeCategories) }}</small>
                <em>{{ number_format($inactiveCategories) }} inactive</em>
            </div>
            <i class="stat-icon fa-solid fa-bag-shopping"></i>
            <svg viewBox="0 0 180 42" aria-hidden="true"><path d="M2 34 C23 33 37 22 55 25 S82 36 103 22 S134 13 150 18 S169 20 178 12"/></svg>
        </article>
        <article class="stat-card">
            <div>
                <span>Total Users</span>
                <strong>{{ number_format($totalUsers) }}</strong>
                <small class="up"><i class="fa-solid fa-user-check"></i> {{ number_format($activeUsers) }}</small>
                <em>active users</em>
            </div>
            <i class="stat-icon fa-solid fa-users"></i>
            <svg viewBox="0 0 180 42" aria-hidden="true"><path d="M2 32 C22 31 34 17 53 24 S84 34 104 18 S134 16 151 20 S171 18 178 9"/></svg>
        </article>
        <article class="stat-card">
            <div>
                <span>Products</span>
                <strong>{{ number_format($totalProducts) }}</strong>
                <small class="up"><i class="fa-solid fa-toggle-on"></i> {{ number_format($activeProducts) }}</small>
                <em>{{ number_format($inactiveProducts) }} inactive</em>
            </div>
            <i class="stat-icon fa-solid fa-cube"></i>
            <svg viewBox="0 0 180 42" aria-hidden="true"><path d="M2 33 C23 32 36 21 55 23 S84 36 104 23 S133 11 150 18 S170 21 178 11"/></svg>
        </article>
    </div>

    <div class="dashboard-grid">
        <article class="panel sales-panel">
            <div class="panel-head">
                <h2>Store Overview</h2>
                <div class="legend"><span class="gold"></span> Products <span></span> Stock</div>
            </div>
            <div class="chart-area">
                <div class="axis">
                    <span>$10K</span><span>$8K</span><span>$6K</span><span>$4K</span><span>$2K</span><span>$0</span>
                </div>
                <div class="line-chart">
                    <div class="tooltip-card">Live Data<strong>{{ number_format($totalProducts) }} items</strong></div>
                    <span class="chart-pin"></span>
                    <svg viewBox="0 0 720 270" preserveAspectRatio="none" aria-hidden="true">
                        <path class="grid-line" d="M0 25H720M0 78H720M0 132H720M0 186H720M0 240H720"/>
                        <path class="line muted" d="M0 232 C18 206 32 178 55 193 S85 174 110 196 S150 183 172 168 S207 183 227 164 S260 183 286 165 S332 161 351 178 S397 144 421 131 S464 164 486 132 S520 143 542 103 S580 150 602 121 S644 140 671 82 S700 90 720 67"/>
                        <path class="line gold" d="M0 188 C22 166 44 155 72 132 S122 158 150 136 S185 89 218 104 S268 110 295 104 S330 102 351 116 S386 102 413 72 S462 33 484 47 S511 95 535 82 S568 89 590 70 S633 90 654 48 S693 24 720 42"/>
                        <path class="pin-line" d="M351 116V240"/>
                    </svg>
                    <div class="x-axis"><span>01 May</span><span>05 May</span><span>10 May</span><span>15 May</span><span>20 May</span><span>25 May</span><span>30 May</span></div>
                </div>
            </div>
        </article>

        <article class="panel status-panel">
            <div class="panel-head">
                <h2>Catalog Status</h2>
                <a href="{{ route('backend.page', 'products') }}">Manage</a>
            </div>
            <div class="status-content">
                <div class="donut">
                    <span>{{ number_format($totalProducts) }}<small>Products</small></span>
                </div>
                <ul class="status-list">
                    <li><b class="pending"></b><span>Active Products</span><em>{{ number_format($activeProducts) }}</em></li>
                    <li><b class="processing"></b><span>Inactive Products</span><em>{{ number_format($inactiveProducts) }}</em></li>
                    <li><b class="shipped"></b><span>Active Categories</span><em>{{ number_format($activeCategories) }}</em></li>
                    <li><b class="delivered"></b><span>Inactive Categories</span><em>{{ number_format($inactiveCategories) }}</em></li>
                    <li><b class="cancelled"></b><span>Stock Units</span><em>{{ number_format($totalInventory) }}</em></li>
                </ul>
            </div>
        </article>
    </div>

    <div class="dashboard-grid bottom-grid">
        <article class="panel orders-panel">
            <div class="panel-head">
                <h2>Recent Products</h2>
                <a href="{{ route('backend.resource.create', ['resource' => 'products']) }}">Add Product</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Product</th><th>Category</th><th>SKU</th><th>Price</th><th>Stock</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($recentProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category?->name ?? '-' }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>${{ number_format((float) $product->price, 2) }}</td>
                                <td>{{ number_format($product->stock) }}</td>
                                <td><span class="badge {{ $product->status === 'active' ? 'green' : 'muted' }}">{{ ucfirst($product->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="empty-cell">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="panel products-panel">
            <div class="panel-head">
                <h2>Recent Categories</h2>
                <a href="{{ route('backend.resource.create', ['resource' => 'categories']) }}">Add Category</a>
            </div>
            <div class="product-list">
                @forelse ($recentCategories as $category)
                    <div class="product-row">
                        <img src="{{ url($category->image ?: 'backend/assets/imgs/product-bottle.png') }}" alt="{{ $category->name }}">
                        <div><strong>{{ $category->name }}</strong><span>{{ $category->products_count }} products</span></div>
                        <em>{{ ucfirst($category->status) }}</em>
                    </div>
                @empty
                    <div class="empty-cell">No categories found.</div>
                @endforelse
            </div>
        </article>
    </div>
@endsection
