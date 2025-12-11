# Services Documentation

## Overview

Services contain the core business logic of the application. They are responsible for processing orders, matching trades, and managing the exchange engine.

---

## MatchingEngineService

**Location:** `app/Services/MatchingEngineService.php`

### Purpose

Handles the core order matching logic - matching buy orders with sell orders based on price compatibility.

### Key Methods

#### `matchOrders(Order $order): array`

Matches a new order against existing orders in the book.

**Process:**
1. Finds compatible orders (opposite side, matching or better price)
2. Sorts compatible orders by price-time priority
3. Matches orders in sequence until the new order is filled or no more matches available
4. Creates trade records for each match
5. Updates order statuses (filled/partial)
6. Updates user balances

**Returns:** Array of created Trade models

**Example:**
```php
$matchingEngine = app(MatchingEngineService::class);
$trades = $matchingEngine->matchOrders($newOrder);
```

---

## OrderService

**Location:** `app/Services/OrderService.php`

### Purpose

Manages order creation, cancellation, and lifecycle.

### Key Methods

#### `createOrder(User $user, string $symbol, OrderSide $side, float $price, float $amount): Order`

Creates a new order and triggers matching.

**Process:**
1. Validates user has sufficient balance (for buy orders)
2. Validates user has sufficient assets (for sell orders)
3. Creates the order record
4. Reserves balance/assets
5. Dispatches matching job to queue
6. Returns the created order

**Balance Logic:**
- **Buy Orders**: Reserves `price * amount` in USD balance
- **Sell Orders**: Reserves `amount` in BTC (or asset being sold)

#### `cancelOrder(Order $order): void`

Cancels an open order and releases reserved funds.

**Process:**
1. Validates order is cancellable (status = open)
2. Releases reserved balance/assets back to user
3. Updates order status to cancelled

---

## Architecture Pattern

### Service Layer Pattern

Services follow the **Service Layer Pattern** to separate business logic from controllers:

- **Controllers** (`app/Http/Controllers/Api/`) handle HTTP requests/responses
- **Services** (`app/Services/`) contain business logic
- **Models** (`app/Models/`) represent data entities
- **Jobs** (`app/Jobs/`) handle asynchronous processing

### Benefits

1. **Separation of Concerns**: Business logic is isolated from HTTP layer
2. **Reusability**: Services can be used from controllers, jobs, commands, etc.
3. **Testability**: Services can be unit tested independently
4. **Maintainability**: Logic changes don't require controller changes

---

## Queue Processing

### MatchOrderJob

**Location:** `app/Jobs/MatchOrderJob.php`

Processes order matching asynchronously.

**Why Queue?**
- Prevents long-running HTTP requests
- Allows parallel processing of multiple orders
- Better error handling and retry logic
- Scalable architecture

**Configuration:**
- Queue connection: `database` (configurable in `.env`)
- Tries: 3 attempts
- Timeout: 90 seconds

**Usage:**
```php
MatchOrderJob::dispatch($order);
```

---

## Order Matching Algorithm

### Price-Time Priority

1. **Price Priority**: Better prices match first
   - Buy orders: Higher prices have priority
   - Sell orders: Lower prices have priority

2. **Time Priority**: Older orders at same price match first

### Matching Rules

1. **Price Compatibility**:
   - Buy order matches if `buy_price >= sell_price`
   - Sell order matches if `sell_price <= buy_price`

2. **Fill Logic**:
   - Orders are filled partially or completely
   - Remaining amount stays in order book
   - Fully filled orders are marked as `filled`

3. **Trade Execution**:
   - Trade price = matched order's price (price-taking)
   - Trade amount = minimum of both order amounts

---

## Database Transactions

All critical operations use database transactions to ensure data consistency:

```php
DB::transaction(function () {
    // Order creation, matching, balance updates
});
```

This ensures:
- Atomicity: All changes succeed or all fail
- Consistency: No partial updates
- Integrity: Data remains valid

---

## Future Enhancements

Potential improvements:

1. **Limit Orders**: Currently all orders are limit orders
2. **Market Orders**: Immediate execution at best available price
3. **Stop Orders**: Trigger orders at specific price
4. **Order Book Depth**: Optimize for large order books
5. **Multiple Trading Pairs**: Extend beyond BTCUSD
6. **Fee Structure**: Add trading fees
7. **Order History**: Enhanced order tracking

