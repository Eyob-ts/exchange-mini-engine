.PHONY: help setup up down logs restart clean

help: ## Show this help message
	@echo "Exchange Mini Engine - Docker Commands"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

setup: ## Initial setup - run this first
	@echo "🚀 Setting up Exchange Mini Engine..."
	@if [ ! -f .env ]; then \
		echo "Creating .env file..."; \
		cp .env.example .env 2>/dev/null || echo "APP_NAME=Exchange\nAPP_ENV=local\nAPP_KEY=\nAPP_DEBUG=true\nAPP_URL=http://localhost:8000\nDB_CONNECTION=mysql\nDB_HOST=mysql\nDB_PORT=3306\nDB_DATABASE=exchange_db\nDB_USERNAME=exchange_user\nDB_PASSWORD=exchange_pass\nSESSION_DRIVER=database\nQUEUE_CONNECTION=database" > .env; \
	fi
	@if [ ! -f frontend/.env ]; then \
		echo "VITE_API_URL=http://localhost:8000/api" > frontend/.env; \
	fi
	@docker-compose up -d --build
	@sleep 5
	@docker-compose exec -T app php artisan key:generate
	@docker-compose exec -T app php artisan migrate --force
	@echo "✅ Setup complete! Frontend: http://localhost:5173 | Backend: http://localhost:8000"

up: ## Start all containers
	docker-compose up -d

down: ## Stop all containers
	docker-compose down

logs: ## View logs from all services
	docker-compose logs -f

restart: ## Restart all containers
	docker-compose restart

clean: ## Stop and remove all containers, volumes, and images
	docker-compose down -v --rmi all

migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

migrate-fresh: ## Fresh migrate with seeding
	docker-compose exec app php artisan migrate:fresh --seed

test: ## Run PHP tests
	docker-compose exec app php artisan test

shell: ## Open shell in app container
	docker-compose exec app bash

queue: ## View queue logs
	docker-compose logs -f queue

