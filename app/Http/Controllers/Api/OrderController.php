<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use App\Models\Order;
use App\Enums\OrderStatus;
use App\Enums\OrderSide;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    /**
     * Get Order Book.
     */
    public function index(Request $request)
    {
        $request->validate([
            'symbol' => 'required|string',
        ]);

        $symbol = $request->symbol;

        // Buy orders: Descending price (Highest first)
        $buyOrders = Order::where('symbol', $symbol)
            ->where('side', OrderSide::BUY)
            ->where('status', OrderStatus::OPEN)
            ->orderBy('price', 'desc')
            ->limit(50)
            ->get();

        // Sell orders: Ascending price (Lowest first)
        $sellOrders = Order::where('symbol', $symbol)
            ->where('side', OrderSide::SELL)
            ->where('status', OrderStatus::OPEN)
            ->orderBy('price', 'asc')
            ->limit(50)
            ->get();

        return response()->json([
            'bids' => OrderResource::collection($buyOrders),
            'asks' => OrderResource::collection($sellOrders),
        ]);
    }

    /**
     * Create Order.
     */
    public function store(OrderRequest $request)
    {
        // Enums are casted by Request automatically if typed?
        // Actually OrderRequest validates valid enum value, but returns string in validated().
        // We need to cast string to Enum manually or let PHP handle it if typed.
        
        $side = OrderSide::from($request->side);

        $order = $this->orderService->createOrder(
            $request->user(),
            $request->symbol,
            $side,
            $request->price,
            $request->amount
        );

        return new OrderResource($order);
    }

    /**
     * Cancel Order.
     */
    public function cancel(Request $request, int $id)
    {
        // Find order belonging to user
        $order = Order::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();

        $this->orderService->cancelOrder($order);

        return response()->json(['message' => 'Order cancelled']);
    }
}
