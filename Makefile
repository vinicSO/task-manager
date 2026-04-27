SAIL = ./vendor/bin/sail

.PHONY: help up down install migrate seed test

help: ## Mostra a ajuda
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

up: ## Sobe o ambiente Docker
	$(SAIL) up -d

down: ## Para o ambiente Docker
	$(SAIL) down

install: ## Instala dependências e gera chave
	composer install
	cp .env.example .env
	$(SAIL) up -d
	php artisan key:generate
	$(SAIL) artisan migrate --seed

migrate: ## Roda as migrations
	$(SAIL) artisan migrate

test: ## Roda os testes (Diferencial do Desafio!)
	$(SAIL) artisan test