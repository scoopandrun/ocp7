# Path: Makefile

.PHONY: help install test fixtures

.DEFAULT_GOAL = help

CURRENT_DIR = $(shell pwd)

help: ## Display this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

vendor: composer.json
	composer install

composer.lock: composer.json
	composer update

install: vendor composer.lock ## Install the project

test: install ## Launch unit tests
	php bin/phpunit

fixtures: install ## Reset the database and load fixtures
	@echo "Resetting the database"
	php bin/console doctrine:database:drop --force
	php bin/console doctrine:database:create
	php bin/console doctrine:schema:update --force
	@echo "Loading the fixtures"
	php bin/console doctrine:fixtures:load --no-interaction
	@echo "Clearing the cache"
	php bin/console cache:clear
	@echo "Done"
