<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\MatchingEngineService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MatchOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     */
    public function handle(MatchingEngineService $matcher): void
    {
        $order = Order::find($this->orderId);

        if (! $order) {
            return;
        }

        $matcher->match($order);
    }
}
