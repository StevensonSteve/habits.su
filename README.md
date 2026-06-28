## Docker
```sh 
#Создайте .env файл
cp .env.example .env
# Запуск всех сервисов
docker compose up -d
# Запуск с пересборкой образов
docker compose up -d --build
 # Просмотр логов
docker compose logs -f
docker compose logs php-fpm
 # Остановка всех сервисов
docker compose stop
 # Перезапуск конкретного сервиса
docker compose restart php-fpm
# Вход в PHP контейнер
docker compose exec php-fpm bash
# Просмотр статуса контейнеров
docker compose ps
# Просмотр использования ресурсов
docker stats
# Очистка неиспользуемых данных Docker
docker system prune
# Просмотр информации о сети
docker network inspect docker-symfony_symfony-network
```

## Прочие команды
```sh
# Список доступных команд symfony
docker compose exec php-fpm php bin/console list
# Обращение в PostgreSQL прямой запрос
docker compose exec php-fpm php bin/console dbal:run-sql "SELECT * FROM users;"
# Доступ к PostgreSQL
docker compose exec postgres psql -U symfony symfony
# Резервное копирование
docker compose exec postgres pg_dump -U symfony symfony > backup.sql
# Восстановление
docker compose exec -T postgres psql -U symfony symfony < backup.sql
# Доступ к Redis CLI
docker compose exec redis redis-cli
# Мониторинг команд Redis
docker compose exec redis redis-cli monitor
# Запуск тестов
docker compose exec php-fpm php bin/phpunit
# Запуск теста для конкретно файла 
docker compose exec php-fpm php bin/phpunit tests/Controller/Vehicle/TruckControllerTest.php
```
