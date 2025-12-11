# Frontend Architecture Documentation

## Overview

The frontend is built with Vue.js 3, TypeScript, and modern tooling for a scalable, maintainable SPA.

---

## Tech Stack

- **Vue.js 3** - Progressive JavaScript framework
- **TypeScript** - Type-safe JavaScript
- **Pinia** - State management
- **Vue Router** - Client-side routing
- **Axios** - HTTP client
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Fast build tool and dev server
- **Laravel Echo** - WebSocket client (for real-time updates)

---

## Project Structure

```
frontend/src/
├── assets/          # Static assets (images, icons)
├── components/      # Reusable Vue components
│   ├── orders/     # Order-related components
│   ├── portfolio/  # Portfolio/wallet components
│   ├── trading/    # Trading interface components
│   └── ui/         # Generic UI components
├── router/          # Vue Router configuration
├── stores/          # Pinia stores (state management)
├── views/           # Page/route components
├── axios.ts         # Axios HTTP client configuration
├── echo.ts          # Laravel Echo WebSocket configuration
├── main.ts          # Application entry point
└── style.css        # Global styles
```

---

## State Management (Pinia)

### Auth Store

**Location:** `stores/auth.ts`

Manages authentication state and user session.

**State:**
- `user` - Current authenticated user
- `token` - Authentication token
- `loading` - Loading state
- `error` - Error messages

**Actions:**
- `login(credentials)` - Authenticate user
- `register(credentials)` - Register new user
- `logout()` - Logout current user
- `fetchUser()` - Fetch current user profile

**Usage:**
```typescript
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
await auth.login({ email, password })
```

---

### Market Store

**Location:** `stores/market.ts`

Manages order book and market data.

**State:**
- `bids` - Buy orders (bids)
- `asks` - Sell orders (asks)
- `trades` - Recent trades
- `loading` - Loading state

**Actions:**
- `fetchOrderBook(symbol)` - Fetch order book data
- `connectWebSocket()` - Connect to real-time updates

**Usage:**
```typescript
import { useMarketStore } from '@/stores/market'

const market = useMarketStore()
await market.fetchOrderBook('BTCUSD')
```

---

## Routing

**Location:** `router/index.ts`

### Routes

- `/` - Dashboard (trading interface)
- `/login` - Login page
- `/register` - Registration page

### Route Guards

Routes can be protected with authentication guards:

```typescript
{
  path: '/',
  component: Dashboard,
  meta: { requiresAuth: true }
}
```

---

## HTTP Client (Axios)

**Location:** `axios.ts`

Configured Axios instance with:
- Base URL from environment variables
- Automatic token injection from localStorage
- Credentials support (cookies)

**Environment Variables:**
- `VITE_API_URL` - Backend API URL (default: `http://localhost:8000/api`)

**Usage:**
```typescript
import api from '@/axios'

// GET request
const response = await api.get('/orders')

// POST request
const response = await api.post('/orders', { symbol, side, price, amount })
```

---

## WebSocket (Laravel Echo)

**Location:** `echo.ts`

Configured Laravel Echo for real-time updates.

**Features:**
- Graceful degradation if WebSocket not configured
- Safe dummy object if environment variables missing
- Prevents errors when WebSocket unavailable

**Environment Variables:**
- `VITE_REVERB_APP_KEY` - Reverb app key
- `VITE_REVERB_HOST` - Reverb host
- `VITE_REVERB_PORT` - Reverb port

**Usage:**
```typescript
import echo from '@/echo'

echo.channel('market')
  .listen('.OrderMatched', (event) => {
    console.log('Order matched:', event)
  })
```

---

## Components

### UI Components (`components/ui/`)

Reusable generic components:
- **Button** - Styled button component
- **Input** - Form input component
- **Card** - Container card component

### Trading Components (`components/trading/`)

Trading-specific components:
- **OrderBook** - Displays order book (bids/asks)
- **TradeForm** - Form to create buy/sell orders

### Portfolio Components (`components/portfolio/`)

- **Wallet** - Display user wallet/balance

### Order Components (`components/orders/`)

- **OrderHistory** - Display user's order history

---

## Views

### Dashboard (`views/Dashboard.vue`)

Main trading interface with:
- Order book
- Trading form
- Chart area (placeholder)
- Order history
- Wallet/portfolio

### Login (`views/Login.vue`)

User authentication form.

### Register (`views/Register.vue`)

User registration form.

---

## Styling

### Tailwind CSS

Utility-first CSS framework for rapid UI development.

**Configuration:**
- Custom color scheme (slate, emerald, red)
- Dark mode by default
- Responsive design utilities

### Custom Styles

**Location:** `style.css`

Global styles and custom CSS variables.

---

## Build & Development

### Development Server

```bash
npm run dev
```

Starts Vite dev server with hot module replacement (HMR).

### Build for Production

```bash
npm run build
```

Builds optimized production bundle to `dist/` directory.

### Preview Production Build

```bash
npm run preview
```

Preview the production build locally.

---

## Environment Variables

Create `frontend/.env`:

```env
VITE_API_URL=http://localhost:8000/api
VITE_REVERB_APP_KEY=your_key
VITE_REVERB_HOST=localhost
VITE_REVERB_PORT=8080
```

**Note:** All Vite environment variables must be prefixed with `VITE_`

---

## TypeScript Configuration

**Location:** `tsconfig.json`

Configured for:
- Vue 3 SFC support
- Modern ES features
- Strict type checking
- Path aliases (`@/` → `src/`)

---

## Best Practices

### 1. Component Composition

Break down complex components into smaller, reusable pieces.

### 2. Type Safety

Use TypeScript interfaces for props and data:

```typescript
interface User {
  id: number
  name: string
  email: string
}
```

### 3. State Management

Use Pinia stores for shared state, local state for component-specific data.

### 4. Error Handling

Handle errors gracefully with try-catch and user-friendly messages.

### 5. Loading States

Always show loading indicators for async operations.

---

## Future Enhancements

- [ ] Chart integration (TradingView, Chart.js)
- [ ] Real-time price updates
- [ ] Order confirmation modals
- [ ] Toast notifications
- [ ] Dark/light theme toggle
- [ ] Responsive mobile design
- [ ] Unit tests (Vitest)
- [ ] E2E tests (Playwright/Cypress)

