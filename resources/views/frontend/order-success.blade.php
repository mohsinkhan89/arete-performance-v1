@extends('frontend.layouts.master')

@section('metas')
@endsection

@section('css')
@endsection

@section('body')
    <section class="success-hero">
      <div class="container">
        <div class="success-hero-copy reveal-up">
          <div class="success-check" aria-hidden="true"><i class="fa-solid fa-check"></i></div>
          <h1>Order Placed <span>Successfully!</span></h1>
          @if ($order)
            <p>Thank you for your order. Your purchase has been confirmed and saved.</p>
            <p class="success-order-id">Order <strong>#{{ $order->order_number }}</strong></p>
            <a class="btn btn-gold" href="#orderDetails">View order details <i class="fa-solid fa-arrow-right"></i></a>
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
                <div class="order-timeline">
                  <div class="timeline-item active"><i class="fa-solid fa-envelope-circle-check"></i><div><strong>Order Confirmed</strong><span>{{ $order->created_at?->format('M d, Y - h:i A') }}</span></div></div>
                  <div class="timeline-item"><i class="fa-solid fa-box-open"></i><div><strong>Order Processing</strong><span>We are preparing your order</span></div></div>
                  <div class="timeline-item"><i class="fa-solid fa-truck-fast"></i><div><strong>Shipped</strong><span>Tracking info will be shared once ready</span></div></div>
                  <div class="timeline-item"><i class="fa-solid fa-house-circle-check"></i><div><strong>Delivered</strong><span>Estimated delivery to your doorstep</span></div></div>
                </div>
              </article>
            </aside>

            <section class="success-card order-detail-card reveal-on-scroll" id="orderDetails">
              <h2><i class="fa-regular fa-rectangle-list"></i> Order Details</h2>
              <dl class="order-detail-list">
                <div><dt>Order Number</dt><dd>{{ $order->order_number }}</dd></div>
                <div><dt>Order Date</dt><dd>{{ $order->created_at?->format('M d, Y') }}</dd></div>
                <div><dt>Payment Method</dt><dd>{{ ucfirst($order->payment_method) }}</dd></div>
                <div><dt>Shipping Method</dt><dd>{{ ucfirst($order->shipping_method) }}</dd></div>
                <div><dt>Shipping Address</dt><dd><strong>{{ $order->customer_name }}</strong><br>{{ $order->address }}<br>{{ $order->city }}, {{ $order->state }} {{ $order->zip }}<br>{{ $order->country }}<br>{{ $order->phone }}</dd></div>
                <div class="paid-line"><dt>Total Paid</dt><dd>£{{ number_format((float) $order->total, 2) }}</dd></div>
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

@section('js')
@endsection
