@extends('backend.layouts.master')

@section('title', $pageTitle)

@section('body')
    <div class="page-heading">
        <h1>{{ $pageTitle }}</h1>
        <p>Manage {{ strtolower($pageTitle) }} from your Arete Performance admin panel.</p>
    </div>

    @if (in_array($page, ['products', 'categories', 'users', 'reviews', 'orders', 'stock-notifications'], true))
        @php
            $moduleStatuses = match ($page) {
                'orders' => ['paid' => 'Paid', 'unpaid' => 'Unpaid', 'processing' => 'Processing', 'dispatched' => 'Dispatched', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'],
                'stock-notifications' => ['pending' => 'Pending', 'notified' => 'Notified'],
                default => ['active' => 'Active', 'inactive' => 'Inactive'],
            };
        @endphp
        <section class="module-filter-bar">
            <form method="GET" action="{{ route('backend.page', $page) }}">
                <div class="module-filter-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search" name="q" value="{{ $search }}" placeholder="Search {{ strtolower($pageTitle) }}...">
                </div>
                <select name="status" aria-label="Filter by status">
                    <option value="">All statuses</option>
                    @foreach ($moduleStatuses as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit"><i class="fa-solid fa-filter"></i> Apply</button>
                @if ($search !== '' || $status !== '')
                    <a href="{{ route('backend.page', $page) }}"><i class="fa-solid fa-xmark"></i> Clear</a>
                @endif
            </form>
            <nav aria-label="{{ $pageTitle }} quick filters">
                <a class="{{ $status === '' ? 'active' : '' }}" href="{{ route('backend.page', $page) }}">All</a>
                @foreach ($moduleStatuses as $value => $label)
                    <a class="{{ $status === $value ? 'active' : '' }}" href="{{ route('backend.page', ['page' => $page, 'status' => $value]) }}">{{ $label }}</a>
                @endforeach
            </nav>
        </section>
    @endif

    @if ($page === 'reports')
        <div class="stats-grid">
            <article class="stat-card">
                <div>
                    <span>Paid Orders</span>
                    <strong>{{ number_format($reportStats['paid_count']) }}</strong>
                    <small class="up"><i class="fa-solid fa-check-circle"></i> £{{ number_format((float) $reportStats['paid_total'], 2) }}</small>
                    <em>payment received</em>
                </div>
                <i class="stat-icon fa-solid fa-wallet"></i>
                <svg viewBox="0 0 180 42" aria-hidden="true"><path d="M2 33 C22 31 34 18 52 23 S82 34 102 20 S132 17 150 22 S171 17 178 10"/></svg>
            </article>
            <article class="stat-card">
                <div>
                    <span>Unpaid Orders</span>
                    <strong>{{ number_format($reportStats['unpaid_count']) }}</strong>
                    <small class="down"><i class="fa-solid fa-clock"></i> £{{ number_format((float) $reportStats['unpaid_total'], 2) }}</small>
                    <em>payment pending</em>
                </div>
                <i class="stat-icon fa-solid fa-hourglass-half"></i>
                <svg viewBox="0 0 180 42" aria-hidden="true"><path d="M2 32 C22 31 34 17 53 24 S84 34 104 18 S134 16 151 20 S171 18 178 9"/></svg>
            </article>
            <article class="stat-card">
                <div>
                    <span>Total Orders</span>
                    <strong>{{ number_format($reportStats['orders']) }}</strong>
                    <small class="down"><i class="fa-solid fa-ban"></i> {{ number_format($reportStats['cancelled_count']) }}</small>
                    <em>cancelled orders</em>
                </div>
                <i class="stat-icon fa-solid fa-chart-column"></i>
                <svg viewBox="0 0 180 42" aria-hidden="true"><path d="M2 33 C23 32 36 21 55 23 S84 36 104 23 S133 11 150 18 S170 21 178 11"/></svg>
            </article>
        </div>

        <article class="panel resource-panel resource-panel-polished resource-reports">
            <div class="panel-head">
                <div>
                    <span class="eyebrow">{{ $reportStats['orders'] }} orders</span>
                    <h2>Payment Report</h2>
                </div>
                <a href="{{ route('backend.page', 'orders') }}"><i class="fa-solid fa-clipboard-list"></i> Orders</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Order</th><th>Customer</th><th>Total</th><th>Payment</th><th>Tracking</th><th>Created</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($reportStats['recent'] as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->customer_name }}</td>
                                <td>£{{ number_format((float) $order->total, 2) }}</td>
                                <td><span class="badge payment-status-{{ $order->payment_status ?? 'unpaid' }}">{{ str_replace('_', ' ', ucfirst($order->payment_status ?? 'unpaid')) }}</span></td>
                                <td>{{ str_replace('_', ' ', ucfirst($order->tracking_status ?? 'placed')) }}</td>
                                <td>{{ $order->created_at?->format('M d, Y') }}</td>
                                <td><div class="action-group"><a href="{{ route('backend.orders.show', $order) }}" title="View"><i class="fa-regular fa-eye"></i></a></div></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="empty-cell">No report data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    @elseif (in_array($page, ['products', 'categories', 'users', 'reviews', 'orders', 'stock-notifications'], true))
        @php
            $canManage = $page !== 'users' || $canManageUsers;
        @endphp
        <article class="panel resource-panel resource-panel-polished resource-{{ $page }} {{ $page === 'orders' ? 'compact-orders-resource' : '' }}">
            <div class="panel-head">
                <div>
                    <span class="eyebrow">{{ $records->total() ?? 0 }} records</span>
                    <h2>{{ $pageTitle }} Table</h2>
                    @if (! empty($search))
                        <small>Filtered by "{{ $search }}"</small>
                    @endif
                </div>
                @if ($canManage && in_array($page, ['products', 'categories', 'users', 'reviews'], true))
                    <a href="{{ route('backend.resource.create', ['resource' => $page]) }}"><i class="fa-solid fa-plus"></i> Add New</a>
                @endif
            </div>

            <div class="table-wrap">
                @if ($page === 'products')
                    <table>
                        <thead>
                            <tr><th>Product</th><th>Category</th><th>SKU</th><th>Price</th><th>Reviews</th><th>Status</th><th>Bestseller</th><th>Stock</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $product)
                                <tr>
                                    <td>
                                        <span class="table-media">
                                            <img src="{{ url($product->image ?: 'backend/assets/imgs/product-bottle.png') }}" alt="{{ $product->name }}">
                                            {{ $product->name }}
                                        </span>
                                    </td>
                                    <td>{{ $product->category?->name ?? '-' }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>£{{ number_format((float) $product->price, 2) }}</td>
                                    <td>{{ number_format($product->reviews_count) }}</td>
                                    <td>
                                        <form action="{{ route('backend.resource.status', ['resource' => 'products', 'id' => $product->id]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="status-toggle {{ $product->status === 'active' ? 'is-active' : 'is-inactive' }}" type="submit" title="Toggle status">
                                                <i class="fa-solid {{ $product->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                {{ ucfirst($product->status) }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <form action="{{ route('backend.products.toggle-field', ['product' => $product->id, 'field' => 'is_bestseller']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="status-toggle {{ $product->is_bestseller ? 'is-active' : 'is-inactive' }}" type="submit" title="Toggle bestseller">
                                                <i class="fa-solid {{ $product->is_bestseller ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                {{ $product->is_bestseller ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <form action="{{ route('backend.products.toggle-field', ['product' => $product->id, 'field' => 'stock']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="status-toggle {{ $product->stock > 0 ? 'is-active' : 'is-inactive' }}" type="submit" title="Toggle stock">
                                                <i class="fa-solid {{ $product->stock > 0 ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                {{ $product->stock > 0 ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <a href="{{ route('backend.resource.edit', ['resource' => 'products', 'id' => $product->id]) }}" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                            <form action="{{ route('backend.resource.destroy', ['resource' => 'products', 'id' => $product->id]) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="empty-cell">No products found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @elseif ($page === 'categories')
                    <table>
                        <thead>
                            <tr><th>Image</th><th>Name</th><th>Slug</th><th>Products</th><th>Sort</th><th>Status</th><th>Created</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $category)
                                <tr>
                                    <td><img class="category-thumb" src="{{ url($category->image ?: 'backend/assets/imgs/product-bottle.png') }}" alt="{{ $category->name }}"></td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->slug }}</td>
                                    <td>{{ $category->products_count }}</td>
                                    <td>{{ $category->sort_order }}</td>
                                    <td>
                                        <form action="{{ route('backend.resource.status', ['resource' => 'categories', 'id' => $category->id]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="status-toggle {{ $category->status === 'active' ? 'is-active' : 'is-inactive' }}" type="submit" title="Toggle status">
                                                <i class="fa-solid {{ $category->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                {{ ucfirst($category->status) }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>{{ $category->created_at?->format('M d, Y') }}</td>
                                    <td>
                                        <div class="action-group">
                                            <a href="{{ route('backend.resource.edit', ['resource' => 'categories', 'id' => $category->id]) }}" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                            <form action="{{ route('backend.resource.destroy', ['resource' => 'categories', 'id' => $category->id]) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="empty-cell">No categories found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @elseif ($page === 'users')
                    <table>
                        <thead>
                            <tr><th>User</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Joined</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?? '-' }}</td>
                                    <td>{{ ucfirst($user->role ?? 'user') }}</td>
                                    <td>
                                        @if ($canManageUsers && auth()->id() !== $user->id)
                                            <form action="{{ route('backend.resource.status', ['resource' => 'users', 'id' => $user->id]) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button class="status-toggle {{ ($user->status ?? 'active') === 'active' ? 'is-active' : 'is-inactive' }}" type="submit" title="Toggle status">
                                                    <i class="fa-solid {{ ($user->status ?? 'active') === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                    {{ ucfirst($user->status ?? 'active') }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge {{ ($user->status ?? 'active') === 'active' ? 'green' : 'red' }}">{{ ucfirst($user->status ?? 'active') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at?->format('M d, Y') }}</td>
                                    <td>
                                        @if ($canManageUsers)
                                            <div class="action-group">
                                                <a href="{{ route('backend.resource.edit', ['resource' => 'users', 'id' => $user->id]) }}" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                                @if (auth()->id() !== $user->id)
                                                    <form action="{{ route('backend.resource.destroy', ['resource' => 'users', 'id' => $user->id]) }}" method="POST" onsubmit="return confirm('Delete this user?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        @else
                                            <span class="view-only"><i class="fa-regular fa-eye"></i> View only</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="empty-cell">No users found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @elseif ($page === 'orders')
                    @php
                        $orderRows = collect($records->items());
                        $paidCount = $orderRows->where('payment_status', 'paid')->count();
                        $proofCount = $orderRows->filter(fn ($order) => ! empty($order->payment_proof))->count();
                        $unpaidCount = $orderRows->where('payment_status', 'unpaid')->count();
                        $pageTotal = $orderRows->sum(fn ($order) => (float) $order->total);
                    @endphp

                    <div class="orders-command-bar">
                        <div>
                            <span class="dashboard-kicker">Orders</span>
                            <strong>{{ number_format($records->total()) }} total orders</strong>
                            <small>{{ $records->count() }} showing on this page</small>
                        </div>
                        <div class="orders-mini-stats">
                            <span><i class="fa-solid fa-sterling-sign"></i> £{{ number_format($pageTotal, 2) }}</span>
                            <span><i class="fa-solid fa-circle-check"></i> {{ $paidCount }} paid</span>
                            <span><i class="fa-solid fa-receipt"></i> {{ $proofCount }} proof</span>
                            <span><i class="fa-solid fa-clock"></i> {{ $unpaidCount }} unpaid</span>
                        </div>
                    </div>

                    <table class="orders-table-compact orders-table-modern">
                        <thead>
                            <tr><th>Order</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Tracking</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $order)
                                <tr>
                                    <td class="order-code-cell">
                                        <strong>{{ $order->order_number }}</strong>
                                        <small class="table-subtext">{{ $order->created_at?->format('M d, Y') }}</small>
                                    </td>
                                    <td class="order-customer-cell">
                                        <strong>{{ $order->customer_name }}</strong>
                                        <small class="table-subtext">{{ $order->email }}</small>
                                    </td>
                                    <td><span class="orders-count-pill">{{ $order->items_count }}</span></td>
                                    <td class="order-total-cell">£{{ number_format((float) $order->total, 2) }}</td>
                                    <td>
                                        <span class="badge payment-status-{{ $order->payment_status ?? 'unpaid' }}">{{ str_replace('_', ' ', ucfirst($order->payment_status ?? 'unpaid')) }}</span>
                                    </td>
                                    <td>
                                        <strong class="tracking-state">{{ str_replace('_', ' ', ucfirst($order->tracking_status ?? 'placed')) }}</strong>
                                        <small class="table-subtext">{{ $order->tracking_number ?: 'Label pending' }}</small>
                                    </td>
                                    <td>
                                        <div class="action-group order-row-actions">
                                            <a href="{{ route('backend.orders.show', $order) }}" title="View"><i class="fa-regular fa-eye"></i></a>
                                            @include('backend.partials.payment-proof-button', ['order' => $order])
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="empty-cell">No orders found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @elseif ($page === 'stock-notifications')
                    <table>
                        <thead>
                            <tr><th>Customer</th><th>Product</th><th>Qty</th><th>Message</th><th>Status</th><th>Requested</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $request)
                                <tr>
                                    <td>
                                        <strong>{{ $request->customer_name }}</strong>
                                        <small class="table-subtext">{{ $request->email }}</small>
                                        <small class="table-subtext">{{ $request->phone ?: '-' }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $request->product?->name ?? 'Deleted product' }}</strong>
                                        <small class="table-subtext">{{ $request->product?->sku ?? '-' }}</small>
                                    </td>
                                    <td>{{ $request->quantity }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($request->message ?: '-', 70) }}</td>
                                    <td>
                                        <span class="badge {{ $request->status === 'notified' ? 'green' : 'payment-status-unpaid' }}">
                                            {{ str_replace('_', ' ', ucfirst($request->status)) }}
                                        </span>
                                        @if ($request->notified_at)
                                            <small class="table-subtext">{{ $request->notified_at->format('M d, Y') }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at?->format('M d, Y') }}</td>
                                    <td>
                                        @if ($request->status === 'notified')
                                            <span class="view-only"><i class="fa-solid fa-check"></i> Sent</span>
                                        @elseif ($request->product)
                                            <form action="{{ route('backend.stock-notifications.notify', $request) }}" method="POST">
                                                @csrf
                                                <button class="status-toggle is-active" type="submit" title="Notify customer">
                                                    <i class="fa-solid fa-paper-plane"></i>
                                                    Notify
                                                </button>
                                            </form>
                                        @else
                                            <span class="view-only"><i class="fa-regular fa-eye"></i> Product missing</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="empty-cell">No stock requests found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <table>
                        <thead>
                            <tr><th>Customer</th><th>Product</th><th>Rating</th><th>Review</th><th>Status</th><th>Created</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $review)
                                <tr>
                                    <td>
                                        <span class="table-media">
                                            <img src="{{ url($review->avatar ?: 'frontend/assets/images/testimonials/miker.png') }}" alt="{{ $review->customer_name }}">
                                            {{ $review->customer_name }}
                                        </span>
                                    </td>
                                    <td>{{ $review->product?->name ?? '-' }}</td>
                                    <td><span class="rating-stars">{{ str_repeat('★', $review->rating) }}</span></td>
                                    <td>{{ \Illuminate\Support\Str::limit($review->comment, 70) }}</td>
                                    <td>
                                        <form action="{{ route('backend.resource.status', ['resource' => 'reviews', 'id' => $review->id]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="status-toggle {{ $review->status === 'active' ? 'is-active' : 'is-inactive' }}" type="submit" title="Toggle status">
                                                <i class="fa-solid {{ $review->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                {{ ucfirst($review->status) }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>{{ $review->created_at?->format('M d, Y') }}</td>
                                    <td>
                                        <div class="action-group">
                                            <a href="{{ route('backend.resource.edit', ['resource' => 'reviews', 'id' => $review->id]) }}" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                            <form action="{{ route('backend.resource.destroy', ['resource' => 'reviews', 'id' => $review->id]) }}" method="POST" onsubmit="return confirm('Delete this review?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="empty-cell">No reviews found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="pagination-row">
                {{ $records->links() }}
            </div>
        </article>
    @endif
@endsection

