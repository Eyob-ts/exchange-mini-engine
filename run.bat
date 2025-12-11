@echo off
setlocal enabledelayedexpansion

echo 🚀 Starting Exchange Mini Engine Setup...

REM Check if Docker is installed
where docker >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker is not installed. Please install Docker Desktop first.
    exit /b 1
)

echo ✓ Docker found

REM Create .env file if it doesn't exist
if not exist .env (
    echo Creating .env file...
    (
        echo APP_NAME=Exchange
        echo APP_ENV=local
        echo APP_KEY=
        echo APP_DEBUG=true
        echo APP_TIMEZONE=UTC
        echo APP_URL=http://localhost:8000
        echo.
        echo DB_CONNECTION=mysql
        echo DB_HOST=mysql
        echo DB_PORT=3306
        echo DB_DATABASE=exchange_db
        echo DB_USERNAME=exchange_user
        echo DB_PASSWORD=exchange_pass
        echo.
        echo SESSION_DRIVER=database
        echo SESSION_LIFETIME=120
        echo.
        echo BROADCAST_CONNECTION=log
        echo FILESYSTEM_DISK=local
        echo QUEUE_CONNECTION=database
        echo.
        echo CACHE_STORE=database
    ) > .env
    echo ✓ .env file created
)

REM Create frontend .env if it doesn't exist
if not exist frontend\.env (
    echo Creating frontend .env file...
    echo VITE_API_URL=http://localhost:8000/api > frontend\.env
    echo ✓ Frontend .env file created
)

REM Stop any existing containers
echo Stopping existing containers...
docker-compose down 2>nul

REM Build and start containers
echo Building and starting Docker containers...
docker-compose up -d --build

REM Wait for MySQL to be ready
echo Waiting for MySQL to be ready...
echo This may take 30-60 seconds on first run...
timeout /t 20 /nobreak >nul

REM Generate app key
echo Generating application key...
docker-compose exec -T app php artisan key:generate

REM Run migrations
echo Running database migrations...
docker-compose exec -T app php artisan migrate --force

REM Clear and cache config
echo Optimizing application...
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan route:clear

echo.
echo ═══════════════════════════════════════════════════
echo ✅ Setup Complete!
echo ═══════════════════════════════════════════════════
echo.
echo 📱 Frontend: http://localhost:5173
echo 🔌 Backend API: http://localhost:8000/api
echo.
echo To view logs: docker-compose logs -f
echo To stop: docker-compose down
echo.
echo Happy trading! 🚀
pause

