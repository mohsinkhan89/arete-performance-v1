@extends('frontend.layouts.master')

@section('body')
    <section class="success-hero">
      <div class="container">
        <div class="success-hero-copy reveal-up">
          <div class="success-check" aria-hidden="true"><i class="fa-solid fa-check"></i></div>
          <h1>Order Placed <span>Successfully!</span></h1>
          @if ($order)
            <p>Thank you for your order. Your purchase has been confirmed and saved.</p>
            <p class="success-order-id">Order <strong>#{{ $order->order_number }}</strong></p>
            <div class="success-actions">
              <a class="btn btn-gold" href="#orderDetails">View order details <i class="fa-solid fa-arrow-right"></i></a>
              <a class="btn btn-outline-light" href="{{ route('frontend.track-order', ['order_number' => $order->order_number, 'email' => $order->email]) }}">Track order <i class="fa-solid fa-truck-fast"></i></a>
            </div>
          @else
            <p>No recent order found.</p>
            <a class="btn btn-gold" href="{{ route('frontend.shop') }}">Continue shopping <i class="fa-solid fa-arrow-right"></i></a>
          @endif
        </div>
      </div>
    </section>

    @if ($order)
      <section class="success-main section-space">
        <div class="container">
          <div class="success-grid">
            <aside class="success-side">
              <article class="success-card reveal-on-scroll">
                <h2><i class="fa-regular fa-clipboard"></i> Order Confirmation</h2>
                <p>Confirmation is ready for <a href="mailto:{{ $order->email }}">{{ $order->email }}</a>.</p>
              </article>

              <article class="success-card reveal-on-scroll">
                <h2>What's Next?</h2>
                @include('frontend.partials.order-timeline', ['order' => $order])
              </article>

              <article class="success-card reveal-on-scroll">
                <h2><i class="fa-solid fa-receipt"></i> Payment Proof</h2>
                <p>Status: <strong>{{ str_replace('_', ' ', ucfirst($order->payment_status ?? 'unpaid')) }}</strong></p>
                @if (($order->payment_status ?? 'unpaid') !== 'paid')
                  <form class="payment-proof-form" action="{{ route('frontend.payment-proof.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="order_number" value="{{ $order->order_number }}">
                    <input type="hidden" name="email" value="{{ $order->email }}">
                    <label>
                      <span>Upload screenshot or PDF</span>
                      <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.webp,.pdf" required>
                    </label>
                    <button class="btn btn-gold" type="submit">Submit proof <i class="fa-solid fa-upload"></i></button>
                  </form>
                @else
                  <p>Your payment has been verified.</p>
                @endif
              </article>
            </aside>

            <section class="success-card order-detail-card reveal-on-scroll" id="orderDetails">
              <h2><i class="fa-regular fa-rectangle-list"></i> Order Details</h2>
              <dl class="order-detail-list">
                <div><dt>Order Number</dt><dd>{{ $order->order_number }}</dd></div>
                <div><dt>Order Date</dt><dd>{{ $order->created_at?->format('M d, Y') }}</dd></div>
                <div><dt>Payment Method</dt><dd>{{ ucfirst($order->payment_method) }}</dd></div>
                <div><dt>Payment Status</dt><dd>{{ str_replace('_', ' ', ucfirst($order->payment_status ?? 'unpaid')) }}</dd></div>
                <div><dt>Order Status</dt><dd>{{ str_replace('_', ' ', ucfirst($order->tracking_status ?? $order->status)) }}</dd></div>
                <div><dt>Royal Mail Tracking ID</dt><dd>{{ $order->tracking_number ?: 'Preparing label' }}</dd></div>
                <div><dt>Shipping Method</dt><dd>{{ ucfirst($order->shipping_method) }}</dd></div>
                <div><dt>Shipping Address</dt><dd><strong>{{ $order->customer_name }}</strong><br>@if($order->company){{ $order->company }}<br>@endif{{ $order->address }}<br>@if($order->address_2){{ $order->address_2 }}<br>@endif{{ $order->city }}, {{ $order->state }} {{ $order->zip }}<br>{{ $order->country }}<br>{{ $order->phone }}</dd></div>
                <div class="paid-line"><dt>Total</dt><dd>£{{ number_format((float) $order->total, 2) }}</dd></div>
              </dl>
            </section>

            <aside class="success-card success-summary reveal-on-scroll" aria-labelledby="successSummaryTitle">
              <h2 id="successSummaryTitle">Order Summary ({{ $order->items->sum('quantity') }} Items)</h2>
              <div class="checkout-summary-items">
                @foreach ($order->items as $item)
                  <article>
                    <img src="{{ url($item->product_image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $item->product_name }}">
                    <div><strong>{{ $item->product_name }}</strong><span>{{ $item->product_sku }}</span><small>Qty: {{ $item->quantity }}</small></div>
                    <b>£{{ number_format((float) $item->line_total, 2) }}</b>
                  </article>
                @endforeach
              </div>
              <div class="checkout-total-lines">
                <div><span>Subtotal</span><strong>£{{ number_format((float) $order->subtotal, 2) }}</strong></div>
                <div><span>Shipping</span><strong>£{{ number_format((float) $order->shipping_total, 2) }}</strong></div>
              </div>
              <div class="checkout-total"><span>Total</span><strong>£{{ number_format((float) $order->total, 2) }}</strong></div>
            </aside>
          </div>
        </div>
      </section>
    @endif

    @include('frontend.inc.delivery-trusted')
@endsection
