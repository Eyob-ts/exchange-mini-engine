# API Controllers Documentation

## Overview

API controllers handle HTTP requests and responses for the Exchange Mini Engine. They follow RESTful principles and Laravel best practices.

---

## Controller Structure

All API controllers are located in `app/Http/Controllers/Api/` and extend the base `Controller` class.

---

## AuthController

**Location:** `app/Http/Controllers/Api/AuthController.php`

### Purpose

Handles user authentication and registration.

### Methods

#### `register(Request $request)`

**Route:** `POST /api/auth/register`

Creates a new user account and returns an authentication token.

**Request Validation:**
- `name`: required, string, max 255
- `email`: required, string, email, max 255, unique
- `password`: required, string, min 8 characters

**Response:**
```json
{
  "token": "1|...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "balance": "0.00"
  }
}
```

**Process:**
1. Validates input
2. Hashes password
3. Creates user with initial balance of 0
4. Generates Sanctum token
5. Returns token and user data

---

#### `login(Request $request)`

**Route:** `POST /api/auth/login`

Authenticates user and returns token.

**Request Validation:**
- `email`: required, email
- `password`: required

**Response:**
```json
{
  "token": "1|...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

**Errors:**
- `422`: Invalid credentials

**Process:**
1. Validates input
2. Finds user by email
3. Verifies password
4. Generates Sanctum token
5. Returns token and user data

---

#### `logout(Request $request)`

**Route:** `POST /api/auth/logout`

**Middleware:** `auth:sanctum`

Revokes the current access token.

**Response:** `204 No Content`

---

#### `me(Request $request)`

**Route:** `GET /api/profile`

**Middleware:** `auth:sanctum`

Returns authenticated user's profile with relationships.

**Response:**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "balance": "10000.00",
  "assets": [...],
  "orders": [...]
}
```

---

## OrderController

**Location:** `app/Http/Controllers/Api/OrderController.php`

### Purpose

Handles order book and order management operations.

### Dependencies

Uses `OrderService` for business logic (dependency injection).

---

#### `orderBook(Request $request)`

**Route:** `GET /api/orderbook`

Returns the current order book for a trading symbol.

**Request Validation:**
- `symbol`: required, string

**Query Parameters:**
- `symbol`: Trading pair (e.g., "BTCUSD")

**Response:**
```json
{
  "bids": [
    {
      "id": 1,
      "price": "45000.00",
      "amount": "0.5",
      "side": "buy",
      "status": "open"
    }
  ],
  "asks": [
    {
      "id": 2,
      "price": "45100.00",
      "amount": "0.3",
      "side": "sell",
      "status": "open"
    }
  ]
}
```

**Process:**
1. Validates symbol parameter
2. Fetches buy orders (bids) - sorted by price DESC
3. Fetches sell orders (asks) - sorted by price ASC
4. Returns formatted order book

---

#### `index(Request $request)`

**Route:** `GET /api/orders`

**Middleware:** `auth:sanctum`

Returns paginated list of authenticated user's orders.

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "symbol": "BTCUSD",
      "side": "buy",
      "price": "45000.00",
      "amount": "0.5",
      "status": "open"
    }
  ],
  "current_page": 1,
  "per_page": 20
}
```

---

#### `store(OrderRequest $request)`

**Route:** `POST /api/orders`

**Middleware:** `auth:sanctum`

Creates a new order.

**Request:**
Uses `OrderRequest` form request for validation (see `app/Http/Requests/OrderRequest.php`)

**Validation Rules:**
- `symbol`: required, string, max 10
- `side`: required, enum (buy/sell)
- `price`: required, numeric, greater than 0
- `amount`: required, numeric, greater than 0

**Response:**
```json
{
  "id": 1,
  "symbol": "BTCUSD",
  "side": "buy",
  "price": "45000.00",
  "amount": "0.5",
  "status": "open"
}
```

**Process:**
1. Validates request via `OrderRequest`
2. Converts side string to `OrderSide` enum
3. Calls `OrderService::createOrder()`
4. Returns order resource

**Business Logic:**
- Order creation and balance reservation handled by `OrderService`
- Order matching triggered asynchronously via queue job

---

#### `cancel(Request $request, int $id)`

**Route:** `POST /api/orders/{id}/cancel`

**Middleware:** `auth:sanctum`

Cancels an order.

**Path Parameters:**
- `id`: Order ID

**Response:**
```json
{
  "message": "Order cancelled"
}
```

**Errors:**
- `404`: Order not found or doesn't belong to user

**Process:**
1. Finds order belonging to authenticated user
2. Calls `OrderService::cancelOrder()`
3. Returns success message

---

## WalletController

**Location:** `app/Http/Controllers/Api/WalletController.php`

### Purpose

Handles wallet operations (deposits, withdrawals, balances).

---

#### `deposit(Request $request)`

**Route:** `POST /api/wallet/deposit`

**Middleware:** `auth:sanctum`

Adds funds to user's wallet.

**Implementation:** Placeholder for future implementation.

---

## Design Patterns

### 1. Form Requests

Validation logic is separated into Form Request classes:

- `OrderRequest` - Validates order creation

Benefits:
- Reusable validation rules
- Cleaner controller methods
- Centralized validation logic

### 2. Service Layer

Business logic is delegated to service classes:

- `OrderService` - Order management logic
- `MatchingEngineService` - Order matching logic

Benefits:
- Controllers stay thin
- Business logic is testable
- Reusable across different contexts

### 3. Resource Classes

API responses use Resource classes for formatting:

- `OrderResource` - Formats order data

Benefits:
- Consistent API responses
- Easy to modify output format
- Handles relationships elegantly

---

## Response Formatting

### Success Responses

Use Laravel Resources for consistent formatting:

```php
return new OrderResource($order);
return OrderResource::collection($orders);
```

### Error Responses

Laravel automatically formats validation errors:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["Error message"]
  }
}
```

---

## Authentication

All protected routes use `auth:sanctum` middleware:

```php
Route::middleware('auth:sanctum')->group(function () {
    // Protected routes
});
```

Tokens are sent in the Authorization header:
```
Authorization: Bearer {token}
```

---

## Error Handling

Controllers rely on Laravel's exception handling:

- **ValidationException** - For validation errors (422)
- **ModelNotFoundException** - For missing models (404)
- **AuthenticationException** - For auth failures (401)

Custom error handling can be added in `app/Exceptions/Handler.php`.

