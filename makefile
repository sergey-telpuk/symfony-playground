# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc test

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@#$(DOCKER_COMP) build --pull --no-cache
	@$(DOCKER_COMP) build

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: down build up vendor migrations load_fixtures ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(PHP_CONT) bash

test: build up migrations_test clear_cache_test ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)

test_coverage: c= --coverage-text
test_coverage: build up migrations_test clear_cache_test
	@$(DOCKER_COMP) exec -e APP_ENV=test -e XDEBUG_MODE=coverage php  bin/phpunit $(c)

db_test: c= --no-interaction --if-not-exists
db_test:
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/console doctrine:database:create $(c)

clear_cache_test:
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/console cache:clear

migrations_test: c= --no-interaction
migrations_test: db_test
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/console doctrine:migrations:migrate $(c)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/console doctrine:fixtures:load $(c)

migrations: c= --no-interaction
migrations:
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=dev php bin/console doctrine:migrations:migrate $(c)

load_fixtures: c= --no-interaction
load_fixtures:
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=dev php bin/console doctrine:fixtures:load $(c)

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf
