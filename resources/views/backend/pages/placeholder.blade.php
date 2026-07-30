@extends('backend.layouts.master')

@section('title', $pageTitle)

@section('body')
    @if ($page !== 'users')
        <div class="page-heading">
            <h1>{{ $pageTitle }}</h1>
            <p>Manage {{ strtolower($pageTitle) }} from your Arete Performance admin panel.</p>
        </div>
    @endif

    @if (in_array($page, ['products', 'categories', 'reviews', 'orders', 'stock-notifications'], true))
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
                                            <button type="button"
                                                data-resource-preview
                                                data-preview-title="{{ $product->name }}"
                                                data-preview-image="{{ url($product->image ?: 'backend/assets/imgs/product-bottle.png') }}"
                                                data-preview-subtitle="{{ $product->category?->name ?? 'Uncategorised' }}"
                                                data-preview-meta="{{ $product->sku }}|£{{ number_format((float) $product->price, 2) }}|{{ ucfirst($product->status) }}|{{ number_format($product->stock) }} stock"
                                                data-preview-edit="{{ route('backend.resource.edit', ['resource' => 'products', 'id' => $product->id]) }}"
                                                title="View"><i class="fa-regular fa-eye"></i></button>
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
                        <section class="vx-user-stats">
                            <article>
                                <span>Session</span>
                                <strong>{{ number_format($userStats['total'] ?? 0) }} <em>(+29%)</em></strong>
                                <small>Total Users</small>
                                <i class="fa-solid fa-users purple"></i>
                            </article>
                            <article>
                                <span>Paid Users</span>
                                <strong>{{ number_format($userStats['paid'] ?? 0) }} <em>(+18%)</em></strong>
                                <small>Last week analytics</small>
                                <i class="fa-solid fa-user-plus red"></i>
                            </article>
                            <article>
                                <span>Active Users</span>
                                <strong>{{ number_format($userStats['active'] ?? 0) }} <em class="down">(-14%)</em></strong>
                                <small>Last week analytics</small>
                                <i class="fa-solid fa-user-check green"></i>
                            </article>
                            <article>
                                <span>Pending Users</span>
                                <strong>{{ number_format($userStats['pending'] ?? 0) }} <em>(+42%)</em></strong>
                                <small>Last week analytics</small>
                                <i class="fa-solid fa-user-clock orange"></i>
                            </article>
                        </section>

                        <section class="vx-users-card">
                            <form class="vx-users-filters" method="GET" action="{{ route('backend.page', 'users') }}">
                                <h2>Filters</h2>
                                <div>
                                    <select name="role" aria-label="Select role" onchange="this.form.submit()">
                                        <option value="">Select Role</option>
                                        @foreach (($userStats['roles'] ?? collect()) as $filterRole)
                                            <option value="{{ $filterRole }}" @selected(($role ?? '') === $filterRole)>{{ Str::headline($filterRole) }}</option>
                                        @endforeach
                                    </select>
                                    <select name="plan" aria-label="Select plan" onchange="this.form.submit()">
                                        <option value="">Select Plan</option>
                                        @foreach ($plans as $plan)
                                            <option value="{{ strtolower($plan) }}" @selected(request('plan') === strtolower($plan))>{{ $plan }}</option>
                                        @endforeach
                                    </select>
                                    <select name="status" aria-label="Select status" onchange="this.form.submit()">
                                        <option value="">Select Status</option>
                                        <option value="active" @selected($status === 'active')>Active</option>
                                        <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                                    </select>
                                </div>
                            </form>

                            <form class="vx-users-toolbar" method="GET" action="{{ route('backend.page', 'users') }}">
                                <input type="hidden" name="role" value="{{ $role ?? '' }}">
                                <input type="hidden" name="status" value="{{ $status ?? '' }}">
                                <select name="per_page" aria-label="Rows per page">
                                    <option>10</option>
                                </select>
                                <div>
                                    <input type="search" name="q" value="{{ $search }}" placeholder="Search User">
                                    <button class="vx-export-btn" type="button"><i class="fa-solid fa-arrow-up-from-bracket"></i> Export <i class="fa-solid fa-chevron-down"></i></button>
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
                                                            <button type="button"
                                                                data-user-modal-open
                                                                data-mode="edit"
                                                                data-action="{{ route('backend.resource.update', ['resource' => 'users', 'id' => $user->id]) }}"
                                                                data-name="{{ $user->name }}"
                                                                data-email="{{ $user->email }}"
                                                                data-phone="{{ $user->phone }}"
                                                                data-role="{{ str_contains(strtolower($user->role ?? ''), 'super') ? 'superadmin' : 'admin' }}"
                                                                data-status="{{ $user->status ?? 'active' }}"
                                                                title="View"><i class="fa-regular fa-eye"></i></button>
                                                            <button type="button"
                                                                data-user-modal-open
                                                                data-mode="edit"
                                                                data-action="{{ route('backend.resource.update', ['resource' => 'users', 'id' => $user->id]) }}"
                                                                data-name="{{ $user->name }}"
                                                                data-email="{{ $user->email }}"
                                                                data-phone="{{ $user->phone }}"
                                                                data-role="{{ str_contains(strtolower($user->role ?? ''), 'super') ? 'superadmin' : 'admin' }}"
                                                                data-status="{{ $user->status ?? 'active' }}"
                                                                title="Edit"><i class="fa-solid fa-ellipsis-vertical"></i></button>
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
                            </div>

                            <footer class="vx-users-pagination">
                                <span>Showing {{ $records->firstItem() ?? 0 }} to {{ $records->lastItem() ?? 0 }} of {{ $records->total() }} entries</span>
                                {{ $records->links() }}
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

