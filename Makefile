.PHONY: help up down build fresh migrate seed test lint format shell logs frontend

help:
	@echo "SRMS — Makefile targets"
	@echo "  make up         → Start all Docker services"
	@echo "  make down       → Stop all services"
	@echo "  make build      → Rebuild images"
	@echo "  make fresh      → Wipe DB + re-migrate + re-seed"
	@echo "  make migrate    → Run migrations"
	@echo "  make seed       → Seed database"
	@echo "  make test       → Run Pest test suite"
	@echo "  make lint       → Run Pint (PHP) + ESLint (TS)"
	@echo "  make format     → Auto-format PHP + TS"
	@echo "  make shell      → Open a bash shell in the app container"
	@echo "  make logs       → Tail application logs"
	@echo "  make frontend   → Install + run Vite dev server locally"

up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build --no-cache

fresh:
	docker compose exec app php artisan migrate:fresh --seed

migrate:
	docker compose exec app php artisan migrate

seed:
	docker compose exec app php artisan db:seed

test:
	docker compose exec app ./vendor/bin/pest

lint:
	docker compose exec app ./vendor/bin/pint --test
	cd frontend && npm run lint || true

format:
	docker compose exec app ./vendor/bin/pint
	cd frontend && npx eslint --fix . || true

shell:
	docker compose exec app bash

logs:
	docker compose logs -f app

frontend:
	cd frontend && npm install && npm run dev
