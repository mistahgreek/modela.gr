.PHONY: help install up down build shell migrate seed fresh test clean

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Install Laravel and dependencies
	composer create-project laravel/laravel:^11.0 temp-laravel
	@if [ -d "temp-laravel" ]; then \
		cp -r temp-laravel/* . && \
		cp -r temp-laravel/.* . 2>/dev/null || true && \
		rm -rf temp-laravel && \
		echo "Laravel installed successfully"; \
	fi
	composer install
	npm install
	cp .env.example .env || true
	php artisan key:generate
	chmod -R 777 storage bootstrap/cache

build: ## Build Docker containers
	docker-compose build

up: ## Start Docker containers
	docker-compose up -d

down: ## Stop Docker containers
	docker-compose down

shell: ## Access PHP container shell
	docker-compose exec php sh

shell-root: ## Access PHP container shell as root
	docker-compose exec -u root php sh

migrate: ## Run database migrations
	docker-compose exec php php artisan migrate

migrate-fresh: ## Fresh database with seed
	docker-compose exec php php artisan migrate:fresh --seed

seed: ## Run database seeders
	docker-compose exec php php artisan db:seed

fresh: ## Fresh install with migrations and seeds
	docker-compose exec php php artisan migrate:fresh --seed

test: ## Run tests
	docker-compose exec php php artisan test

queue: ## Start queue worker
	docker-compose exec php php artisan queue:work

clear: ## Clear all caches
	docker-compose exec php php artisan cache:clear
	docker-compose exec php php artisan config:clear
	docker-compose exec php php artisan route:clear
	docker-compose exec php php artisan view:clear

optimize: ## Optimize application
	docker-compose exec php php artisan config:cache
	docker-compose exec php php artisan route:cache
	docker-compose exec php php artisan view:cache

clean: ## Clean up containers and volumes
	docker-compose down -v
	rm -rf vendor node_modules

logs: ## Show Docker logs
	docker-compose logs -f

npm-dev: ## Run npm dev
	npm run dev

npm-build: ## Build assets for production
	npm run build
