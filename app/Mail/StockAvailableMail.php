<?php

namespace App\Mail;

use App\Models\SiteSetting;
use App\Models\StockNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StockAvailableMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public StockNotification $stockNotification)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->stockNotification->product?->name . ' is available now',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.stock-available',
            with: [
                'logoUrl' => url(SiteSetting::getValue('header_logo', 'frontend/assets/images/logo/logo-transperent.png')),
            ],
        );
    }
}
