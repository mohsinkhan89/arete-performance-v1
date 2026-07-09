@php
    $paymentStatus = $order->payment_status ?? 'unpaid';
    $proofUrl = $order->payment_proof ? url($order->payment_proof) : null;
@endphp

<div class="payment-proof-widget">
    <span class="badge payment-status-{{ $paymentStatus }}">{{ str_replace('_', ' ', ucfirst($paymentStatus)) }}</span>

    @if ($proofUrl)
        <a class="payment-proof-link" href="{{ $proofUrl }}" target="_blank" rel="noopener">
            <i class="fa-regular fa-image"></i> Proof
        </a>
    @endif

    <form class="payment-proof-form" action="{{ route('backend.orders.payment-proof', $order) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <label>
            <span class="visually-hidden">Payment proof</span>
            <input type="file" name="payment_proof_file" accept="image/png,image/jpeg,image/webp">
        </label>
        <button type="submit" title="Upload proof and mark paid">
            <i class="fa-solid fa-check"></i>
            {{ $paymentStatus === 'paid' ? 'Update Proof' : 'Mark Paid' }}
        </button>
    </form>
</div>
