# Exchange Mini Engine 🚀

A modern cryptocurrency exchange mini-engine built with Laravel 12 (Backend) and Vue.js 3 (Frontend).

## Features

- 🔐 User Authentication (Laravel Sanctum)
- 📊 Real-time Order Book
- 💰 Order Matching Engine
- 📈 Trading Interface
- 💼 Portfolio Management
- 🔄 Queue-based Order Processing

## Quick Start (Docker) 🐳

The easiest way to get started is using Docker. Just run one command:

### For Linux/Mac:
```bash
chmod +x run.sh
./run.sh
```

### For Windows:
```bash
run.bat
```

That's it! The script will:
- ✅ Install all dependencies
- ✅ Set up the database
- ✅ Run migrations
- ✅ Start all services

### Access Points:
- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000/api
- **API Documentation**: http://localhost:8000/docs (if Scramble is enabled)

## Manual Setup (Without Docker)

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 20+
- MySQL 8.0+ or SQLite

### Backend Setup

1. Install dependencies:
```bash
composer install
```

2. Create `.env` file:
```bash
cp .env.example .env
php artisan key:generate
```

3. Configure database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=exchange_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

4. Run migrations:
```bash
php artisan migrate
```

5. Start the server:
```bash
php artisan serve
```

6. Start queue worker (in another terminal):
```bash
php artisan queue:work
```

### Frontend Setup

1. Navigate to frontend directory:
```bash
cd frontend
```

2. Install dependencies:
```bash
npm install
```

3. Create `.env` file:
```bash
echo "VITE_API_URL=http://localhost:8000/api" > .env
```

4. Start development server:
```bash
npm run dev
```

## Docker Services

The Docker setup includes:

- **app**: Laravel PHP-FPM application
- **nginx**: Web server (port 8000)
- **mysql**: MySQL database (port 3306)
- **queue**: Laravel queue worker
- **frontend**: Vue.js development server (port 5173)

## Docker Commands

```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f

# Stop all services
docker-compose down

# Restart a specific service
docker-compose restart app

# Execute commands in containers
docker-compose exec app php artisan migrate
docker-compose exec frontend npm run build
```

## Project Structure

```
exchange-mini-engine/
├── app/
│   ├── Http/Controllers/Api/  # API Controllers
│   ├── Services/               # Business Logic
│   ├── Models/                 # Eloquent Models
│   └── Jobs/                   # Queue Jobs
├── frontend/
│   ├── src/
│   │   ├── components/         # Vue Components
│   │   ├── stores/             # Pinia Stores
│   │   └── views/              # Vue Views
│   └── vite.config.ts
├── database/
│   ├── migrations/             # Database Migrations
│   └── seeders/                # Database Seeders
├── routes/
│   └── api.php                 # API Routes
├── docker-compose.yml          # Docker Configuration
└── run.sh / run.bat           # Setup Scripts
```

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user
- `POST /api/auth/logout` - Logout user (requires auth)

### Orders
- `GET /api/orderbook?symbol=BTCUSD` - Get order book
- `GET /api/orders` - Get user orders (requires auth)
- `POST /api/orders` - Create new order (requires auth)
- `POST /api/orders/{id}/cancel` - Cancel order (requires auth)

### Profile
- `GET /api/profile` - Get user profile (requires auth)

## Technology Stack

**Backend:**
- Laravel 12
- Laravel Sanctum (Authentication)
- Laravel Queue (Background Jobs)
- Laravel Telescope (Debugging)

**Frontend:**
- Vue.js 3
- TypeScript
- Pinia (State Management)
- Axios (HTTP Client)
- Tailwind CSS
- Vite

**Infrastructure:**
- Docker & Docker Compose
- Nginx
- MySQL 8.0
- PHP 8.2

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

## License

MIT License
