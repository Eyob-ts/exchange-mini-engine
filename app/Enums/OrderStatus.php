<?php

namespace App\Enums;

enum OrderStatus: int
{
    case OPEN = 1;
    case FILLED = 2;
    case CANCELLED = 3;

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'open',
            self::FILLED => 'filled',
            self::CANCELLED => 'cancelled',
        };
    }
}
