<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') - Arete Performance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link href="{{ url('backend/assets/css/style.css') }}?v={{ urlencode($siteSettings['css_version'] ?? '1.0.0') }}" rel="stylesheet">
    @yield('css')
</head>
<body class="admin-light-page {{ request()->routeIs('backend.dashboard') ? 'backend-dashboard-page' : 'backend-inner-page' }}">
    <div class="admin-shell">
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-brand-row">
                <a class="brand" href="{{ route('backend.dashboard') }}" aria-label="Arete Performance Admin">
                    <img src="{{ url($siteSettings['header_logo'] ?? 'frontend/assets/images/logo/logo-transperent.png') }}" alt="Arete Performance">
                    <span><strong>Arete</strong><small>Admin Console</small></span>
                </a>
                <button class="sidebar-close" type="button" aria-label="Close sidebar"><i class="fa-solid fa-xmark"></i></button>
            </div>

            @php
                $menuItems = [
                    ['label' => 'Dashboard', 'icon' => 'fa-house', 'url' => route('backend.dashboard'), 'active' => request()->routeIs('backend.dashboard')],
                    ['label' => 'Products', 'icon' => 'fa-cube', 'url' => route('backend.page', 'products')],
                    ['label' => 'Categories', 'icon' => 'fa-table-cells', 'url' => route('backend.page', 'categories')],
                    ['label' => 'Orders', 'icon' => 'fa-clipboard-list', 'url' => route('backend.page', 'orders')],
                    ['label' => 'Stock Requests', 'icon' => 'fa-bell', 'url' => route('backend.page', 'stock-notifications'), 'active' => request()->is('admin/stock-notifications')],
                    ['label' => 'Users', 'icon' => 'fa-users', 'url' => route('backend.page', 'users')],
                    ['label' => 'Reviews', 'icon' => 'fa-star', 'url' => route('backend.page', 'reviews')],
                    ['label' => 'Reports', 'icon' => 'fa-chart-column', 'url' => route('backend.page', 'reports')],
                    ['label' => 'Settings', 'icon' => 'fa-gear', 'url' => route('backend.page', 'settings')],
                ];
            @endphp

            <nav class="admin-nav" aria-label="Admin navigation">
                <span class="admin-nav-label">Apps &amp; Pages</span>
                @foreach ($menuItems as $item)
                    <a class="{{ ($item['active'] ?? false) || request()->is('admin/' . strtolower($item['label'])) ? 'active' : '' }}" href="{{ $item['url'] }}">
                        <i class="fa-solid {{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- <div class="support-card">
                <i class="fa-solid fa-database"></i>
                <div>
                    <strong>Live Store Data</strong>
                    <span>Orders, products, users</span>
                </div>
                <a href="{{ route('backend.page', 'reports') }}">Open Reports</a>
            </div> --}}

            <div class="sidebar-foot">
                <span>Arete Admin</span>
                <small>Secure management panel</small>
            </div>
        </aside>

        <main class="admin-main">
            <header class="topbar">
                <button class="sidebar-toggle" type="button" aria-label="Toggle sidebar">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>

                <div class="topbar-actions">
                    @php
                        $searchablePages = [
                            'products' => 'Search products...',
                            'categories' => 'Search categories...',
                            'orders' => 'Search orders...',
                            'stock-notifications' => 'Search stock requests...',
                            'users' => 'Search users...',
                            'reviews' => 'Search reviews...',
                        ];
                        $activeSearchPage = request()->routeIs('backend.page') && isset($searchablePages[request()->route('page')])
                            ? request()->route('page')
                            : 'orders';
                    @endphp
                    <form class="admin-search" method="GET" action="{{ route('backend.page', $activeSearchPage) }}">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ $searchablePages[$activeSearchPage] }}" aria-label="{{ $searchablePages[$activeSearchPage] }}">
                        <button type="submit" aria-label="Search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
                    <button class="icon-btn command-toggle" type="button" aria-label="Open quick navigation" title="Quick navigation (Ctrl+K)">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </button>
                    <a class="icon-btn storefront-link" href="{{ route('frontend.index') }}" target="_blank" rel="noopener" aria-label="Open storefront" title="Open storefront">
                        <i class="fa-solid fa-store"></i>
                    </a>
                    <button class="icon-btn fullscreen-toggle" type="button" aria-label="Enter fullscreen" title="Fullscreen">
                        <i class="fa-solid fa-expand"></i>
                    </button>
                    <button class="icon-btn theme-toggle" type="button" aria-label="Toggle dark mode">
                        <i class="fa-regular fa-moon"></i>
                    </button>
                    <div class="notification-dropdown">
                        <button class="icon-btn has-badge notification-toggle" type="button" aria-label="Notifications" aria-expanded="false">
                            <i class="fa-regular fa-bell"></i>
                            <span data-notification-count>{{ $orderNotificationCount ?? 0 }}</span>
                        </button>
                        <div class="notification-panel" aria-hidden="true">
                            <div class="notification-head">
                                <strong>Live Orders</strong>
                                <small>Runtime updates</small>
                            </div>
                            <div class="notification-list" data-order-notifications>
                                @forelse (($orderNotifications ?? collect()) as $order)
                                    <a class="notification-item" href="{{ route('backend.orders.show', $order) }}">
                                        <span class="notification-dot payment-status-{{ $order->payment_status ?? 'unpaid' }}"></span>
                                        <div>
                                            <strong>#{{ $order->order_number }} &middot; {{ $order->customer_name }}</strong>
                                            <small>{{ str_replace('_', ' ', ucfirst($order->payment_status ?? 'unpaid')) }} &middot; {{ str_replace('_', ' ', ucfirst($order->tracking_status ?? 'placed')) }} &middot; &pound;{{ number_format((float) $order->total, 2) }}</small>
                                        </div>
                                    </a>
                                @empty
                                    <div class="notification-empty">No orders yet.</div>
                                @endforelse
                            </div>
                            <a class="notification-all" href="{{ route('backend.page', 'orders') }}">View all orders</a>
                        </div>
                    </div>
                    <div class="profile-dropdown">
                        <button class="profile-menu" type="button" aria-expanded="false" aria-haspopup="true">
                            <span class="profile-copy">
                                <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
                                <small>Administrator</small>
                            </span>
                            <span class="avatar small"><i class="fa-solid fa-user"></i></span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="profile-panel" aria-hidden="true">
                            <div class="profile-panel-head">
                                <span class="avatar small"><i class="fa-solid fa-user"></i></span>
                                <div>
                                    <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
                                    <small>{{ auth()->user()->email ?? '' }}</small>
                                </div>
                            </div>
                            <a href="{{ route('backend.profile') }}"><i class="fa-regular fa-user"></i> View Profile</a>
                            <a href="{{ route('backend.profile.edit') }}"><i class="fa-solid fa-pen"></i> Edit Profile</a>
                            <a href="{{ route('backend.page', 'settings') }}"><i class="fa-solid fa-gear"></i> Settings</a>
                            <a href="{{ route('backend.page', 'reports') }}"><i class="fa-solid fa-chart-line"></i> Activity</a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <section class="content-wrap">
                @if (session('success'))
                    <div class="flash-message"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="flash-message error"><i class="fa-solid fa-circle-exclamation"></i>Please check the highlighted fields.</div>
                @endif

                @yield('body')
            </section>
        </main>
    </div>
    <button class="sidebar-backdrop" type="button" aria-label="Close sidebar"></button>

    <div class="command-palette" aria-hidden="true">
        <button class="command-backdrop" type="button" aria-label="Close quick navigation"></button>
        <section class="command-dialog" role="dialog" aria-modal="true" aria-labelledby="commandTitle">
            <div class="command-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" placeholder="Search pages and actions..." aria-label="Search quick navigation" autocomplete="off">
                <kbd>ESC</kbd>
            </div>
            <div class="command-body">
                <span id="commandTitle">Quick navigation</span>
                <div class="command-results">
                    @foreach ($menuItems as $item)
                        <a href="{{ $item['url'] }}" data-command-label="{{ strtolower($item['label']) }}">
                            <i class="fa-solid {{ $item['icon'] }}"></i>
                            <div><strong>{{ $item['label'] }}</strong><small>Open {{ strtolower($item['label']) }}</small></div>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @endforeach
                    <a href="{{ route('backend.profile') }}" data-command-label="profile account">
                        <i class="fa-regular fa-user"></i>
                        <div><strong>Profile</strong><small>View administrator profile</small></div>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('frontend.index') }}" target="_blank" data-command-label="store frontend website">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <div><strong>View Store</strong><small>Open storefront in a new tab</small></div>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                <div class="command-empty" hidden>No matching page or action found.</div>
            </div>
            <footer><span><kbd>↑</kbd><kbd>↓</kbd> Navigate</span><span><kbd>Enter</kbd> Open</span></footer>
        </section>
    </div>

    <div class="admin-modal payment-proof-modal" data-payment-proof-modal aria-hidden="true">
        <div class="admin-modal-backdrop" data-payment-proof-close></div>
        <section class="admin-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="paymentProofTitle">
            <button class="admin-modal-close" type="button" data-payment-proof-close aria-label="Close payment proof modal">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="admin-modal-head">
                <span><i class="fa-solid fa-receipt"></i></span>
                <div>
                    <h2 id="paymentProofTitle">Payment Proof</h2>
                    <p data-payment-proof-summary>Upload proof and mark payment paid.</p>
                </div>
            </div>
            <div class="payment-proof-current" data-payment-proof-current>
                <div class="proof-empty-state" data-payment-proof-empty>
                    <i class="fa-regular fa-file-image"></i>
                    <strong>No payment proof uploaded yet</strong>
                    <small>Upload a receipt or payment screenshot below to mark this order as paid.</small>
                </div>
                <div class="proof-current-preview" data-payment-proof-preview hidden>
                    <img src="" alt="Current payment proof" data-payment-proof-current-image>
                </div>
                <a href="#" target="_blank" rel="noopener" data-payment-proof-current-link hidden>
                    <i class="fa-regular fa-image"></i> Open current proof
                </a>
            </div>
            <form class="payment-proof-modal-form" data-payment-proof-form method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <label>
                    <span>Payment proof image</span>
                    <input type="file" name="payment_proof_file" accept="image/png,image/jpeg,image/webp">
                    <small>PNG, JPG, JPEG, or WEBP up to 4MB.</small>
                </label>
                <button type="submit">
                    <i class="fa-solid fa-check"></i>
                    Save proof &amp; mark paid
                </button>
            </form>
        </section>
    </div>

    <div class="admin-modal resource-preview-modal" data-resource-preview-modal aria-hidden="true">
        <div class="admin-modal-backdrop" data-resource-preview-close></div>
        <section class="admin-modal-dialog vx-preview-dialog" role="dialog" aria-modal="true" aria-labelledby="resourcePreviewTitle">
            <button class="admin-modal-close" type="button" data-resource-preview-close aria-label="Close preview"><i class="fa-solid fa-xmark"></i></button>
            <div class="vx-preview-media"><img src="" alt="" data-resource-preview-image></div>
            <div class="vx-preview-content">
                <span data-resource-preview-subtitle></span>
                <h2 id="resourcePreviewTitle" data-resource-preview-title></h2>
                <div class="vx-preview-meta" data-resource-preview-meta></div>
                <a href="#" data-resource-preview-edit><i class="fa-solid fa-pen"></i> Edit Product</a>
            </div>
        </section>
    </div>

    <script>
        window.adminRoutes = {
            orderNotifications: "{{ route('backend.notifications.orders') }}"
        };
    </script>
    <script src="{{ url('backend/assets/js/main.js') }}?v={{ urlencode($siteSettings['js_version'] ?? '1.0.0') }}"></script>
    @yield('js')
</body>
</html>
