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
            <form id="checkoutForm" class="checkout-form reveal-on-scroll" method="POST" action="{{ route('frontend.order.store') }}">
              @csrf
              <section class="checkout-step checkout-billing-card">
                <div class="checkout-step-head checkout-title-head">
                  <h2>Billing Details</h2>
                </div>

                <div class="checkout-fields checkout-fields-two">
                  <label class="checkout-field" for="firstName"><span>First Name</span><input id="firstName" name="first_name" type="text" value="{{ old('first_name') }}" required></label>
                  <label class="checkout-field" for="lastName"><span>Last Name</span><input id="lastName" name="last_name" type="text" value="{{ old('last_name') }}" required></label>

                  <label class="checkout-field" for="zip"><span>Post Code</span><input id="zip" name="zip" type="text" value="{{ old('zip') }}" data-uk-postcode required></label>
                  <div class="postcode-actions">
                    <button class="postcode-action-btn" type="button" data-find-postcode><i class="fa-solid fa-magnifying-glass"></i> Find Post Code</button>
                    <button class="postcode-action-btn" type="button" data-enter-manual>Enter Manually</button>
                    <button class="postcode-action-btn postcode-action-alt" type="button" data-use-postcode hidden><i class="fa-solid fa-magnifying-glass"></i> Use Post Code Search</button>
                  </div>

                  <label class="checkout-field" for="phone"><span>Phone</span><input id="phone" name="phone" type="tel" value="{{ old('phone') }}" data-uk-phone required></label>
                  <label class="checkout-field" for="checkoutEmail"><span>Email Address</span><input id="checkoutEmail" name="email" type="email" value="{{ old('email') }}" required></label>
                </div>

                <input type="hidden" name="country" value="United Kingdom">

                <div class="postcode-lookup-status" data-postcode-status aria-live="polite"></div>

                <div class="postcode-address-picker" data-postcode-address-picker hidden>
                  <label class="checkout-field wide" for="postcodeAddress"><span>Select Address</span>
                    <select id="postcodeAddress" data-postcode-address>
                      <option value="">Select your address</option>
                    </select>
                  </label>
                </div>

                <div class="manual-address-fields {{ old('address') || $errors->has('address') || $errors->has('city') ? 'is-visible' : '' }}" data-manual-address>
                  <div class="checkout-fields checkout-fields-two">
                    {{-- <label class="checkout-field wide" for="company"><span>Company Name</span><input id="company" name="company" type="text" value="{{ old('company') }}" placeholder="Optional"></label> --}}
                    <label class="checkout-field wide" for="streetAddress"><span>Street Address</span><input id="streetAddress" name="address" type="text" value="{{ old('address') }}" data-address-required></label>
                  </div>
                </div>

                <div class="checkout-additional">
                  <h3>Additional information</h3>
                  <label class="visually-hidden" for="orderNotes">Order notes</label>
                  <textarea id="orderNotes" name="order_notes" placeholder="Notes about your order...">{{ old('order_notes') }}</textarea>
                </div>

                <input type="hidden" name="shipping_method" value="uk_tracked">
                <input type="hidden" name="payment_method" value="card">
              </section>
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
              <button class="btn btn-gold w-100" type="submit" form="checkoutForm">Place order <i class="fa-solid fa-lock"></i></button>
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
