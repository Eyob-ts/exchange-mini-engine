<?php

namespace App\Enums;

enum OrderSide: string
{
    case BUY = 'buy';
    case SELL = 'sell';

    public function label(): string
    {
        return match ($this) {
            self::BUY => 'Buy',
            self::SELL => 'Sell',
        };
    }
}
