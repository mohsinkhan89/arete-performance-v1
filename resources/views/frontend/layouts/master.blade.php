<!doctype html>
<html lang="en">
<head>
    @yield('metas')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Arete Performance premium sports nutrition and performance products.">
    @yield('metas')
    <title>Arete Performance</title>

    @yield('css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link href="{{ url('frontend/assets/css/style.css') }}" rel="stylesheet">
</head>
<body>
  

  @include('frontend.inc.header')
  <main>
    @yield('body')
  </main>
  @include('frontend.inc.footer')

  

  <div class="search-panel" aria-hidden="true">
    <button class="panel-close search-close" type="button" aria-label="Close search"><i class="fa-solid fa-xmark"></i></button>
    <div class="search-box">
      <p class="eyebrow">Search products</p>
      <form class="search-form">
        <label class="visually-hidden" for="siteSearch">Search</label>
        <input id="siteSearch" type="search" placeholder="Search Orals, Peptides, HGH..." autocomplete="off">
        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
      </form>
      <div class="search-results" aria-live="polite"></div>
    </div>
  </div>

  <div class="cart-overlay" aria-hidden="true"></div>
  <aside class="cart-drawer" aria-hidden="true" aria-label="Shopping cart">
    <div class="cart-head">
      <div><p class="eyebrow">Your cart</p><h2>Shopping Cart</h2></div>
      <button class="panel-close cart-close" type="button" aria-label="Close cart"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="cart-items"></div>
    <div class="cart-empty">Your cart is empty.</div>
    <div class="cart-summary">
      <div><span>Subtotal</span><strong class="cart-subtotal">$0.00</strong></div>
      <p>Taxes and shipping calculated at checkout.</p>
      <button class="btn btn-gold w-100" type="button">Checkout <i class="fa-solid fa-arrow-right"></i></button>
    </div>
  </aside>

  <div class="toast-container position-fixed bottom-0 end-0 p-3"><div id="cartToast" class="toast" role="status"><div class="toast-body">Product added to your cart.</div></div></div>

  <div class="report-lightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Test report">
    <button class="report-lightbox-close" type="button" aria-label="Close test report"><i class="fa-solid fa-xmark"></i></button>
    <figure>
      <img src="" alt="Product test report" data-report-lightbox-image>
      <figcaption data-report-lightbox-title>Test Report</figcaption>
    </figure>
  </div>

  @yield('js')
  <script>
    window.appRoutes = {
      home: "{{ route('frontend.index') }}",
      shop: "{{ route('frontend.shop') }}",
      search: "{{ route('frontend.search') }}",
      cart: "{{ route('frontend.my-cart') }}",
      checkout: "{{ route('frontend.checkout') }}",
      orderSuccess: "{{ route('frontend.order-success') }}",
      productDetails: "{{ route('frontend.product-details') }}",
      cartJson: "{{ route('frontend.cart.json') }}",
      cartAddBase: "{{ url('cart/add') }}",
      cartUpdateBase: "{{ url('cart/update') }}",
      cartRemoveBase: "{{ url('cart/remove') }}",
      cartClear: "{{ route('frontend.cart.clear') }}"
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ url('frontend/assets/js/main.js') }}"></script>
</body>
</html>
