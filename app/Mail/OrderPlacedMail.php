<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlacedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Order $order, public bool $adminNotification = false)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->adminNotification
                ? 'New order received #' . $this->order->order_number
                : 'Your Arete Performance order #' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-placed',
            with: [
                'logoUrl' => url(SiteSetting::getValue('header_logo', 'frontend/assets/images/logo/logo-transperent.png')),
            ],
        );
    }
}
