@extends('frontend.layouts.master')

@section('metas')
@endsection

@section('css')
@endsection

@section('body')

    <section class="checkout-hero">
      <div class="container">
        <div class="checkout-hero-copy reveal-up">
          <nav class="cart-breadcrumb" aria-label="Breadcrumb"><a href="index.html">Home</a><i class="fa-solid fa-chevron-right"></i><a href="my-cart.html">Cart</a><i class="fa-solid fa-chevron-right"></i><span>Checkout</span></nav>
          <h1>Secure Checkout.<br><span>Stronger You.</span></h1>
        </div>
      </div>
    </section>

    <section class="checkout-main section-space">
      <div class="container">
        <div class="checkout-layout">
          <form class="checkout-form reveal-on-scroll">
            <section class="checkout-step">
              <div class="checkout-step-head">
                <span>1</span>
                <h2>Contact Information</h2>
                <p>Already have an account? <a href="my-cart.html">Log in</a></p>
              </div>
              <label class="visually-hidden" for="checkoutEmail">Email Address</label>
              <input id="checkoutEmail" type="email" placeholder="Email Address">
              <label class="checkout-check"><input type="checkbox" checked> Email me with news and offers</label>
            </section>

            <section class="checkout-step">
              <div class="checkout-step-head">
                <span>2</span>
                <h2>Shipping Information</h2>
              </div>
              <div class="checkout-fields">
                <label class="visually-hidden" for="fullName">Full Name</label>
                <input id="fullName" class="wide" type="text" placeholder="Full Name">
                <label class="visually-hidden" for="streetAddress">Street Address</label>
                <input id="streetAddress" class="wide" type="text" placeholder="Street Address">
                <label class="visually-hidden" for="city">City</label>
                <input id="city" type="text" placeholder="City">
                <label class="visually-hidden" for="state">State / Province</label>
                <input id="state" type="text" placeholder="State / Province">
                <label class="visually-hidden" for="zip">ZIP / Postal Code</label>
                <input id="zip" type="text" placeholder="ZIP / Postal Code">
                <label class="checkout-select" for="country"><span>Country</span><select id="country"><option>United States</option><option>Canada</option><option>United Kingdom</option></select></label>
                <label class="visually-hidden" for="phone">Phone Number</label>
                <input id="phone" class="span-2" type="tel" placeholder="Phone Number">
              </div>
            </section>

            <section class="checkout-step">
              <div class="checkout-step-head">
                <span>3</span>
                <h2>Shipping Method</h2>
              </div>
              <label class="checkout-option active"><span><input type="radio" name="shipping" checked> Standard Shipping (5-7 Business Days)</span><strong>$9.99</strong></label>
              <label class="checkout-option"><span><input type="radio" name="shipping"> Express Shipping (2-3 Business Days)</span><strong>$19.99</strong></label>
            </section>

            <section class="checkout-step">
              <div class="checkout-step-head">
                <span>4</span>
                <h2>Payment Method</h2>
              </div>
              <label class="checkout-option active payment-option"><span><input type="radio" name="payment" checked> Credit / Debit Card</span><div class="payment-badges mini-payments"><span>VISA</span><span></span><span>AMEX</span><span>DISC</span></div></label>
              <label class="checkout-option payment-option"><span><input type="radio" name="payment"> PayPal</span><strong class="paypal-mark">PayPal</strong></label>
              <label class="checkout-option payment-option"><span><input type="radio" name="payment"> Other Payment Methods</span><div class="crypto-marks"><span>BTC</span><span>ETH</span></div></label>
            </section>
          </form>

          <aside class="checkout-summary reveal-on-scroll" aria-labelledby="checkoutSummaryTitle">
            <h2 id="checkoutSummaryTitle">Order Summary</h2>
            <div class="checkout-summary-items">
              <article>
                <img src="{{ url('frontend/assets/images/product-bottle.png') }}" alt="Anavar 50">
                <div><strong>Anavar 50</strong><span>(Oxandrolone)</span><small>Qty: 1</small></div>
                <b>$59.99</b>
              </article>
              <article>
                <img src="{{ url('frontend/assets/images/category-boxes.svg') }}    " alt="PCT Complete Stack">
                <div><strong>PCT Complete Stack</strong><span>Post Cycle Therapy</span><small>Qty: 1</small></div>
                <b>$89.99</b>
              </article>
              <article>
                <img src="{{ url('frontend/assets/images/categories-imgs/peptides.png') }}" alt="CJC-1295">
                <div><strong>CJC-1295 2mg</strong><span>Peptides</span><small>Qty: 2</small></div>
                <b>$149.98</b>
              </article>
            </div>
            <div class="checkout-total-lines">
              <div><span>Subtotal (3 Items)</span><strong>$299.96</strong></div>
              <div><span>Shipping <i class="fa-regular fa-circle-question"></i></span><strong>$9.99</strong></div>
              <div class="discount"><span>Discount (ARETE10)</span><strong>- $29.99</strong></div>
            </div>
            <div class="checkout-total"><span>Total</span><strong>$279.96</strong></div>
            <p class="checkout-savings"><i class="fa-solid fa-shield-heart"></i> You saved $29.99 with this order!</p>
            <button class="btn btn-gold w-100" type="button" data-go="order-success.html">Place order <i class="fa-solid fa-lock"></i></button>
            <p class="checkout-secure"><i class="fa-solid fa-shield-halved"></i> Secure 256-bit SSL encrypted checkout</p>
          </aside>
        </div>
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
            <label class="visually-hidden" for="checkoutNewsletter">Email</label>
            <input id="checkoutNewsletter" type="email" placeholder="Enter your email" required>
            <button type="submit">Subscribe</button>
          </form>
        </div>
      </div>
    </section>

@endsection

@section('js')
@endsection