@extends('frontend.layouts.master')

@section('metas')
@endsection

@section('css')
@endsection

@section('body')
    <section class="checkout-hero">
      <div class="container">
        <div class="checkout-hero-copy reveal-up">
          <nav class="cart-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('frontend.index') }}">Home</a><i class="fa-solid fa-chevron-right"></i><a href="{{ route('frontend.my-cart') }}">Cart</a><i class="fa-solid fa-chevron-right"></i><span>Checkout</span></nav>
          <h1>Secure Checkout.<br><span>Stronger You.</span></h1>
        </div>
      </div>
    </section>

    <section class="checkout-main section-space">
      <div class="container">
        @if ($errors->any())
          <div class="alert alert-warning">
            Please complete the required checkout fields.
          </div>
        @endif

        @if ($cart['is_empty'])
          <div class="cart-empty-state">
            <h3>Your cart is empty.</h3>
            <p>Add products before checkout.</p>
            <a class="btn btn-gold" href="{{ route('frontend.shop') }}">Go to shop <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        @else
          <div class="checkout-layout">
            <form class="checkout-form reveal-on-scroll" method="POST" action="{{ route('frontend.order.store') }}">
              @csrf
              <section class="checkout-step">
                <div class="checkout-step-head">
                  <span>1</span>
                  <h2>Contact Information</h2>
                  <p>Already have an account? <a href="{{ route('frontend.my-cart') }}">Cart</a></p>
                </div>
                <label class="visually-hidden" for="checkoutEmail">Email Address</label>
                <input id="checkoutEmail" name="email" type="email" placeholder="Email Address" value="{{ old('email') }}" required>
                <label class="checkout-check"><input type="checkbox" checked> Email me with news and offers</label>
              </section>

              <section class="checkout-step">
                <div class="checkout-step-head">
                  <span>2</span>
                  <h2>Shipping Information</h2>
                </div>
                <div class="checkout-fields">
                  <label class="visually-hidden" for="fullName">Full Name</label>
                  <input id="fullName" name="customer_name" class="wide" type="text" placeholder="Full Name" value="{{ old('customer_name') }}" required>
                  <label class="visually-hidden" for="streetAddress">Street Address</label>
                  <input id="streetAddress" name="address" class="wide" type="text" placeholder="Street Address" value="{{ old('address') }}" required>
                  <label class="visually-hidden" for="city">City</label>
                  <input id="city" name="city" type="text" placeholder="City" value="{{ old('city') }}" required>
                  <label class="visually-hidden" for="state">State / Province</label>
                  <input id="state" name="state" type="text" placeholder="State / Province" value="{{ old('state') }}">
                  <label class="visually-hidden" for="zip">ZIP / Postal Code</label>
                  <input id="zip" name="zip" type="text" placeholder="ZIP / Postal Code" value="{{ old('zip') }}">
                  <label class="checkout-select" for="country"><span>Country</span><select id="country" name="country"><option @selected(old('country') === 'United States')>United States</option><option @selected(old('country') === 'Canada')>Canada</option><option @selected(old('country') === 'United Kingdom')>United Kingdom</option></select></label>
                  <label class="visually-hidden" for="phone">Phone Number</label>
                  <input id="phone" name="phone" class="span-2" type="tel" placeholder="Phone Number" value="{{ old('phone') }}">
                </div>
              </section>

              <section class="checkout-step">
                <div class="checkout-step-head">
                  <span>3</span>
                  <h2>Shipping Method</h2>
                </div>
                <label class="checkout-option active"><span><input type="radio" name="shipping_method" value="standard" checked> Standard Shipping (5-7 Business Days)</span><strong>£9.99</strong></label>
                <label class="checkout-option"><span><input type="radio" name="shipping_method" value="express"> Express Shipping (2-3 Business Days)</span><strong>£19.99</strong></label>
              </section>

              <section class="checkout-step">
                <div class="checkout-step-head">
                  <span>4</span>
                  <h2>Payment Method</h2>
                </div>
                <label class="checkout-option active payment-option"><span><input type="radio" name="payment_method" value="card" checked> Credit / Debit Card</span><div class="payment-badges mini-payments"><span>VISA</span><span></span><span>AMEX</span><span>DISC</span></div></label>
                <label class="checkout-option payment-option"><span><input type="radio" name="payment_method" value="paypal"> PayPal</span><strong class="paypal-mark">PayPal</strong></label>
                <label class="checkout-option payment-option"><span><input type="radio" name="payment_method" value="other"> Other Payment Methods</span><div class="crypto-marks"><span>BTC</span><span>ETH</span></div></label>
              </section>

              <button class="btn btn-gold w-100" type="submit">Place order <i class="fa-solid fa-lock"></i></button>
            </form>

            <aside class="checkout-summary reveal-on-scroll" aria-labelledby="checkoutSummaryTitle">
              <h2 id="checkoutSummaryTitle">Order Summary</h2>
              <div class="checkout-summary-items">
                @foreach ($cart['items'] as $item)
                  @php($product = $item['product'])
                  <article>
                    <img src="{{ url($product->image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $product->name }}">
                    <div><strong>{{ $product->name }}</strong><span>{{ $product->category?->name ?? 'Product' }}</span><small>Qty: {{ $item['quantity'] }}</small></div>
                    <b>£{{ number_format($item['line_total'], 2) }}</b>
                  </article>
                @endforeach
              </div>
              <div class="checkout-total-lines">
                <div><span>Subtotal ({{ $cart['item_count'] }} Items)</span><strong>£{{ number_format($cart['subtotal'], 2) }}</strong></div>
                <div><span>Shipping <i class="fa-regular fa-circle-question"></i></span><strong>£9.99</strong></div>
              </div>
              <div class="checkout-total"><span>Total</span><strong>£{{ number_format($cart['subtotal'] + 9.99, 2) }}</strong></div>
              <p class="checkout-secure"><i class="fa-solid fa-shield-halved"></i> Secure checkout</p>
            </aside>
          </div>
        @endif
      </div>
    </section>

    @include('frontend.inc.delivery-trusted')
@endsection

@section('js')
@endsection
