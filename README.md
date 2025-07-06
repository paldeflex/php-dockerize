# Docker-сборка для PHP

Этот репозиторий содержит примерную конфигурацию Docker для запуска PHP-приложения с Nginx, MySQL и Xdebug. Службы описаны в `compose.yml`.

## Содержимое
- `php/Dockerfile` — образ PHP 8.4 с Composer и Xdebug.
- `nginx/conf.d/default.conf` — конфигурация сервера.
- `mysql/init/init.sql` — скрипт, создающий тестовую базу.
- `app/` — пример кода и тестов.

## Запуск
1. Скопируйте файл окружения:
   ```bash
   cp .env.example .env
   ```
2. Соберите и запустите контейнеры:
   ```bash
   docker compose up -d --build
   ```
3. Перейдите по адресу [http://localhost](http://localhost) и убедитесь, что сервисы работают.

## Тесты
Для выполнения тестов PHPUnit используйте команду:
```bash
docker compose exec php composer run-phpunit
```
Тесты используют базу `DB_TEST_DATABASE`, которая инициализируется скриптом из `mysql/init`.

## Остановка сервисов
Остановить и удалить контейнеры можно так:
```bash
docker compose down
```
