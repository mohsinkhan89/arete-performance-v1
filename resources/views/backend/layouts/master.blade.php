<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') - Arete Performance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link href="{{ url('backend/assets/css/style.css') }}" rel="stylesheet">
    @yield('css')
</head>
<body class="admin-light-page">
    <div class="admin-shell">
        <aside class="admin-sidebar" id="adminSidebar">
            <a class="brand" href="{{ route('backend.dashboard') }}" aria-label="Arete Performance Admin">
                <img src="{{ url('frontend/assets/images/logo/logo-transperent.png') }}" alt="Arete Performance">
            </a>

            @php
                $menuItems = [
                    ['label' => 'Dashboard', 'icon' => 'fa-house', 'url' => route('backend.dashboard'), 'active' => request()->routeIs('backend.dashboard')],
                    ['label' => 'Products', 'icon' => 'fa-cube', 'url' => route('backend.page', 'products')],
                    ['label' => 'Categories', 'icon' => 'fa-table-cells', 'url' => route('backend.page', 'categories')],
                    ['label' => 'Orders', 'icon' => 'fa-clipboard-list', 'url' => route('backend.page', 'orders')],
                    ['label' => 'Users', 'icon' => 'fa-users', 'url' => route('backend.page', 'users')],
                    ['label' => 'Reviews', 'icon' => 'fa-star', 'url' => route('backend.page', 'reviews')],
                    ['label' => 'Reports', 'icon' => 'fa-chart-column', 'url' => route('backend.page', 'reports')],
                ];
            @endphp

            <nav class="admin-nav" aria-label="Admin navigation">
                @foreach ($menuItems as $item)
                    <a class="{{ ($item['active'] ?? false) || request()->is('admin/' . strtolower($item['label'])) ? 'active' : '' }}" href="{{ $item['url'] }}">
                        <i class="fa-solid {{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="support-card">
                <i class="fa-solid fa-headset"></i>
                <div>
                    <strong>Need Help?</strong>
                    <span>We're here to help you</span>
                </div>
                <a href="#">Contact Support</a>
            </div>

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
                    <form class="admin-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="search" placeholder="Search anything..." aria-label="Search anything">
                        <button type="submit" aria-label="Search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
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
                                            <strong>#{{ $order->order_number }} · {{ $order->customer_name }}</strong>
                                            <small>{{ str_replace('_', ' ', ucfirst($order->payment_status ?? 'unpaid')) }} · {{ str_replace('_', ' ', ucfirst($order->tracking_status ?? 'placed')) }} · £{{ number_format((float) $order->total, 2) }}</small>
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

    <script>
        window.adminRoutes = {
            orderNotifications: "{{ route('backend.notifications.orders') }}"
        };
    </script>
    <script src="{{ url('backend/assets/js/main.js') }}"></script>
    @yield('js')
</body>
</html>
