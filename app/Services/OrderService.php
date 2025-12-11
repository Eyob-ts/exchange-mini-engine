<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Enums\OrderSide;
use App\Enums\OrderStatus;
use App\Jobs\MatchOrderJob;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    /**
     * Create a new order and deduct/lock balance.
     */
    public function createOrder(User $user, string $symbol, OrderSide $side, float $price, float $amount): Order
    {
        return DB::transaction(function () use ($user, $symbol, $side, $price, $amount) {
            // Lock user for update to prevent race conditions on balance
            $user = User::where('id', $user->id)->lockForUpdate()->first();

            if ($side === OrderSide::BUY) {
                $totalCost = $price * $amount;
                // Check USD balance
                if ($user->balance < $totalCost) {
                    throw new Exception('Insufficient USD balance');
                }
                // Deduct balance and lock it in the order (or just deduct, as per simplistic model)
                // The requirements say "Locking balance or assets". 
                // In standard exchange, we deduct from 'available' and move to 'locked' or just deduct from main balance.
                // The order table has `locked_usd`.
                // Let's deduct from user balance and put into order->locked_usd ??
                // Actually the requirement says "users table has balance DECIMAL".
                // Let's assume we deduct from User->balance.
                
                $user->balance -= $totalCost;
                $user->save();
                
                $lockedUsd = $totalCost;
            } else {
                // Sell side - check Asset
                $asset = $user->assets()->where('symbol', $symbol)->lockForUpdate()->first();
                if (! $asset || $asset->amount < $amount) {
                    throw new Exception('Insufficient asset balance');
                }

                $asset->amount -= $amount;
                $asset->locked_amount += $amount; // Move to locked?
                // The requirements say "assets table with amount and locked_amount".
                // So for SELL, we move amount -> locked_amount.
                $asset->save();

                $lockedUsd = null;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'symbol' => $symbol,
                'side' => $side,
                'price' => $price,
                'amount' => $amount,
                'locked_usd' => $lockedUsd,
                'status' => OrderStatus::OPEN,
            ]);

            // Dispatch matching job
            dispatch(new MatchOrderJob($order->id))->onQueue('matching');

            return $order;
        });
    }

    /**
     * Cancel an order and refund balance/assets.
     */
    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->refresh(); // Reload to get fresh status
            
            // Lock order
            $order = Order::where('id', $order->id)->lockForUpdate()->first();

            if ($order->status !== OrderStatus::OPEN) {
                throw new Exception('Order cannot be cancelled');
            }

            // Refund
            if ($order->side === OrderSide::BUY) {
                $user = User::where('id', $order->user_id)->lockForUpdate()->first();
                // Refund remaining locked_usd
                // Partial fills might complicate this, but let's assume simple cases or
                // calculate remaining based on (amount - filled) * price?
                // Requirement 9: "No partial matches required (only full match)".
                // So if it's OPEN, it's fully open.
                $user->balance += $order->locked_usd;
                $user->save();
            } else {
                $asset = $order->user->assets()->where('symbol', $order->symbol)->lockForUpdate()->first();
                $asset->locked_amount -= $order->amount;
                $asset->amount += $order->amount;
                $asset->save();
            }

            $order->status = OrderStatus::CANCELLED;
            $order->save();
        });
    }
}
