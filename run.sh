#!/bin/bash

set -e

echo "🚀 Starting Exchange Mini Engine Setup..."

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

echo -e "${GREEN}✓${NC} Docker found"

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo -e "${YELLOW}Creating .env file...${NC}"
    cat > .env << 'EOF'
APP_NAME=Exchange
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=exchange_db
DB_USERNAME=exchange_user
DB_PASSWORD=exchange_pass

SESSION_DRIVER=database
SESSION_LIFETIME=120

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
EOF
    echo -e "${GREEN}✓${NC} .env file created"
fi

# Create frontend .env if it doesn't exist
if [ ! -f frontend/.env ]; then
    echo -e "${YELLOW}Creating frontend .env file...${NC}"
    echo "VITE_API_URL=http://localhost:8000/api" > frontend/.env
    echo -e "${GREEN}✓${NC} Frontend .env file created"
fi

# Stop any existing containers
echo -e "${YELLOW}Stopping existing containers...${NC}"
docker-compose down 2>/dev/null || docker compose down 2>/dev/null || true

# Build and start containers
echo -e "${YELLOW}Building and starting Docker containers...${NC}"
docker-compose up -d --build 2>/dev/null || docker compose up -d --build

# Wait for MySQL to be ready
echo -e "${YELLOW}Waiting for MySQL to be ready...${NC}"
echo "This may take 30-60 seconds on first run..."
timeout=60
counter=0
until docker-compose exec -T mysql mysqladmin ping -h localhost --silent 2>/dev/null || docker compose exec -T mysql mysqladmin ping -h localhost --silent 2>/dev/null; do
    sleep 2
    counter=$((counter+2))
    if [ $counter -ge $timeout ]; then
        echo "❌ MySQL did not become ready in time"
        exit 1
    fi
done
echo -e "${GREEN}✓${NC} MySQL is ready"

# Generate app key
echo -e "${YELLOW}Generating application key...${NC}"
docker-compose exec -T app php artisan key:generate 2>/dev/null || docker compose exec -T app php artisan key:generate

# Run migrations
echo -e "${YELLOW}Running database migrations...${NC}"
docker-compose exec -T app php artisan migrate --force 2>/dev/null || docker compose exec -T app php artisan migrate --force

# Clear and cache config
echo -e "${YELLOW}Optimizing application...${NC}"
docker-compose exec -T app php artisan config:clear 2>/dev/null || docker compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan route:clear 2>/dev/null || docker compose exec -T app php artisan route:clear

echo ""
echo -e "${GREEN}═══════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ Setup Complete!${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════${NC}"
echo ""
echo "📱 Frontend: http://localhost:5173"
echo "🔌 Backend API: http://localhost:8000/api"
echo ""
echo "To view logs: docker-compose logs -f"
echo "To stop: docker-compose down"
echo ""
echo -e "${GREEN}Happy trading! 🚀${NC}"

