<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentProofStatusMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Order $order,
        public string $decision,
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = $this->decision === 'accepted'
            ? 'Payment verified for Arete Performance order #' . $this->order->order_number
            : 'Payment proof update for Arete Performance order #' . $this->order->order_number;

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-proof-status');
    }
}
