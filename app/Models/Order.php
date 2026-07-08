<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'company',
        'email',
        'phone',
        'address',
        'address_2',
        'city',
        'state',
        'zip',
        'country',
        'shipping_method',
        'payment_method',
        'payment_status',
        'payment_proof',
        'payment_proof_submitted_at',
        'order_notes',
        'subtotal',
        'shipping_total',
        'total',
        'status',
        'tracking_status',
        'tracking_number',
        'tracking_note',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_total' => 'decimal:2',
            'total' => 'decimal:2',
            'payment_proof_submitted_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function whatsappPhone(): string
    {
        $phone = preg_replace('/\D+/', '', (string) $this->phone);

        if (str_starts_with($phone, '0')) {
            return '44' . substr($phone, 1);
        }

        return $phone;
    }

    public function whatsappMessage(): string
    {
        $order = $this->relationLoaded('items') ? $this : $this->loadMissing('items');
        $items = $order->items instanceof Collection ? $order->items : collect();
        $trackUrl = route('frontend.track-order', [
            'order_number' => $this->order_number,
            'email' => $this->email,
        ]);

        $itemLines = $items->map(function (OrderItem $item) {
            return '- ' . $item->product_name
                . ' x ' . $item->quantity
                . ' @ £' . number_format((float) $item->unit_price, 2)
                . ' = £' . number_format((float) $item->line_total, 2);
        })->implode("\n");

        $address = collect([
            $this->address,
            $this->address_2,
            $this->city,
            $this->state,
            $this->zip,
            $this->country,
        ])->filter()->implode(', ');

        return trim("Hi {$this->customer_name},\n\nArete Performance order #{$this->order_number}\n\nItems:\n{$itemLines}\n\nSubtotal: £" . number_format((float) $this->subtotal, 2) . "\nShipping: £" . number_format((float) $this->shipping_total, 2) . "\nTotal: £" . number_format((float) $this->total, 2) . "\n\nPayment: " . str_replace('_', ' ', ucfirst($this->payment_status ?? 'unpaid')) . "\nTracking: " . str_replace('_', ' ', ucfirst($this->tracking_status ?? 'placed')) . "\nRoyal Mail ID: " . ($this->tracking_number ?: 'Pending') . "\n\nDelivery address:\n{$address}\n\nTrack or upload payment proof:\n{$trackUrl}\n\nThank you for ordering with Arete Performance.");
    }

    public function whatsappUrl(): string
    {
        return 'https://wa.me/' . $this->whatsappPhone() . '?text=' . rawurlencode($this->whatsappMessage());
    }
}
