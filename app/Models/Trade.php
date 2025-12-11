<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends Model
{
    protected $fillable = [
        'buy_order_id',
        'sell_order_id',
        'symbol',
        'price',
        'amount',
        'volume',
        'fee',
    ];

    protected $casts = [
        'price' => 'decimal:8',
        'amount' => 'decimal:16',
        'volume' => 'decimal:8',
        'fee' => 'decimal:8',
    ];

    public function buyerOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'buy_order_id');
    }

    public function sellerOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'sell_order_id');
    }
}
