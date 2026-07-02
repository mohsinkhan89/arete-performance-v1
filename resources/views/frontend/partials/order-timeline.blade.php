@php
  $current = $order->tracking_status ?? 'placed';
  $steps = [
    'placed' => ['icon' => 'fa-envelope-circle-check', 'label' => 'Order Placed', 'text' => $order->created_at?->format('M d, Y - h:i A')],
    'processing' => ['icon' => 'fa-gears', 'label' => 'Processing', 'text' => 'We are checking your order and payment'],
    'packed' => ['icon' => 'fa-box-open', 'label' => 'Packed', 'text' => 'Your items are being prepared'],
    'dispatched' => ['icon' => 'fa-truck-fast', 'label' => 'Dispatched', 'text' => $order->tracking_number ? 'Tracking: ' . $order->tracking_number : 'Tracking info will be shared once ready'],
    'out_for_delivery' => ['icon' => 'fa-route', 'label' => 'Out For Delivery', 'text' => 'Courier is on the way'],
    'delivered' => ['icon' => 'fa-house-circle-check', 'label' => 'Delivered', 'text' => 'Order completed'],
  ];
  $keys = array_keys($steps);
  $foundIndex = array_search($current, $keys, true);
  $activeIndex = $foundIndex === false ? 0 : $foundIndex;
@endphp

<div class="order-timeline">
  @foreach ($steps as $key => $step)
    @php $isActive = array_search($key, $keys, true) <= $activeIndex && $current !== 'cancelled'; @endphp
    <div class="timeline-item {{ $isActive ? 'active' : '' }}">
      <i class="fa-solid {{ $step['icon'] }}"></i>
      <div><strong>{{ $step['label'] }}</strong><span>{{ $step['text'] }}</span></div>
    </div>
  @endforeach
  @if ($current === 'cancelled')
    <div class="timeline-item active"><i class="fa-solid fa-circle-xmark"></i><div><strong>Cancelled</strong><span>{{ $order->tracking_note ?: 'This order has been cancelled.' }}</span></div></div>
  @elseif ($order->tracking_note)
    <div class="timeline-item active"><i class="fa-solid fa-circle-info"></i><div><strong>Latest Update</strong><span>{{ $order->tracking_note }}</span></div></div>
  @endif
</div>
