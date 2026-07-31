<?php

namespace App\Mail;

use App\Models\SiteSetting;
use App\Models\StockNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StockRequestMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public StockNotification $stockNotification)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [$this->stockNotification->email],
            subject: 'Stock request: ' . $this->stockNotification->product?->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.stock-request',
            with: [
                'logoUrl' => url(SiteSetting::getValue('header_logo', 'frontend/assets/images/logo/logo-transperent.png')),
            ],
        );
    }
}