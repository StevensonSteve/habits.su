build:
	docker compose build \
		--build-arg USER_ID=$(shell id -u) \
		--build-arg USER_NAME=$(shell id -un) \
		--build-arg GROUP_ID=$(shell id -g) \
		--build-arg GROUP_NAME=$(shell id -gn)


up: build
	docker compose up -d

down:
	docker compose down

ps:
	docker compose ps

restart: down up

fix:
	docker compose exec php-fpm vendor/bin/ecs --fix

check:
	docker compose exec php-fpm vendor/bin/ecs
