# API Routes Documentation

## Overview

This document describes all API endpoints available in the Exchange Mini Engine.

## Base URL

- **Local Development**: `http://localhost:8000/api`
- **Docker**: `http://localhost:8000/api`

## Authentication

Most endpoints require authentication via Laravel Sanctum. Include the token in the Authorization header:

```
Authorization: Bearer {token}
```

---

## Authentication Endpoints

### Register User
**POST** `/api/auth/register`

Register a new user account.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}
```

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

---

### Login
**POST** `/api/auth/login`

Authenticate and receive an access token.

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

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

---

### Logout
**POST** `/api/auth/logout`

Logout the authenticated user (revokes current token).

**Headers:** `Authorization: Bearer {token}`

**Response:** `204 No Content`

---

## Order Endpoints

### Get Order Book
**GET** `/api/orderbook?symbol=BTCUSD`

Get the current order book for a trading pair.

**Query Parameters:**
- `symbol` (required): Trading pair symbol (e.g., "BTCUSD")

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

---

### Get User Orders
**GET** `/api/orders`

Get all orders for the authenticated user.

**Headers:** `Authorization: Bearer {token}`

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
      "status": "open",
      "filled_amount": "0.0",
      "created_at": "2024-01-01T00:00:00.000000Z"
    }
  ]
}
```

---

### Create Order
**POST** `/api/orders`

Create a new buy or sell order.

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "symbol": "BTCUSD",
  "side": "buy",
  "price": 45000.00,
  "amount": 0.5
}
```

**Validation Rules:**
- `symbol`: required, string, max 10 characters
- `side`: required, enum: "buy" or "sell"
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
  "status": "open",
  "filled_amount": "0.0",
  "created_at": "2024-01-01T00:00:00.000000Z"
}
```

**Notes:**
- Orders are automatically matched by the matching engine via queue jobs
- Matching happens asynchronously in the background
- Orders with matching prices are matched immediately (buy orders match with sell orders)

---

### Cancel Order
**POST** `/api/orders/{id}/cancel`

Cancel an open order.

**Headers:** `Authorization: Bearer {token}`

**Path Parameters:**
- `id`: Order ID to cancel

**Response:**
```json
{
  "message": "Order cancelled"
}
```

**Notes:**
- Only the order owner can cancel their orders
- Only open orders can be cancelled
- Cancelled orders cannot be reopened

---

## Profile Endpoints

### Get User Profile
**GET** `/api/profile`

Get the authenticated user's profile with assets and orders.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "balance": "10000.00",
  "assets": [],
  "orders": []
}
```

---

## Error Responses

All endpoints may return standard error responses:

**422 Unprocessable Entity** (Validation Error):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "price": ["The price must be greater than 0."]
  }
}
```

**401 Unauthorized**:
```json
{
  "message": "Unauthenticated."
}
```

**404 Not Found**:
```json
{
  "message": "No query results for model..."
}
```

---

## CORS Configuration

The API is configured to accept requests from:
- `http://localhost:5173` (Frontend dev server)
- `http://127.0.0.1:5173`

Credentials are enabled for authentication cookies/tokens.

---

## Rate Limiting

Currently no rate limiting is implemented. In production, consider adding rate limiting to prevent abuse.

---

## WebSocket Events

Real-time updates are available via Laravel Echo (WebSocket):

**Channels:**
- `market` - Public market updates

**Events:**
- `OrderMatched` - Fired when an order is matched
- `OrderPlaced` - Fired when a new order is placed

**Note:** WebSocket setup requires Laravel Reverb configuration.

