<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'image',
        'test_report_image',
        'price',
        'sale_price',
        'stock',
        'status',
        'is_featured',
        'is_bestseller',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock' => 'integer',
            'is_featured' => 'boolean',
            'is_bestseller' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockNotifications(): HasMany
    {
        return $this->hasMany(StockNotification::class);
    }
}
