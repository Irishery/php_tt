#!/bin/bash

set -e  # Прекращать выполнение при ошибках

echo "🧹 Остановка и очистка старых контейнеров..."
docker-compose down -v

echo "🔧 Сборка образов..."
docker-compose build

echo "🚀 Запуск контейнеров..."
docker-compose up -d

echo "⏳ Ожидание запуска MariaDB..."
sleep 10

echo "🔍 Поиск PHP-контейнера..."
PHP_CONTAINER=$(docker ps -qf "name=php")

if [ -z "$PHP_CONTAINER" ]; then
  echo "❌ Не удалось найти контейнер с PHP"
  docker ps
  exit 1
fi

echo "🗃 Применение миграций в контейнере $PHP_CONTAINER..."
docker exec -it "$PHP_CONTAINER" php app/migrate.php

echo ""
echo "✅ Всё готово. Открой в браузере: http://localhost:8000"
