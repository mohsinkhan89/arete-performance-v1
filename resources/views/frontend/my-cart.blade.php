@extends('frontend.layouts.master')

@section('metas')
@endsection

@section('css')
<style>
.cart-hero-copy .eyebrow{color:#e9a611;font-size:11px;font-weight:900;letter-spacing:.1em;text-transform:uppercase;margin-bottom:12px}.cart-hero h1{font-family:'Barlow Condensed',sans-serif;font-size:clamp(42px,6vw,68px);font-weight:800;line-height:.95}
</style>
@endsection

@section('body')
    <section class="cart-hero">
      <div class="container">
        <div class="cart-hero-copy reveal-up">
          <p class="eyebrow">Shopping cart</p>
          <h1>Your <span>Cart</span></h1>
          <p>Review your items and proceed to checkout.</p>
        </div>
      </div>
    </section>

    <section class="cart-main section-space">
      <div class="container">
        @if (session('error'))
          <div class="alert alert-warning">{{ session('error') }}</div>
        @endif

        <div class="cart-layout">
          <section class="cart-items-panel reveal-on-scroll" aria-labelledby="cartItemsTitle">
            <h2 id="cartItemsTitle">Cart Items ({{ $cart['item_count'] }})</h2>

            @if ($cart['is_empty'])
              <div class="cart-empty-state">
                <h3>Your cart is empty.</h3>
                <p>Add products from the shop to see them here.</p>
                <a class="cart-secondary-btn" href="{{ route('frontend.shop') }}"><i class="fa-solid fa-arrow-left"></i> Continue shopping</a>
              </div>
            @else
              <div class="cart-table" role="table" aria-label="Cart items">
                <div class="cart-row cart-row-head" role="row">
                  <span role="columnheader">Product</span>
                  <span role="columnheader">Price</span>
                  <span role="columnheader">Quantity</span>
                  <span role="columnheader">Total</span>
                  <span role="columnheader" class="visually-hidden">Remove</span>
                </div>

                @foreach ($cart['items'] as $item)
                  @php($product = $item['product'])
                  <article class="cart-row" role="row" data-cart-row="{{ $product->id }}">
                    <div class="cart-product-cell" role="cell">
                      <img src="{{ url($product->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $product->name }}">
                      <div><strong>{{ $product->name }}</strong><span>{{ $product->category?->name ?? 'Product' }}</span></div>
                    </div>
                    <strong role="cell">£{{ number_format($item['unit_price'], 2) }}</strong>
                    <div class="cart-stepper" role="cell" aria-label="{{ $product->name }} quantity">
                      <button type="button" data-cart-dec="{{ $product->id }}">-</button>
                      <span>{{ $item['quantity'] }}</span>
                      <button type="button" data-cart-inc="{{ $product->id }}">+</button>
                    </div>
                    <strong role="cell">£{{ number_format($item['line_total'], 2) }}</strong>
                    <button class="cart-remove" type="button" data-cart-remove="{{ $product->id }}" aria-label="Remove {{ $product->name }}"><i class="fa-regular fa-trash-can"></i></button>
                  </article>
                @endforeach
              </div>

              <div class="cart-panel-actions">
                <a class="cart-secondary-btn" href="{{ route('frontend.shop') }}"><i class="fa-solid fa-arrow-left"></i> Continue shopping</a>
                <button class="cart-secondary-btn" type="button" data-cart-clear>Clear cart <i class="fa-regular fa-trash-can"></i></button>
              </div>
            @endif
          </section>

          <aside class="order-summary reveal-on-scroll" aria-labelledby="orderSummaryTitle">
            <h2 id="orderSummaryTitle">Order Summary</h2>
            <div class="summary-line"><span>Subtotal ({{ $cart['item_count'] }} items)</span><strong>£{{ number_format($cart['subtotal'], 2) }}</strong></div>
            <div class="summary-line"><span>Shipping <i class="fa-regular fa-circle-question"></i></span><span>Calculated at checkout</span></div>
            <div class="summary-line"><span>Tax <i class="fa-regular fa-circle-question"></i></span><span>Calculated at checkout</span></div>
            <div class="summary-total"><span>Estimated Total</span><strong>£{{ number_format($cart['subtotal'], 2) }}</strong></div>
            <a class="btn btn-gold w-100 {{ $cart['is_empty'] ? 'disabled' : '' }}" href="{{ $cart['is_empty'] ? '#' : route('frontend.checkout') }}">Proceed to checkout <i class="fa-solid fa-arrow-right"></i></a>
            <p>We accept</p>
            <div class="payment-badges cart-payments"><span>VISA</span><span></span><span>AMEX</span><span>PayPal</span><span>BTC</span></div>
          </aside>
        </div>

        <section class="bundle-banner reveal-on-scroll" aria-label="Bundle offer">
          <div></div>
          <div>
            <p class="eyebrow">Bundle &amp; save</p>
            <h2>Buy More. Save More.</h2>
            <p>Stack your results with premium bundles and exclusive savings.</p>
            <a class="btn btn-gold" href="{{ route('frontend.shop') }}">View bundles <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </section>
      </div>
    </section>

    @include('frontend.inc.delivery-trusted')
@endsection

@section('js')
@endsection
