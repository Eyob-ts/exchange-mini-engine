<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // 1. Buyer User (with USD)
        $buyer = User::firstOrCreate([
            'email' => 'buyer@test.com',
        ], [
            'name' => 'Buyer User',
            'password' => Hash::make('password'),
            'balance' => 100000, // $100k
        ]);

        // 2. Seller User (with BTC Asset)
        $seller = User::firstOrCreate([
            'email' => 'seller@test.com',
        ], [
            'name' => 'Seller User',
            'password' => Hash::make('password'),
            'balance' => 1000,
        ]);

        if (! $seller->assets()->where('symbol', 'BTC')->exists()) {
            $seller->assets()->create([
                'symbol' => 'BTC',
                'amount' => 10.0000,
                'locked_amount' => 0,
            ]);
        }
    }
}
