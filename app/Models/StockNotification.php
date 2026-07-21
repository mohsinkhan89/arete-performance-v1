<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'customer_name',
        'email',
        'phone',
        'quantity',
        'message',
        'status',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'notified_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
