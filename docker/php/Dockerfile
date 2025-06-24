FROM php:8.2-cli

# Установка необходимых пакетов и расширений
RUN apt-get update && apt-get install -y \
    mariadb-client \
    zip unzip \
    && docker-php-ext-install pdo pdo_mysql

# Установка рабочей директории
WORKDIR /var/www/html

# Копируем исходники проекта внутрь контейнера
COPY . /var/www/html

# Запускаем встроенный PHP сервер
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
