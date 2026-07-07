<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
