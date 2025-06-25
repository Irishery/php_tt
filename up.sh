set -e

if [[ "$1" == "--reset" ]]; then
  echo "🧨 Удаляем volume с базой данных..."
  docker-compose down -v
else
  echo "🛑 Останавливаем контейнеры без удаления данных..."
  docker-compose down
fi

echo "🔧 Пересобираем контейнеры с очисткой кеша..."
docker-compose build --no-cache

echo "🚀 Запускаем контейнеры в фоновом режиме..."
docker-compose up -d

echo "⏳ Ожидаем готовности MariaDB (макс 30 секунд)..."
for i in {1..30}; do
  # Улучшенная проверка через mysqladmin ping
  if docker-compose exec db mysqladmin ping -h localhost -u root -proot --silent; then
    echo "✅ MariaDB готова к подключению"
    break
  fi
  echo "⌛ Ожидание готовности БД ($i/30)..."
  sleep 2
done

# Проверка успешности ожидания
if ! docker-compose exec db mysqladmin ping -h localhost -u root -proot --silent; then
  echo "❌ MariaDB не запустилась за отведенное время"
  docker-compose logs db
  exit 1
fi

echo "🔍 Проверка подключения из PHP-контейнера..."
docker-compose exec php php -r "
require_once '/var/www/html/app/Core/Database.php'; 
try {
    \$pdo = Database::connect();
    echo '✅ Подключение успешно. Версия БД: '.\$pdo->getAttribute(PDO::ATTR_SERVER_VERSION).\"\n\";
} catch (PDOException \$e) {
    echo '❌ Ошибка подключения: '.\$e->getMessage().\"\n\";
    exit(1);
}
"

echo "🗃 Применение миграций..."
docker-compose exec php php /var/www/html/app/migrate.php

echo ""
echo "🌍 Готово! Приложение доступно по http://localhost:8000"
