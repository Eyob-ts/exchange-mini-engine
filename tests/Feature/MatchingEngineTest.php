<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Asset;
use App\Enums\OrderSide;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Laravel\Sanctum\Sanctum;

class MatchingEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_buy_and_sell_order_matching()
    {
        // 1. Create Buyer with USD
        $buyer = User::factory()->create(['balance' => 100000]);
        Sanctum::actingAs($buyer);

        // 2. Create Seller with BTC
        $seller = User::factory()->create(['balance' => 0]);
        Asset::create(['user_id' => $seller->id, 'symbol' => 'BTC', 'amount' => 10, 'locked_amount' => 0]);
        
        // 3. Place Sell Order (Maker) - Seller
        Sanctum::actingAs($seller);
        $response = $this->postJson('/api/orders', [
            'symbol' => 'BTC',
            'side' => 'sell',
            'price' => 50000,
            'amount' => 1,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', ['side' => 'sell', 'status' => OrderStatus::OPEN->value]);

        // 4. Place Buy Order (Taker) - Buyer
        Sanctum::actingAs($buyer);
        $response = $this->postJson('/api/orders', [
            'symbol' => 'BTC',
            'side' => 'buy',
            'price' => 50000,
            'amount' => 1,
        ]);

        $response->assertStatus(201);

        // 5. Run Matching Job (Simulate Queue)
        // In testing, we can force sync queue or run the job manually if not using 'queue' driver in test.
        // But better, let's configure phpunit.xml to use sync queue? 
        // Or if we used dispatch(...)->onQueue('matching'), we need to be careful.
        // Let's rely on standard Laravel test behavior which defaults to sync unless configured otherwise.
        
        // Assert Trade Created
        $this->assertDatabaseHas('trades', [
            'symbol' => 'BTC',
            'price' => 50000,
            'amount' => 1,
        ]);

        // Assert Balances Updated
        $buyer->refresh();
        $seller->refresh();

        // Buyer: Paid 50,000 + Fee. Fee = 50000 * 0.015 = 750.
        // Balance = 100,000 - 50,000 - 750 = 49,250
        $this->assertEquals(49250, $buyer->balance);

        // Seller: Received 50,000.
        // Balance = 50,000.
        $this->assertEquals(50000, $seller->balance);

        // Assert Orders Filled
        $this->assertDatabaseHas('orders', ['side' => 'sell', 'status' => OrderStatus::FILLED->value]);
        $this->assertDatabaseHas('orders', ['side' => 'buy', 'status' => OrderStatus::FILLED->value]);
    }
}
