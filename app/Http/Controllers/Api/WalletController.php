<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * Deposit funds (USD) or Assets.
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'symbol' => 'required|string|max:10', // 'USD' or Asset Symbol
            'amount' => 'required|numeric|gt:0',
        ]);

        $user = $request->user();
        $symbol = strtoupper($request->symbol);
        $amount = $request->amount;

        DB::transaction(function () use ($user, $symbol, $amount) {
            $user->lockForUpdate();

            if ($symbol === 'USD') {
                $user->balance += $amount;
                $user->save();
            } else {
                $asset = Asset::firstOrCreate(
                    ['user_id' => $user->id, 'symbol' => $symbol],
                    ['amount' => 0, 'locked_amount' => 0]
                );
                // Lock asset row if it existed, though firstOrCreate saves it. 
                // To be strictly atomic if high concurrency, we might want to re-fetch with lock, 
                // but for a simple deposit endpoint for testing, this is fine.
                
                $asset->increment('amount', $amount);
            }
        });

        return response()->json([
            'message' => 'Deposit successful',
            'balance' => $user->fresh()->balance,
            'assets' => $user->assets,
        ]);
    }
}
