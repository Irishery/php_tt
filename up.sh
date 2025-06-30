#!/bin/bash
set -e

# Загружаем переменные из .env
if [ -f .env ]; then
  export $(grep -v '^#' .env | xargs)
fi

MODE="dev"
if [[ "$1" == "--prod" ]]; then
  MODE="prod"
fi

echo "Выбран режим: $MODE"

if [[ "$2" == "--reset" ]]; then
  echo "Удаление volume базы данных..."
  docker-compose -f docker-compose.$MODE.yml down -v
else
  echo "Остановка контейнеров без удаления данных..."
  docker-compose -f docker-compose.$MODE.yml down
fi

echo "Пересборка контейнеров..."
docker-compose -f docker-compose.$MODE.yml build --no-cache

echo "Запуск контейнеров..."
docker-compose -f docker-compose.$MODE.yml up -d

echo "Ожидание готовности MariaDB (максимум 30 секунд)..."
for i in {1..30}; do
  if docker-compose -f docker-compose.$MODE.yml exec db mysqladmin ping -h localhost -u root -proot --silent; then
    echo "MariaDB готова к подключению"
    break
  fi
  echo "Ожидание ($i/30)..."
  sleep 1
done

if ! docker-compose -f docker-compose.$MODE.yml exec db mysqladmin ping -h localhost -u root -proot --silent; then
  echo "MariaDB не запустилась за отведённое время"
  docker-compose -f docker-compose.$MODE.yml logs db
  exit 1
fi

echo "Проверка подключения из PHP-контейнера..."
docker-compose -f docker-compose.$MODE.yml exec php php -r "
require_once '/var/www/html/app/Core/Database.php';
try {
    \$pdo = Database::connect();
    echo 'Подключение успешно. Версия базы данных: ' . \$pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . \"\n\";
} catch (PDOException \$e) {
    echo 'Ошибка подключения: ' . \$e->getMessage() . \"\n\";
    exit(1);
}
"

echo "Применение миграций..."
docker-compose -f docker-compose.$MODE.yml exec php php /var/www/html/app/migrate.php

echo

URL="${BASE_URL:-http://localhost:8000}"

echo "Готово. Приложение доступно по адресу $URL"
