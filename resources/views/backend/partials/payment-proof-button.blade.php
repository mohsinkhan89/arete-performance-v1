@php
    $paymentStatus = $order->payment_status ?? 'unpaid';
    $proofUrl = $order->payment_proof ? url($order->payment_proof) : null;
@endphp

<button
    class="payment-proof-icon"
    type="button"
    title="{{ $paymentStatus === 'paid' ? 'Update payment proof' : 'Upload proof and mark paid' }}"
    data-payment-proof-open
    data-action="{{ route('backend.orders.payment-proof', $order) }}"
    data-order-number="{{ $order->order_number }}"
    data-customer="{{ $order->customer_name }}"
    data-total="&pound;{{ number_format((float) $order->total, 2) }}"
    data-status="{{ str_replace('_', ' ', ucfirst($paymentStatus)) }}"
    data-proof-url="{{ $proofUrl }}"
>
    <i class="fa-solid fa-receipt"></i>
</button>
