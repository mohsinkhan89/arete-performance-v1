@extends('frontend.layouts.master')

@section('metas')
@endsection

@section('css')
@endsection

@section('body')

    <section class="cart-hero">
      <div class="container">
        <nav class="cart-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('frontend.index') }}">Home</a><i class="fa-solid fa-chevron-right"></i><span>Cart</span></nav>
        <div class="cart-hero-copy reveal-up">
          <h1>Your <span>Cart</span></h1>
          <p>Review your items and proceed to checkout.</p>
        </div>
      </div>
    </section>

    <section class="cart-main section-space">
      <div class="container">
        <div class="cart-layout">
          <section class="cart-items-panel reveal-on-scroll" aria-labelledby="cartItemsTitle">
            <h2 id="cartItemsTitle">Cart Items (3)</h2>
            <div class="cart-table" role="table" aria-label="Cart items">
              <div class="cart-row cart-row-head" role="row">
                <span role="columnheader">Product</span>
                <span role="columnheader">Price</span>
                <span role="columnheader">Quantity</span>
                <span role="columnheader">Total</span>
                <span role="columnheader" class="visually-hidden">Remove</span>
              </div>
              <article class="cart-row" role="row">
                <div class="cart-product-cell" role="cell">
                  <img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Anavar 50">
                  <div><strong>Anavar 50 (Oxandrolone)</strong><span>90 Capsules</span></div>
                </div>
                <strong role="cell">$59.99</strong>
                <div class="cart-stepper" role="cell" aria-label="Anavar 50 quantity"><button type="button">-</button><span>1</span><button type="button">+</button></div>
                <strong role="cell">$59.99</strong>
                <button class="cart-remove" type="button" aria-label="Remove Anavar 50"><i class="fa-regular fa-trash-can"></i></button>
              </article>
              <article class="cart-row" role="row">
                <div class="cart-product-cell" role="cell">
                  <img src="{{ url('frontend/assets/images/categories-imgs/fat-burrners.png') }}" alt="Clenbuterol">
                  <div><strong>Clenbuterol 40mcg</strong><span>90 Tablets</span></div>
                </div>
                <strong role="cell">$49.99</strong>
                <div class="cart-stepper" role="cell" aria-label="Clenbuterol quantity"><button type="button">-</button><span>2</span><button type="button">+</button></div>
                <strong role="cell">$99.98</strong>
                <button class="cart-remove" type="button" aria-label="Remove Clenbuterol"><i class="fa-regular fa-trash-can"></i></button>
              </article>
              <article class="cart-row" role="row">
                <div class="cart-product-cell" role="cell">
                  <img src="{{ url('frontend/assets/images/category-boxes.svg') }}" alt="PCT Complete Stack">
                  <div><strong>PCT Complete Stack</strong><span>30 Servings</span></div>
                </div>
                <strong role="cell">$89.99</strong>
                <div class="cart-stepper" role="cell" aria-label="PCT Complete Stack quantity"><button type="button">-</button><span>1</span><button type="button">+</button></div>
                <strong role="cell">$89.99</strong>
                <button class="cart-remove" type="button" aria-label="Remove PCT Complete Stack"><i class="fa-regular fa-trash-can"></i></button>
              </article>
            </div>
            <div class="cart-panel-actions">
              <a class="cart-secondary-btn" href="{{ route('frontend.shop') }}"><i class="fa-solid fa-arrow-left"></i> Continue shopping</a>
              <button class="cart-secondary-btn" type="button">Clear cart <i class="fa-regular fa-trash-can"></i></button>
            </div>
          </section>

          <aside class="order-summary reveal-on-scroll" aria-labelledby="orderSummaryTitle">
            <h2 id="orderSummaryTitle">Order Summary</h2>
            <div class="summary-line"><span>Subtotal (3 items)</span><strong>$249.96</strong></div>
            <div class="summary-line"><span>Shipping <i class="fa-regular fa-circle-question"></i></span><span>Calculated at checkout</span></div>
            <div class="summary-line"><span>Tax <i class="fa-regular fa-circle-question"></i></span><span>Calculated at checkout</span></div>
            <div class="summary-total"><span>Estimated Total</span><strong>$249.96</strong></div>
            <a class="btn btn-gold w-100" href="{{ route('frontend.checkout') }}">Proceed to checkout <i class="fa-solid fa-arrow-right"></i></a>
            <p>We accept</p>
            <div class="payment-badges cart-payments"><span>VISA</span><span></span><span>AMEX</span><span>PayPal</span><span>BTC</span></div>
          </aside>
        </div>

       <section class="bundle-banner reveal-on-scroll" aria-label="Bundle offer">
          <div>
          </div>
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

    <section class="shop-newsletter">
      <div class="container">
        <div class="shop-newsletter-inner">
          <div>
            <p class="eyebrow">Stay in the loop</p>
            <h2>Exclusive deals. Expert tips. Straight to your inbox.</h2>
            <p>Join the Arete Performance community.</p>
          </div>
          <form class="newsletter">
            <label class="visually-hidden" for="cartEmail">Email</label>
            <input id="cartEmail" type="email" placeholder="Enter your email" required>
            <button type="submit">Subscribe</button>
          </form>
        </div>
      </div>
    </section>

@endsection

@section('js')
@endsection
