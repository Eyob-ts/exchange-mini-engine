# Models Documentation

## Overview

This document describes the Eloquent models and their relationships in the Exchange Mini Engine.

---

## User Model

**Location:** `app/Models/User.php`

### Purpose

Represents application users/traders.

### Relationships

- `hasMany(Order::class)` - User's orders
- `hasMany(Asset::class)` - User's asset holdings
- `hasMany(Trade::class, 'buyer_id')` - Trades where user was buyer
- `hasMany(Trade::class, 'seller_id')` - Trades where user was seller

### Key Attributes

- `id` - User ID
- `name` - User's full name
- `email` - Unique email address
- `password` - Hashed password
- `balance` - USD balance (decimal)

### Usage

```php
$user = User::find(1);
$orders = $user->orders;
$balance = $user->balance;
```

---

## Order Model

**Location:** `app/Models/Order.php`

### Purpose

Represents buy/sell orders in the exchange.

### Relationships

- `belongsTo(User::class)` - Order owner
- `hasMany(Trade::class)` - Trades resulting from this order

### Key Attributes

- `id` - Order ID
- `user_id` - Owner user ID
- `symbol` - Trading pair (e.g., "BTCUSD")
- `side` - Order side (buy/sell) - `OrderSide` enum
- `price` - Order price (decimal)
- `amount` - Order amount (decimal)
- `filled_amount` - Amount already filled (decimal)
- `status` - Order status - `OrderStatus` enum
- `created_at` - Order creation timestamp

### Enums

#### OrderSide
```php
enum OrderSide: string {
    case BUY = 'buy';
    case SELL = 'sell';
}
```

#### OrderStatus
```php
enum OrderStatus: string {
    case OPEN = 'open';
    case FILLED = 'filled';
    case CANCELLED = 'cancelled';
    case PARTIAL = 'partial';
}
```

### Scopes

Common scopes for querying orders:

```php
Order::open()->get(); // Get all open orders
Order::buy()->get(); // Get all buy orders
Order::sell()->get(); // Get all sell orders
Order::forSymbol('BTCUSD')->get(); // Get orders for specific symbol
```

### Usage

```php
$order = Order::create([
    'user_id' => 1,
    'symbol' => 'BTCUSD',
    'side' => OrderSide::BUY,
    'price' => 45000,
    'amount' => 0.5,
]);

// Check if order is fully filled
if ($order->filled_amount >= $order->amount) {
    $order->status = OrderStatus::FILLED;
}
```

---

## Trade Model

**Location:** `app/Models/Trade.php`

### Purpose

Represents executed trades between two orders.

### Relationships

- `belongsTo(Order::class, 'buy_order_id')` - Buy order
- `belongsTo(Order::class, 'sell_order_id')` - Sell order
- `belongsTo(User::class, 'buyer_id')` - Buyer user
- `belongsTo(User::class, 'seller_id')` - Seller user

### Key Attributes

- `id` - Trade ID
- `buy_order_id` - Buy order ID
- `sell_order_id` - Sell order ID
- `buyer_id` - Buyer user ID
- `seller_id` - Seller user ID
- `symbol` - Trading pair
- `price` - Execution price (decimal)
- `amount` - Trade amount (decimal)
- `created_at` - Trade execution timestamp

### Usage

```php
$trade = Trade::create([
    'buy_order_id' => 1,
    'sell_order_id' => 2,
    'buyer_id' => 3,
    'seller_id' => 4,
    'symbol' => 'BTCUSD',
    'price' => 45000,
    'amount' => 0.5,
]);
```

---

## Asset Model

**Location:** `app/Models/Asset.php`

### Purpose

Represents user's holdings of various assets (BTC, ETH, etc.).

### Relationships

- `belongsTo(User::class)` - Asset owner

### Key Attributes

- `id` - Asset ID
- `user_id` - Owner user ID
- `symbol` - Asset symbol (e.g., "BTC")
- `balance` - Asset balance (decimal)

### Usage

```php
// Get user's BTC balance
$btcAsset = Asset::where('user_id', $user->id)
    ->where('symbol', 'BTC')
    ->first();

$btcBalance = $btcAsset ? $btcAsset->balance : 0;
```

---

## Database Schema

### Users Table
- Primary key: `id`
- Unique: `email`
- Indexes: `email`

### Orders Table
- Primary key: `id`
- Foreign keys: `user_id` → `users.id`
- Indexes: `user_id`, `symbol`, `side`, `status`, `price`
- Composite index: `(symbol, side, status, price)` for order book queries

### Trades Table
- Primary key: `id`
- Foreign keys: `buy_order_id`, `sell_order_id`, `buyer_id`, `seller_id`
- Indexes: All foreign keys, `symbol`, `created_at`

### Assets Table
- Primary key: `id`
- Foreign keys: `user_id` → `users.id`
- Unique: `(user_id, symbol)` - One asset per user per symbol

---

## Eloquent Best Practices

### 1. Use Type Casting

Models use type casting for enums and decimals:

```php
protected $casts = [
    'side' => OrderSide::class,
    'status' => OrderStatus::class,
    'price' => 'decimal:2',
    'amount' => 'decimal:8',
];
```

### 2. Use Scopes for Common Queries

```php
// Instead of repeating query logic
Order::where('status', OrderStatus::OPEN)->where('side', OrderSide::BUY)->get();

// Use scopes
Order::open()->buy()->get();
```

### 3. Use Relationships

```php
// Instead of manual joins
$orders = Order::where('user_id', $user->id)->get();

// Use relationships
$orders = $user->orders;
```

### 4. Mass Assignment Protection

Models use `$fillable` to specify which attributes can be mass-assigned:

```php
protected $fillable = [
    'symbol',
    'side',
    'price',
    'amount',
];
```

---

## Migration Files

Database structure is defined in migrations:

- `database/migrations/*_create_users_table.php`
- `database/migrations/*_create_orders_table.php`
- `database/migrations/*_create_trades_table.php`
- `database/migrations/*_create_assets_table.php`

To view current schema:
```bash
php artisan migrate:status
```

