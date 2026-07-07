@extends('frontend.layouts.master')

@section('body')
  <section class="search-hero">
    <div class="container">
      <div class="search-hero-copy reveal-up">
        <h1>Track <span>Order</span></h1>
        <p>Enter your order number and email to see where your order is right now.</p>
        <form class="search-hero-form track-order-form" action="{{ route('frontend.track-order') }}" method="GET">
          <input type="text" name="order_number" value="{{ request('order_number') }}" placeholder="Order number" required>
          <input type="email" name="email" value="{{ request('email') }}" placeholder="Email address" required>
          <button class="btn btn-gold" type="submit">Track <i class="fa-solid fa-arrow-right"></i></button>
        </form>
      </div>
    </div>
  </section>

  <section class="success-main section-space">
    <div class="container">
      @if (session('success'))
        <div class="track-alert">{{ session('success') }}</div>
      @endif

      @if ($order)
        <div class="success-grid">
          <aside class="success-side">
            <article class="success-card">
              <h2><i class="fa-solid fa-truck-fast"></i> Current Status</h2>
              <p><strong>{{ str_replace('_', ' ', ucfirst($order->tracking_status ?? 'placed')) }}</strong></p>
              @if ($order->tracking_number)
                <p>Tracking number: <strong>{{ $order->tracking_number }}</strong></p>
              @endif
            </article>
            <article class="success-card">
              <h2>Order Timeline</h2>
              @include('frontend.partials.order-timeline', ['order' => $order])
            </article>
          </aside>

          <section class="success-card order-detail-card">
            <h2><i class="fa-regular fa-rectangle-list"></i> Order #{{ $order->order_number }}</h2>
            <dl class="order-detail-list">
              <div><dt>Customer</dt><dd>{{ $order->customer_name }}</dd></div>
              <div><dt>Payment</dt><dd>{{ str_replace('_', ' ', ucfirst($order->payment_status ?? 'unpaid')) }}</dd></div>
              <div><dt>Royal Mail ID</dt><dd>{{ $order->tracking_number ?: 'Preparing label' }}</dd></div>
              <div><dt>Total</dt><dd>£{{ number_format((float) $order->total, 2) }}</dd></div>
              <div><dt>Address</dt><dd>{{ $order->address }}, {{ $order->city }}, {{ $order->zip }}</dd></div>
            </dl>

            @if (($order->payment_status ?? 'unpaid') !== 'paid')
              <form class="payment-proof-form" action="{{ route('frontend.payment-proof.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="order_number" value="{{ $order->order_number }}">
                <input type="hidden" name="email" value="{{ $order->email }}">
                <label>
                  <span>Payment proof screenshot/PDF</span>
                  <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.webp,.pdf" required>
                </label>
                <button class="btn btn-gold" type="submit">Submit payment proof <i class="fa-solid fa-upload"></i></button>
              </form>
            @endif
          </section>

          <aside class="success-card success-summary">
            <h2>Items</h2>
            <div class="checkout-summary-items">
              @foreach ($order->items as $item)
                <article>
                  <img src="{{ url($item->product_image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $item->product_name }}">
                  <div><strong>{{ $item->product_name }}</strong><span>{{ $item->product_sku }}</span><small>Qty: {{ $item->quantity }}</small></div>
                  <b>£{{ number_format((float) $item->line_total, 2) }}</b>
                </article>
              @endforeach
            </div>
          </aside>
        </div>
      @else
        <section class="track-empty-panel">
          <div class="track-empty-copy">
            <span class="track-empty-kicker">
              <i class="fa-solid {{ $searched ? 'fa-magnifying-glass' : 'fa-location-crosshairs' }}"></i>
              {{ $searched ? 'Search result' : 'Order tracking' }}
            </span>
            <h2>{{ $searched ? 'No order found yet' : 'Ready to track your order?' }}</h2>
            <p>
              {{ $searched
                ? 'We could not match that order number with the email address. Check both details exactly as they appear on your confirmation email and search again.'
                : 'Enter your order number and email above to see the latest status, Royal Mail ID, order timeline, payment update, and item details in one place.' }}
            </p>
            <div class="track-empty-actions">
              <a class="btn btn-gold" href="{{ route('frontend.shop') }}">Continue shopping <i class="fa-solid fa-arrow-right"></i></a>
              <a class="btn btn-outline-dark" href="mailto:support@areteperformance.co.uk">Contact support <i class="fa-regular fa-envelope"></i></a>
            </div>
          </div>
          <div class="track-empty-steps" aria-label="Tracking help">
            <div>
              <i class="fa-regular fa-envelope-open"></i>
              <strong>Check your email</strong>
              <span>Use the same email address from checkout.</span>
            </div>
            <div>
              <i class="fa-solid fa-hashtag"></i>
              <strong>Enter order number</strong>
              <span>Copy the full order number without extra spaces.</span>
            </div>
            <div>
              <i class="fa-solid fa-truck-fast"></i>
              <strong>View live details</strong>
              <span>If the order is found, the current details show here.</span>
            </div>
          </div>
        </section>
      @endif
    </div>
  </section>
@endsection
