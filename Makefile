build:
	docker compose build \
		--build-arg USER_ID=$(id -u) \
		--build-arg USER_NAME=$(id -un) \
		--build-arg GROUP_ID=$(id -g) \
		--build-arg GROUP_NAME=$(id -gn)

up:
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
