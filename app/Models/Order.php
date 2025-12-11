<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\OrderSide;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'symbol',
        'side',
        'price',
        'amount',
        'locked_usd',
        'status',
    ];

    protected $casts = [
        'side' => OrderSide::class,
        'status' => OrderStatus::class,
        'price' => 'decimal:8',
        'amount' => 'decimal:16',
        'locked_usd' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tradesAsBuyer(): HasMany
    {
        return $this->hasMany(Trade::class, 'buy_order_id');
    }

    public function tradesAsSeller(): HasMany
    {
        return $this->hasMany(Trade::class, 'sell_order_id');
    }
}
