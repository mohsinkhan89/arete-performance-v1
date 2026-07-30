@extends('backend.layouts.master')

@section('title', $pageTitle)

@section('body')
    @if (! in_array($page, ['users', 'products', 'categories', 'reviews', 'reports'], true))
        <div class="page-heading">
            <h1>{{ $pageTitle }}</h1>
            <p>Manage {{ strtolower($pageTitle) }} from your Arete Performance admin panel.</p>
        </div>
    @endif

    @if ($page === 'reports')
        <section class="order-vx-insights order-vx-insights-standalone">
            <div class="order-vx-stats report-vx-stats">
                <article>
                    <i class="fa-solid fa-chart-column purple"></i>
                    <span>Total Orders</span>
                    <strong>{{ number_format($reportStats['orders']) }}</strong>
                    <small>{{ number_format($reportStats['cancelled_count']) }} cancelled</small>
                </article>
                <article>
                    <i class="fa-solid fa-wallet green"></i>
                    <span>Paid Orders</span>
                    <strong>{{ number_format($reportStats['paid_count']) }}</strong>
                    <small>£{{ number_format((float) $reportStats['paid_total'], 2) }} received</small>
                </article>
                <article>
                    <i class="fa-solid fa-clock orange"></i>
                    <span>Unpaid Orders</span>
                    <strong>{{ number_format($reportStats['unpaid_count']) }}</strong>
                    <small>£{{ number_format((float) $reportStats['unpaid_total'], 2) }} pending</small>
                </article>
            </div>
        </section>

        <article class="panel resource-panel resource-panel-polished resource-reports crud-vx-list report-vx-list">
            <div class="panel-head">
                <form class="vx-users-toolbar crud-vx-toolbar" method="GET" action="{{ route('backend.page', 'reports') }}">
                    <select name="per_page" aria-label="Rows per page">
                        <option>10</option>
                    </select>
                    <div>
                        <a class="vx-add-record" href="{{ route('backend.page', 'orders') }}"><i class="fa-solid fa-clipboard-list"></i> All Orders</a>
                    </div>
                </form>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th><input type="checkbox" aria-label="Select all report rows"></th><th>Order</th><th>Customer</th><th>Total</th><th>Payment</th><th>Tracking</th><th>Created</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($reportStats['recent'] as $order)
                            <tr>
                                <td><input type="checkbox" aria-label="Select {{ $order->order_number }}"></td>
                                <td><strong>{{ $order->order_number }}</strong><small class="table-subtext">{{ $order->created_at?->format('M d, Y') }}</small></td>
                                <td><strong>{{ $order->customer_name }}</strong><small class="table-subtext">{{ $order->email }}</small></td>
                                <td>£{{ number_format((float) $order->total, 2) }}</td>
                                <td><span class="badge payment-status-{{ $order->payment_status ?? 'unpaid' }}">{{ str_replace('_', ' ', ucfirst($order->payment_status ?? 'unpaid')) }}</span></td>
                                <td>{{ str_replace('_', ' ', ucfirst($order->tracking_status ?? 'placed')) }}</td>
                                <td>{{ $order->created_at?->format('M d, Y') }}</td>
                                <td>
                                    <div class="vx-row-actions">
                                        <button type="button"
                                            data-report-modal-open
                                            data-order="{{ $order->order_number }}"
                                            data-customer="{{ $order->customer_name }}"
                                            data-email="{{ $order->email }}"
                                            data-total="£{{ number_format((float) $order->total, 2) }}"
                                            data-payment="{{ str_replace('_', ' ', ucfirst($order->payment_status ?? 'unpaid')) }}"
                                            data-tracking="{{ str_replace('_', ' ', ucfirst($order->tracking_status ?? 'placed')) }}"
                                            data-created="{{ $order->created_at?->format('M d, Y H:i') }}"
                                            data-view-url="{{ route('backend.orders.show', $order) }}"
                                            title="View"><i class="fa-regular fa-eye"></i></button>
                                        @include('backend.partials.payment-proof-button', ['order' => $order])
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="empty-cell">No report data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <div class="admin-modal user-editor-modal report-view-modal" data-report-view-modal aria-hidden="true">
            <div class="admin-modal-backdrop" data-report-modal-close></div>
            <section class="admin-modal-dialog vx-user-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="reportModalTitle">
                <button class="admin-modal-close" type="button" data-report-modal-close aria-label="Close report modal"><i class="fa-solid fa-xmark"></i></button>
                <div class="admin-modal-head">
                    <span><i class="fa-solid fa-chart-line"></i></span>
                    <div>
                        <h2 id="reportModalTitle" data-report-order>Order Report</h2>
                        <p data-report-summary>Payment and fulfilment snapshot.</p>
                    </div>
                </div>
                <div class="report-modal-body">
                    <div class="vx-user-detail-card">
                        <span><b>Customer</b><span data-report-customer></span></span>
                        <span><b>Email</b><span data-report-email></span></span>
                        <span><b>Total</b><span data-report-total></span></span>
                        <span><b>Payment</b><span data-report-payment></span></span>
                        <span><b>Tracking</b><span data-report-tracking></span></span>
                        <span><b>Created</b><span data-report-created></span></span>
                    </div>
                    <div class="vx-user-modal-actions">
                        <button type="button" data-report-modal-close>Close</button>
                        <a class="vx-add-record" href="#" data-report-view-url><i class="fa-regular fa-eye"></i> Open Order</a>
                    </div>
                </div>
            </section>
        </div>
    @elseif (in_array($page, ['products', 'categories', 'users', 'reviews', 'orders', 'stock-notifications'], true))
        @php
            $canManage = $page !== 'users' || $canManageUsers;
        @endphp

        @if ($page === 'orders')
            @php
                $orderNarrowLinks = [
                    '' => ['All orders', $orderStats['total'] ?? 0],
                    'paid' => ['Paid', $orderStats['paid'] ?? 0],
                    'unpaid' => ['Unpaid', $orderStats['unpaid'] ?? 0],
                    'processing' => ['Processing', null],
                    'delivered' => ['Delivered', $orderStats['delivered'] ?? 0],
                    'cancelled' => ['Cancelled', null],
                ];
            @endphp

            <section class="order-vx-insights order-vx-insights-standalone">
                <div class="order-vx-stats">
                    <article>
                        <i class="fa-solid fa-cart-shopping purple"></i>
                        <span>Total Orders</span>
                        <strong>{{ number_format($orderStats['total'] ?? 0) }}</strong>
                        <small>£{{ number_format((float) ($orderStats['revenue'] ?? 0), 2) }} total value</small>
                    </article>
                    <article>
                        <i class="fa-solid fa-wallet green"></i>
                        <span>Paid Revenue</span>
                        <strong>£{{ number_format((float) ($orderStats['paidRevenue'] ?? 0), 2) }}</strong>
                        <small>{{ number_format($orderStats['paid'] ?? 0) }} paid orders</small>
                    </article>
                    <article>
                        <i class="fa-solid fa-clock orange"></i>
                        <span>Pending Flow</span>
                        <strong>{{ number_format($orderStats['pending'] ?? 0) }}</strong>
                        <small>{{ number_format($orderStats['unpaid'] ?? 0) }} unpaid</small>
                    </article>
                    <article>
                        <i class="fa-solid fa-truck-fast cyan"></i>
                        <span>Delivered</span>
                        <strong>{{ number_format($orderStats['delivered'] ?? 0) }}</strong>
                        <small>{{ number_format($orderStats['proofs'] ?? 0) }} proofs uploaded</small>
                    </article>
                </div>
                <nav class="order-vx-narrow" aria-label="Narrow orders">
                    @foreach ($orderNarrowLinks as $value => [$label, $count])
                        <a class="{{ ($status ?? '') === $value ? 'active' : '' }}" href="{{ route('backend.page', array_filter(['page' => 'orders', 'status' => $value], fn ($item) => $item !== '' && $item !== null)) }}">
                            <span>{{ $label }}</span>
                            @if (! is_null($count))
                                <b>{{ number_format($count) }}</b>
                            @endif
                        </a>
                    @endforeach
                </nav>
            </section>
        @endif

        <article class="panel resource-panel resource-panel-polished resource-{{ $page }} {{ in_array($page, ['products', 'categories', 'reviews', 'orders'], true) ? 'crud-vx-list' : '' }} {{ $page === 'orders' ? 'order-vx-list' : '' }}">
            <div class="panel-head">
                @if (in_array($page, ['products', 'categories', 'reviews'], true))
                    <form class="vx-users-toolbar crud-vx-toolbar" method="GET" action="{{ route('backend.page', $page) }}">
                        <input type="hidden" name="status" value="{{ $status ?? '' }}">
                        @if ($page === 'products')
                            <input type="hidden" name="category" value="{{ $categoryFilter ?? '' }}">
                        @endif
                        <select name="per_page" aria-label="Rows per page">
                            <option>10</option>
                        </select>
                        <div>
                            @if ($canManage && in_array($page, ['products', 'categories', 'reviews'], true))
                                <a class="vx-add-record" href="{{ route('backend.resource.create', ['resource' => $page]) }}">
                                    <i class="fa-solid fa-plus"></i> Add New Record
                                </a>
                            @endif
                        </div>
                    </form>
                @else
                    <div>
                        <span class="eyebrow">{{ $records->total() ?? 0 }} records</span>
                        <h2>{{ $pageTitle }} Table</h2>
                        @if (! empty($search))
                            <small>Filtered by "{{ $search }}"</small>
                        @endif
                    </div>
                    @if ($canManage && in_array($page, ['products', 'categories', 'reviews'], true))
                        <a class="vx-add-record" href="{{ route('backend.resource.create', ['resource' => $page]) }}">
                            <i class="fa-solid fa-plus"></i> Add New
                        </a>
                    @endif
                @endif
            </div>

            <div class="table-wrap">
                @if ($page === 'products')
                    <table>
                        <thead>
                            <tr><th><input type="checkbox" aria-label="Select all products"></th><th>Product</th><th>Category</th><th>SKU</th><th>Price</th><th>Reviews</th><th>Status</th><th>Bestseller</th><th>Stock</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $product)
                                <tr>
                                    <td><input type="checkbox" aria-label="Select {{ $product->name }}"></td>
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
                                        <div class="vx-row-actions">
                                            <button type="button"
                                                data-resource-view-toggle="product-detail-{{ $product->id }}"
                                                title="View"><i class="fa-regular fa-eye"></i></button>
                                            <a href="{{ route('backend.resource.edit', ['resource' => 'products', 'id' => $product->id]) }}" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>
                                            <form action="{{ route('backend.resource.destroy', ['resource' => 'products', 'id' => $product->id]) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="product-detail-{{ $product->id }}" class="vx-user-detail-row" hidden>
                                    <td colspan="10">
                                        <div class="vx-user-detail-card">
                                            <span><b>SKU</b>{{ $product->sku }}</span>
                                            <span><b>Category</b>{{ $product->category?->name ?? '-' }}</span>
                                            <span><b>Price</b>£{{ number_format((float) $product->price, 2) }}</span>
                                            <span><b>Status</b>{{ Str::headline($product->status) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="empty-cell">No products found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @elseif ($page === 'categories')
                    <table>
                        <thead>
                            <tr><th><input type="checkbox" aria-label="Select all categories"></th><th>Image</th><th>Name</th><th>Slug</th><th>Products</th><th>Sort</th><th>Status</th><th>Created</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $category)
                                <tr>
                                    <td><input type="checkbox" aria-label="Select {{ $category->name }}"></td>
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
                                        <div class="vx-row-actions">
                                            <button type="button" data-resource-view-toggle="category-detail-{{ $category->id }}" title="View"><i class="fa-regular fa-eye"></i></button>
                                            <a href="{{ route('backend.resource.edit', ['resource' => 'categories', 'id' => $category->id]) }}" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>
                                            <form action="{{ route('backend.resource.destroy', ['resource' => 'categories', 'id' => $category->id]) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="category-detail-{{ $category->id }}" class="vx-user-detail-row" hidden>
                                    <td colspan="9">
                                        <div class="vx-user-detail-card">
                                            <span><b>Slug</b>{{ $category->slug }}</span>
                                            <span><b>Products</b>{{ number_format($category->products_count) }}</span>
                                            <span><b>Sort</b>{{ $category->sort_order }}</span>
                                            <span><b>Status</b>{{ Str::headline($category->status) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="empty-cell">No categories found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @elseif ($page === 'users')
                    @php
                        $plans = ['Enterprise', 'Basic', 'Team', 'Company'];
                        $billings = ['Auto Debit', 'Manual - Paypal', 'Manual - Cash'];
                        $roleIconMap = [
                            'super administrator' => ['fa-user-shield', 'red'],
                            'administrator' => ['fa-user-gear', 'purple'],
                            'admin' => ['fa-desktop', 'red'],
                            'maintainer' => ['fa-user-check', 'green'],
                            'subscriber' => ['fa-crown', 'purple'],
                            'editor' => ['fa-clock', 'cyan'],
                            'author' => ['fa-pen-to-square', 'orange'],
                            'user' => ['fa-user', 'green'],
                        ];
                    @endphp

                    <div class="vx-users-view">
                        <section class="vx-users-card">
                            <form class="vx-users-toolbar" method="GET" action="{{ route('backend.page', 'users') }}">
                                <input type="hidden" name="role" value="{{ $role ?? '' }}">
                                <input type="hidden" name="status" value="{{ $status ?? '' }}">
                                <select name="per_page" aria-label="Rows per page">
                                    <option>10</option>
                                </select>
                                <div>
                                    <input type="search" name="q" value="{{ $search }}" placeholder="Search User">
                                    @if ($canManage)
                                        <button class="vx-add-record" type="button"
                                            data-user-modal-open
                                            data-mode="create"
                                            data-action="{{ route('backend.resource.store', ['resource' => 'users']) }}">
                                            <i class="fa-solid fa-plus"></i> Add New Record
                                        </button>
                                    @endif
                                </div>
                            </form>

                            <div class="vx-users-table-wrap">
                                <table class="vx-users-table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" aria-label="Select all users"></th>
                                            <th>User</th>
                                            <th>Role</th>
                                            <th>Plan</th>
                                            <th>Billing</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($records as $user)
                                            @php
                                                $roleName = strtolower($user->role ?? 'user');
                                                $roleIcon = $roleIconMap[$roleName] ?? $roleIconMap['user'];
                                                $plan = $plans[$user->id % count($plans)];
                                                $billing = $billings[$user->id % count($billings)];
                                                $statusName = strtolower($user->status ?? 'active');
                                                $initials = Str::of($user->name)->explode(' ')->filter()->map(fn ($part) => Str::substr($part, 0, 1))->take(2)->implode('');
                                            @endphp
                                            <tr>
                                                <td><input type="checkbox" aria-label="Select {{ $user->name }}"></td>
                                                <td>
                                                    <span class="vx-user-cell">
                                                        @if ($user->avatar)
                                                            <img src="{{ url($user->avatar) }}" alt="{{ $user->name }}">
                                                        @else
                                                            <b>{{ Str::upper($initials ?: 'U') }}</b>
                                                        @endif
                                                        <span><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></span>
                                                    </span>
                                                </td>
                                                <td><span class="vx-role {{ $roleIcon[1] }}"><i class="fa-solid {{ $roleIcon[0] }}"></i>{{ Str::headline($user->role ?? 'user') }}</span></td>
                                                <td>{{ $plan }}</td>
                                                <td>{{ $billing }}</td>
                                                <td>
                                                    <span class="vx-status {{ $statusName === 'active' ? 'active' : ($statusName === 'inactive' ? 'inactive' : 'pending') }}">{{ Str::headline($statusName) }}</span>
                                                </td>
                                                <td>
                                                    @if ($canManageUsers)
                                                        <div class="vx-row-actions">
                                                            @if (auth()->id() !== $user->id)
                                                                <form action="{{ route('backend.resource.destroy', ['resource' => 'users', 'id' => $user->id]) }}" method="POST" onsubmit="return confirm('Delete this user?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                                                                </form>
                                                            @else
                                                                <button type="button" disabled title="Current user"><i class="fa-regular fa-trash-can"></i></button>
                                                            @endif
                                                            <button type="button" data-user-view-toggle="user-detail-{{ $user->id }}" title="View"><i class="fa-regular fa-eye"></i></button>
                                                            <button type="button"
                                                                data-user-modal-open
                                                                data-mode="edit"
                                                                data-action="{{ route('backend.resource.update', ['resource' => 'users', 'id' => $user->id]) }}"
                                                                data-name="{{ $user->name }}"
                                                                data-email="{{ $user->email }}"
                                                                data-phone="{{ $user->phone }}"
                                                                data-role="{{ str_contains(strtolower($user->role ?? ''), 'super') ? 'superadmin' : 'admin' }}"
                                                                data-status="{{ $user->status ?? 'active' }}"
                                                                title="Edit"><i class="fa-regular fa-pen-to-square"></i></button>
                                                        </div>
                                                    @else
                                                        <span class="view-only"><i class="fa-regular fa-eye"></i> View only</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr id="user-detail-{{ $user->id }}" class="vx-user-detail-row" hidden>
                                                <td colspan="7">
                                                    <div class="vx-user-detail-card">
                                                        <span><b>Email</b>{{ $user->email }}</span>
                                                        <span><b>Phone</b>{{ $user->phone ?: '-' }}</span>
                                                        <span><b>Joined</b>{{ $user->created_at?->format('M d, Y') }}</span>
                                                        <span><b>Role</b>{{ Str::headline($user->role ?? 'user') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7" class="empty-cell">No users found.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <footer class="vx-users-pagination">
                                <span>Showing {{ $records->firstItem() ?? 0 }} to {{ $records->lastItem() ?? 0 }} of {{ $records->total() }} entries</span>
                                {{ $records->links('backend.partials.vuexy-pagination') }}
                            </footer>
                        </section>

                        @if ($canManage)
                            <div class="admin-modal user-editor-modal" data-user-editor-modal aria-hidden="true">
                                <div class="admin-modal-backdrop" data-user-modal-close></div>
                                <section class="admin-modal-dialog vx-user-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="userModalTitle">
                                    <button class="admin-modal-close" type="button" data-user-modal-close aria-label="Close user modal"><i class="fa-solid fa-xmark"></i></button>
                                    <div class="admin-modal-head">
                                        <span><i class="fa-solid fa-user-gear"></i></span>
                                        <div>
                                            <h2 id="userModalTitle">Add User</h2>
                                            <p>Create and update users without leaving this list.</p>
                                        </div>
                                    </div>
                                    <form class="vx-user-modal-form" data-user-modal-form method="POST">
                                        @csrf
                                        <input type="hidden" name="_method" value="" data-user-modal-method disabled>
                                        <div class="vx-user-modal-grid">
                                            <label>Name
                                                <input name="name" data-user-field="name" required>
                                            </label>
                                            <label>Phone
                                                <input name="phone" data-user-field="phone">
                                            </label>
                                            <label class="wide">Email
                                                <input type="email" name="email" data-user-field="email" required>
                                            </label>
                                            <label>Role
                                                <select name="role" data-user-field="role" required>
                                                    <option value="admin">Admin</option>
                                                    <option value="superadmin">Super Admin</option>
                                                </select>
                                            </label>
                                            <label>Status
                                                <select name="status" data-user-field="status" required>
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </label>
                                            <label class="wide">Password
                                                <input type="password" name="password" data-user-field="password" placeholder="Minimum 6 characters">
                                                <small data-user-password-note>Required for new users.</small>
                                            </label>
                                        </div>
                                        <div class="vx-user-modal-actions">
                                            <button type="button" data-user-modal-close>Cancel</button>
                                            <button type="submit"><i class="fa-solid fa-floppy-disk"></i><span data-user-submit-label>Save User</span></button>
                                        </div>
                                    </form>
                                </section>
                            </div>
                        @endif
                    </div>
                @elseif ($page === 'orders')
                    <table>
                        <thead>
                            <tr><th><input type="checkbox" aria-label="Select all orders"></th><th>Order</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Tracking</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $order)
                                <tr>
                                    <td><input type="checkbox" aria-label="Select {{ $order->order_number }}"></td>
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
                                        <div class="vx-row-actions order-row-actions">
                                            <button type="button" data-resource-view-toggle="order-detail-{{ $order->id }}" title="View"><i class="fa-regular fa-eye"></i></button>
                                            <button type="button"
                                                data-order-modal-open
                                                data-action="{{ route('backend.orders.update', $order) }}"
                                                data-customer-name="{{ $order->customer_name }}"
                                                data-email="{{ $order->email }}"
                                                data-phone="{{ $order->phone }}"
                                                data-zip="{{ $order->zip }}"
                                                data-address="{{ $order->address }}"
                                                data-address-2="{{ $order->address_2 }}"
                                                data-city="{{ $order->city }}"
                                                data-state="{{ $order->state }}"
                                                data-status="{{ $order->status }}"
                                                data-payment-status="{{ $order->payment_status ?? 'unpaid' }}"
                                                data-tracking-status="{{ $order->tracking_status ?? 'placed' }}"
                                                data-tracking-number="{{ $order->tracking_number }}"
                                                data-tracking-note="{{ $order->tracking_note }}"
                                                data-admin-note="{{ $order->admin_note }}"
                                                title="Edit"><i class="fa-regular fa-pen-to-square"></i></button>
                                            @include('backend.partials.payment-proof-button', ['order' => $order])
                                        </div>
                                    </td>
                                </tr>
                                <tr id="order-detail-{{ $order->id }}" class="vx-user-detail-row" hidden>
                                    <td colspan="8">
                                        <div class="vx-user-detail-card">
                                            <span><b>Email</b>{{ $order->email }}</span>
                                            <span><b>Phone</b>{{ $order->phone ?: '-' }}</span>
                                            <span><b>Payment</b>{{ Str::headline($order->payment_status ?? 'unpaid') }}</span>
                                            <span><b>Tracking</b>{{ Str::headline($order->tracking_status ?? 'placed') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="empty-cell">No orders found.</td></tr>
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
                            <tr><th><input type="checkbox" aria-label="Select all reviews"></th><th>Customer</th><th>Product</th><th>Rating</th><th>Review</th><th>Status</th><th>Created</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $review)
                                <tr>
                                    <td><input type="checkbox" aria-label="Select {{ $review->customer_name }}"></td>
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
                                        <div class="vx-row-actions">
                                            <button type="button" data-resource-view-toggle="review-detail-{{ $review->id }}" title="View"><i class="fa-regular fa-eye"></i></button>
                                            <a href="{{ route('backend.resource.edit', ['resource' => 'reviews', 'id' => $review->id]) }}" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>
                                            <form action="{{ route('backend.resource.destroy', ['resource' => 'reviews', 'id' => $review->id]) }}" method="POST" onsubmit="return confirm('Delete this review?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="review-detail-{{ $review->id }}" class="vx-user-detail-row" hidden>
                                    <td colspan="8">
                                        <div class="vx-user-detail-card">
                                            <span><b>Customer</b>{{ $review->customer_name }}</span>
                                            <span><b>Product</b>{{ $review->product?->name ?? '-' }}</span>
                                            <span><b>Rating</b>{{ $review->rating }} star</span>
                                            <span><b>Status</b>{{ Str::headline($review->status) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="empty-cell">No reviews found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>

            @if (in_array($page, ['products', 'categories', 'reviews', 'orders'], true))
                <footer class="vx-users-pagination crud-vx-pagination">
                    <span>Showing {{ $records->firstItem() ?? 0 }} to {{ $records->lastItem() ?? 0 }} of {{ $records->total() }} entries</span>
                    {{ $records->links('backend.partials.vuexy-pagination') }}
                </footer>
            @else
                <div class="pagination-row">
                    {{ $records->links('backend.partials.vuexy-pagination') }}
                </div>
            @endif

            @if ($canManage && in_array($page, ['products', 'categories', 'reviews'], true))
                <div class="admin-modal user-editor-modal resource-editor-modal" data-resource-editor-modal aria-hidden="true">
                    <div class="admin-modal-backdrop" data-resource-modal-close></div>
                    <section class="admin-modal-dialog vx-user-modal-dialog vx-resource-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="resourceModalTitle">
                        <button class="admin-modal-close" type="button" data-resource-modal-close aria-label="Close modal"><i class="fa-solid fa-xmark"></i></button>
                        <div class="admin-modal-head">
                            <span><i class="fa-solid fa-pen-to-square"></i></span>
                            <div>
                                <h2 id="resourceModalTitle">Add {{ Str::headline(Str::singular($page)) }}</h2>
                                <p>Manage {{ strtolower($pageTitle) }} without leaving this list.</p>
                            </div>
                        </div>
                        <form class="vx-user-modal-form" data-resource-modal-form method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_method" value="" data-resource-modal-method disabled>

                            @if ($page === 'products')
                                <div class="vx-user-modal-grid">
                                    <label class="wide">Product Name
                                        <input name="name" data-resource-field="name" required>
                                    </label>
                                    <label>SKU
                                        <input name="sku" data-resource-field="sku" required>
                                    </label>
                                    <label>Slug
                                        <input name="slug" data-resource-field="slug" placeholder="Auto generated if empty">
                                    </label>
                                    <label>Category
                                        <select name="category_id" data-resource-field="categoryId">
                                            <option value="">No category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label>Price
                                        <input type="number" step="0.01" min="0" name="price" data-resource-field="price" required>
                                    </label>
                                    <label>Sale Price
                                        <input type="number" step="0.01" min="0" name="sale_price" data-resource-field="salePrice">
                                    </label>
                                    <label>Stock
                                        <select name="stock" data-resource-field="stock" required>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </label>
                                    <label>Reviews
                                        <input type="number" min="0" name="reviews_count" data-resource-field="reviewsCount" required>
                                    </label>
                                    <label>Status
                                        <select name="status" data-resource-field="status" required>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </label>
                                    <label>Product Image
                                        <input type="file" name="image_file" accept="image/png,image/jpeg,image/webp">
                                    </label>
                                    <label class="switch-row">
                                        <input type="checkbox" name="is_featured" value="1" data-resource-field="isFeatured">
                                        <span>Featured product</span>
                                    </label>
                                    <label class="switch-row">
                                        <input type="checkbox" name="is_bestseller" value="1" data-resource-field="isBestseller">
                                        <span>Bestseller product</span>
                                    </label>
                                    <label class="wide">Short Description
                                        <input name="short_description" data-resource-field="shortDescription">
                                    </label>
                                    <label class="wide">Description
                                        <textarea name="description" rows="4" data-resource-field="description" data-rich-text></textarea>
                                    </label>
                                </div>
                            @elseif ($page === 'categories')
                                <div class="vx-user-modal-grid">
                                    <label class="wide">Name
                                        <input name="name" data-resource-field="name" required>
                                    </label>
                                    <label>Slug
                                        <input name="slug" data-resource-field="slug" placeholder="Auto generated if empty">
                                    </label>
                                    <label>Sort Order
                                        <input type="number" min="0" name="sort_order" data-resource-field="sortOrder" required>
                                    </label>
                                    <label>Status
                                        <select name="status" data-resource-field="status" required>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </label>
                                    <label>Upload Image
                                        <input type="file" name="image_file" accept="image/png,image/jpeg,image/webp">
                                    </label>
                                    <label>Image Path
                                        <input name="image" data-resource-field="image">
                                    </label>
                                    <label class="wide">Description
                                        <textarea name="description" rows="4" data-resource-field="description" data-rich-text></textarea>
                                    </label>
                                </div>
                            @else
                                <div class="vx-user-modal-grid">
                                    <label class="wide">Customer Name
                                        <input name="customer_name" data-resource-field="customerName" required>
                                    </label>
                                    <label>Rating
                                        <select name="rating" data-resource-field="rating" required>
                                            @for ($rating = 5; $rating >= 1; $rating--)
                                                <option value="{{ $rating }}">{{ $rating }} Star{{ $rating > 1 ? 's' : '' }}</option>
                                            @endfor
                                        </select>
                                    </label>
                                    <label>Status
                                        <select name="status" data-resource-field="status" required>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </label>
                                    <label class="wide">Customer Title
                                        <input name="customer_title" data-resource-field="customerTitle">
                                    </label>
                                    <label class="wide">Product
                                        <select name="product_id" data-resource-field="productId">
                                            <option value="">No product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label class="wide">Avatar Path
                                        <input name="avatar" data-resource-field="avatar">
                                    </label>
                                    <label class="switch-row wide">
                                        <input type="checkbox" name="is_featured" value="1" data-resource-field="isFeatured">
                                        <span>Featured review</span>
                                    </label>
                                    <label class="wide">Review
                                        <textarea name="comment" rows="5" data-resource-field="comment" data-rich-text required></textarea>
                                    </label>
                                </div>
                            @endif

                            <div class="vx-user-modal-actions">
                                <button type="button" data-resource-modal-close>Cancel</button>
                                <button type="submit"><i class="fa-solid fa-floppy-disk"></i><span data-resource-submit-label>Save</span></button>
                            </div>
                        </form>
                    </section>
                </div>
            @endif

            @if ($page === 'orders')
                <div class="admin-modal user-editor-modal resource-editor-modal" data-order-editor-modal aria-hidden="true">
                    <div class="admin-modal-backdrop" data-order-modal-close></div>
                    <section class="admin-modal-dialog vx-user-modal-dialog vx-resource-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="orderModalTitle">
                        <button class="admin-modal-close" type="button" data-order-modal-close aria-label="Close order modal"><i class="fa-solid fa-xmark"></i></button>
                        <div class="admin-modal-head">
                            <span><i class="fa-solid fa-truck-fast"></i></span>
                            <div>
                                <h2 id="orderModalTitle">Edit Order</h2>
                                <p>Update customer, payment and tracking details without leaving this list.</p>
                            </div>
                        </div>
                        <form class="vx-user-modal-form" data-order-modal-form method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="vx-user-modal-grid">
                                <label>Customer Name
                                    <input name="customer_name" data-order-field="customerName">
                                </label>
                                <label>Email
                                    <input type="email" name="email" data-order-field="email">
                                </label>
                                <label>Phone
                                    <input name="phone" data-order-field="phone">
                                </label>
                                <label>Post Code
                                    <input name="zip" data-order-field="zip">
                                </label>
                                <label class="wide">Address
                                    <input name="address" data-order-field="address">
                                </label>
                                <label>Address 2
                                    <input name="address_2" data-order-field="address2">
                                </label>
                                <label>City
                                    <input name="city" data-order-field="city">
                                </label>
                                <label>County/State
                                    <input name="state" data-order-field="state">
                                </label>
                                <label>Order Status
                                    <select name="status" data-order-field="status" required>
                                        <option value="pending">Pending</option>
                                        <option value="processing">Processing</option>
                                        <option value="shipped">Shipped</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </label>
                                <label>Payment Status
                                    <select name="payment_status" data-order-field="paymentStatus" required>
                                        <option value="unpaid">Unpaid</option>
                                        <option value="paid">Paid</option>
                                        <option value="failed">Failed</option>
                                        <option value="refunded">Refunded</option>
                                    </select>
                                </label>
                                <label>Tracking Status
                                    <select name="tracking_status" data-order-field="trackingStatus" required>
                                        <option value="placed">Placed</option>
                                        <option value="processing">Processing</option>
                                        <option value="packed">Packed</option>
                                        <option value="dispatched">Dispatched</option>
                                        <option value="out_for_delivery">Out for delivery</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </label>
                                <label>Royal Mail ID
                                    <input name="tracking_number" data-order-field="trackingNumber">
                                </label>
                                <label class="wide">Customer Tracking Note
                                    <textarea name="tracking_note" rows="3" data-order-field="trackingNote"></textarea>
                                </label>
                                <label class="wide">Admin Note
                                    <textarea name="admin_note" rows="3" data-order-field="adminNote"></textarea>
                                </label>
                            </div>
                            <div class="vx-user-modal-actions">
                                <button type="button" data-order-modal-close>Cancel</button>
                                <button type="submit"><i class="fa-solid fa-floppy-disk"></i><span>Update Order</span></button>
                            </div>
                        </form>
                    </section>
                </div>
            @endif
        </article>
    @endif
@endsection

