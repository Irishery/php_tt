# URL Shortener

Простой сервис сокращения URL на PHP с MariaDB и Docker.

---

## Описание

* Генерация коротких ссылок из длинных URL
* Хранение в MariaDB с использованием PDO и миграций
* Настройки через `.env` файл
* Веб-интерфейс с аналитикой переходов и отображением карт
* Поддержка API для внешнего взаимодействия

---

## Требования

* Docker
* Docker Compose
* PHP 8+ (в контейнере)

---

## Быстрый старт

1. Клонируйте репозиторий и перейдите в папку проекта:

   ```bash
   git clone https://github.com/Irishery/php_tt.git
   cd php_tt
   ```

2. Скопируйте шаблон `.env-template` в `.env` и заполните своими параметрами:

   ```bash
   cp .env-template .env
   # отредактируйте .env под свои настройки (БД, SMTP, BASE_URL)
   ```

3. Запустите установочный скрипт:

   ```bash
   ./up.sh [--prod] [--reset]
   ```

   Опции:
   `--prod` — запуск в продакшн-режиме (`docker-compose.prod.yml`)
   `--reset` — удаление volume с базой данных перед запуском

   Скрипт выполнит:

   * Остановит контейнеры (с опциональным удалением данных)
   * Пересоберёт образы без кеша
   * Запустит контейнеры в фоне
   * Подождёт готовности MariaDB
   * Проверит подключение к базе из PHP-контейнера
   * Применит миграции
   * Выведет ссылку на приложение

---

## Структура проекта

* `/app` — PHP-код (контроллеры, модели, ядро, миграции)
* `/app/migrations` — SQL миграции для БД
* `/config` — конфигурация приложения с загрузкой из `.env`
* `/docker` — Dockerfile для PHP
* `docker-compose.dev.yml` — конфигурация для разработки
* `docker-compose.prod.yml` — конфигурация для продакшна
* `up.sh` — скрипт запуска, остановки, миграций и проверки
* `.env-template` — шаблон файла с настройками окружения

---

## Основные маршруты (Web)

| Метод | URL          | Описание                            |
| ----- | ------------ | ----------------------------------- |
| GET   | `/`          | Форма ввода URL для сокращения      |
| POST  | `/shorten`   | Создание короткой ссылки            |
| GET   | `/r/{code}`  | Перенаправление на оригинальный URL |
| GET   | `/login`     | Страница входа                      |
| POST  | `/login`     | Обработка входа                     |
| GET   | `/register`  | Страница регистрации                |
| POST  | `/register`  | Регистрация пользователя            |
| GET   | `/verify`    | Подтверждение email по токену       |
| GET   | `/analytics` | Просмотр статистики и аналитики     |
| GET   | `/logout`    | Выход из системы                    |

---

## API

В проекте реализован API с авторизацией по токену. Все запросы должны иметь заголовок:

```http
Authorization: Bearer {token}
```

### Получение токена

**POST** `/api/login`
`Content-Type: application/json`

```json
{
  "email": "user@example.com",
  "password": "your_password"
}
```

**Ответ:**

```json
{
  "token": "e84d9f60b00a4f29a429ab...",
  "user_id": 1
}
```

---

### Получение статистики ссылок

**GET** `/api/analytics`
(требуется заголовок `Authorization`)

**Ответ:**

```json
{
  "stats": [
    {
      "url": {
        "id": 1,
        "original_url": "https://example.com",
        "short_code": "abc123",
        ...
      },
      "clicks": [
        {
          "ip_address": "192.0.2.1",
          "clicked_at": "2024-06-30 10:25:00"
        },
        ...
      ]
    }
  ],
  "markers": [
    {
      "lat": 55.75,
      "lon": 37.62,
      "ip": "192.0.2.1",
      "country": "RU"
    }
  ]
}
```

---

### Сокращение URL через API

**POST** `/api/shorten`
`Content-Type: application/json`
(требуется авторизация)

```json
{
  "url": "https://example.com"
}
```

**Ответ:**

```json
{
  "original_url": "https://example.com",
  "short": "abc123"
}
```

---

## Работа с базой данных

* Используются миграции для создания и обновления схемы
* Подключение к MariaDB через `PDO`
* Параметры БД задаются в `.env`:

  ```env
  DB_HOST=db
  DB_NAME=url_shortener
  DB_USER=root
  DB_PASS=root
  ```

---

## Примечания

* Для отправки email используется SMTP (настройки в `.env`)
* Все чувствительные данные находятся в `.env` и не попадают в Git
* Для авторизации API используется Bearer-токен, который создаётся при логине

---

## Схема базы данных

[Ссылка на Google Drive](https://drive.google.com/file/d/1F2IGthCdVf5Yiol2nMqpCiwZ15kU2esm/view?usp=sharing)
