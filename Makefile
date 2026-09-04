build:
	docker compose build --no-cache \
		--build-arg USER_ID=$(shell id -u) \
		--build-arg USER_NAME=$(shell id -un) \
		--build-arg GROUP_ID=$(shell id -g) \
		--build-arg GROUP_NAME=$(shell id -gn) \

init: build up composer-install migrate fixtures

prod-deploy: prod-build prod-up prod-composer-install prod-migrate prod-cache-clear

prod-build:
	docker compose -f docker-compose.prod.yml build \
		--build-arg USER_ID=$(shell id -u) \
		--build-arg USER_NAME=$(shell id -un) \
		--build-arg GROUP_ID=$(shell id -g) \
		--build-arg GROUP_NAME=$(shell id -gn)

prod-up:
	docker compose -f docker-compose.prod.yml up -d

prod-down:
	docker compose -f docker-compose.prod.yml down

prod-composer-install:
	docker compose -f docker-compose.prod.yml exec -T php-fpm composer install --no-dev --optimize-autoloader --no-interaction

prod-migrate:
	docker compose -f docker-compose.prod.yml exec -T php-fpm php bin/console doctrine:migrations:migrate --no-interaction

prod-cache-clear:
	docker compose -f docker-compose.prod.yml exec -T php-fpm php bin/console cache:clear --env=prod --no-interaction

prod-git-pull:
	git pull origin master --ff-only

prod-update: prod-git-pull prod-cache-clear

up:
	docker compose up -d

down:
	docker compose down

composer-install:
	docker compose exec php-fpm composer install

migrate:
	docker compose exec php-fpm php bin/console doctrine:migrations:migrate --no-interaction

migration-diff:
	docker compose exec php-fpm php bin/console doctrine:migrations:diff --no-interaction

fixtures:
	docker compose exec php-fpm php bin/console doctrine:fixtures:load --no-interaction

cache-clear:
	docker compose exec php-fpm php bin/console cache:clear

ps:
	docker compose ps

restart: down up

test:
	docker compose exec php-fpm php bin/phpunit

fix:
	docker compose exec php-fpm vendor/bin/ecs --fix

check:
	docker compose exec php-fpm vendor/bin/ecs
