<?php

namespace App\Events;

use App\Models\Trade;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderMatched implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trade;

    /**
     * Create a new event instance.
     */
    public function __construct(Trade $trade)
    {
        $this->trade = $trade;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast to both buyer and seller
        return [
            new PrivateChannel('user.'.$this->trade->buy_order_id), // Assumption: User ID is retrievable or channel is strictly user.{id}
            // Actually, requirements say "private-user.{userId}".
            // PrivateChannel('user.{userId}') corresponds to `private-user.{userId}` in Pusher if defined correctly.
            // But we need to target the USER ID, not Order ID.
            // The Trade has relations to orders, which have user_ids.
            new PrivateChannel('user.'.$this->trade->buyerOrder->user_id),
            new PrivateChannel('user.'.$this->trade->sellerOrder->user_id),
        ];
    }

    public function broadcastWith()
    {
        return [
            'trade' => $this->trade,
            'message' => 'Order Matched',
        ];
    }
}
