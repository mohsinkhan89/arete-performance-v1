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
                  <h2>Billing Details</h2>
                </div>
                <div class="checkout-fields">
                  <label class="visually-hidden" for="firstName">First name</label>
                  <input id="firstName" name="first_name" type="text" placeholder="First name *" value="{{ old('first_name') }}" required>
                  <label class="visually-hidden" for="lastName">Last name</label>
                  <input id="lastName" name="last_name" type="text" placeholder="Last name *" value="{{ old('last_name') }}" required>
                  <label class="visually-hidden" for="company">Company name</label>
                  <input id="company" name="company" class="wide" type="text" placeholder="Company name (optional)" value="{{ old('company') }}">
                  <label class="checkout-select wide" for="country"><span>Country / Region</span><select id="country" name="country" required><option value="United Kingdom" selected>United Kingdom (UK)</option></select></label>
                  <label class="visually-hidden" for="streetAddress">Street address</label>
                  <input id="streetAddress" name="address" class="wide" type="text" placeholder="House number and street name *" value="{{ old('address') }}" required>
                  <label class="visually-hidden" for="addressTwo">Apartment, suite, unit</label>
                  <input id="addressTwo" name="address_2" class="wide" type="text" placeholder="Apartment, suite, unit, etc. (optional)" value="{{ old('address_2') }}">
                  <label class="visually-hidden" for="city">Town / City</label>
                  <input id="city" name="city" type="text" placeholder="Town / City *" value="{{ old('city') }}" required>
                  <label class="visually-hidden" for="state">County</label>
                  <input id="state" name="state" type="text" placeholder="County (optional)" value="{{ old('state') }}">
                  <label class="visually-hidden" for="zip">Postcode</label>
                  <input id="zip" name="zip" type="text" placeholder="Postcode *" value="{{ old('zip') }}" data-uk-postcode required>
                  <label class="visually-hidden" for="phone">Phone</label>
                  <input id="phone" name="phone" type="tel" placeholder="Phone *" value="{{ old('phone') }}" data-uk-phone required>
                  <label class="visually-hidden" for="orderNotes">Order notes</label>
                  <textarea id="orderNotes" name="order_notes" class="wide" placeholder="Order notes (optional)">{{ old('order_notes') }}</textarea>
                </div>
              </section>

              <section class="checkout-step">
                <div class="checkout-step-head">
                  <span>3</span>
                  <h2>Shipping Method</h2>
                </div>
                <label class="checkout-option active"><span><input type="radio" name="shipping_method" value="uk_tracked" checked> UK tracked postage (24 hours tracked service)</span><strong>£4.99</strong></label>
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
                <div><span>UK tracked postage <i class="fa-regular fa-circle-question"></i></span><strong>£4.99</strong></div>
              </div>
              <div class="checkout-total"><span>Total</span><strong>£{{ number_format($cart['subtotal'] + 4.99, 2) }}</strong></div>
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
