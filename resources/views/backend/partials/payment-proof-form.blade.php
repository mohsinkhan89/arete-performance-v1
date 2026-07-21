@php
    $paymentStatus = $order->payment_status ?? 'unpaid';
    $proofUrl = $order->payment_proof ? url($order->payment_proof) : null;
@endphp

<div class="payment-proof-widget">
    <span class="badge payment-status-{{ $paymentStatus }}">{{ str_replace('_', ' ', ucfirst($paymentStatus)) }}</span>

    <div class="payment-proof-actions">
        @include('backend.partials.payment-proof-button', ['order' => $order])
    </div>
</div>
