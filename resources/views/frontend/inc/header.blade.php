<header class="site-header">
    <nav class="navbar navbar-expand-lg navbar-dark py-lg-0 py-md-1 py-3">
      <div class="container">
        <a class="brand" href="{{ route('frontend.index') }}#home" aria-label="Arete Performance home"><img src="{{ url($siteSettings['header_logo'] ?? 'frontend/assets/images/logo/logo-transperent.png') }}" alt="Arete Performance"></a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
          <ul class="navbar-nav mx-auto gap-lg-2">
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.index') ? 'active' : '' }}" href="{{ route('frontend.index') }}#home"><i class="fa-solid fa-house"></i><span>Home</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.shop') ? 'active' : '' }}" href="{{ route('frontend.shop') }}"><i class="fa-solid fa-bag-shopping"></i><span>Shop</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.search') ? 'active' : '' }}" href="{{ route('frontend.search') }}"><i class="fa-solid fa-magnifying-glass-chart"></i><span>Search</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.track-order') ? 'active' : '' }}" href="{{ route('frontend.track-order') }}"><i class="fa-solid fa-truck-fast"></i><span>Track</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.my-cart') ? 'active' : '' }}" href="{{ route('frontend.my-cart') }}"><i class="fa-solid fa-cart-shopping"></i><span>Cart</span></a></li>
          </ul>
          <div class="nav-actions d-flex align-items-center gap-2">
            <button class="icon-btn search-toggle" type="button" aria-label="Search products"><i class="fa-solid fa-magnifying-glass"></i></button>
            <button class="icon-btn cart-btn" type="button" aria-label="Cart"><i class="fa-solid fa-cart-shopping"></i><span class="cart-count">{{ $cartSummary['item_count'] ?? 0 }}</span></button>
          </div>
        </div>
      </div>
    </nav>
  </header>
