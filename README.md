# Task Management API

> RESTful API для управления задачами с чистой архитектурой, поддержкой двух языков и полным покрытием тестами.

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=for-the-badge&logo=php)](https://www.php.net)
[![PHPUnit](https://img.shields.io/badge/PHPUnit-10.x-36648B?style=for-the-badge&logo=phpunit)](https://phpunit.de)
[![Tests](https://img.shields.io/badge/tests-15%20passed-brightgreen?style=for-the-badge)](https://phpunit.de)

---

## 🚀 Быстрый старт (3 шага)


# 1. Клонировать репозиторий
```
git clone https://github.com/gust3/task-management-api.git
cd task-management-api
```

# 2. Установить зависимости
```
composer install
```

# 3. Настроить и запустить
```
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```
API будет доступно по адресу: http://localhost:8000/api

# Название проекта

## Содержание
- [Особенности](#особенности)
- [Архитектура](#архитектура)
- [Принципы проектирования](#принципы-проектирования)
- [Структура проекта](#структура-проекта)
- [Установка](#установка)
    - [Требования](#требования)
    - [Пошаговая настройка](#пошаговая-настройка)
- [Использование API](#использование-api)
    - [Язык интерфейса](#язык-интерфейса)
    - [Эндпоинты](#эндпоинты)
    - [Обработка ошибок](#обработка-ошибок)
- [Тестирование](#тестирование)
- [Статусы задач](#статусы-задач)
- [Файлы локализации](#файлы-локализации)
- [Возможности для развития](#возможности-для-развития)
- [Лицензия](#лицензия)
- [Автор](#автор)

---

## Особенности

| Фича | Описание |
|------|----------|
| 🌐 **Двуязычный интерфейс** | Поддержка английского и русского языков через заголовок `Accept-Language` |
| 🏗️ **Чистая архитектура** | Соблюдение принципов SOLID и GRASP |
| 📦 **Сервисный слой** | Полное разделение бизнес-логики и HTTP-слоя |
| ✅ **Полное покрытие тестами** | 15+ тестов с проверкой всех сценариев |
| 🎯 **Enum для статусов** | Типобезопасность и автодополнение |
| 🔍 **Детальная обработка ошибок** | Понятные сообщения с подсказками и списком доступных ID |
| 📝 **Form Request** | Валидация на уровне запросов с кастомными сообщениями |
| 🔄 **Dependency Injection** | Внедрение зависимостей через контейнер Laravel |
| 📊 **Resource** | Форматирование ответов через ресурсы |
| 🛡️ **Безопасность** | Чувствительные данные исключены из репозитория |

[⬆️ Вернуться к содержанию](#содержание)
## Архитектура

Проект построен на принципах чистой архитектуры с четким разделением ответственности между слоями.

[⬆️ Вернуться к содержанию](#содержание)

## Принципы проектирования

В проекте реализованы ключевые принципы SOLID и GRASP для обеспечения поддерживаемости и расширяемости кода.

## SOLID и GRASP

| Принцип | Реализация в проекте |
|---------|---------------------|
| **SRP** (Single Responsibility) | Контроллер отвечает только за HTTP-логику, Сервис — только за бизнес-логику |
| **DRY** (Don't Repeat Yourself) | Отсутствие дублирования кода, централизованная обработка ошибок |
| **DI** (Dependency Injection) | Сервис внедряется через конструктор контроллера |
| **OCP** (Open/Closed) | Возможность расширения функционала без изменения существующего кода |
| **LoD** (Law of Demeter) | Контроллер взаимодействует только с сервисом, не работает с моделью напрямую |
| **LSP** (Liskov Substitution) | Возможность замены реализации без изменения поведения системы |
| **ISP** (Interface Segregation) | Клиенты зависят только от методов, которые им действительно нужны |

## Структура проекта

```
task-api/
├── app/
│   ├── Enums/                    # Типобезопасные статусы задач
│   │   └── TaskStatus.php
│   ├── Services/                 # Бизнес-логика
│   │   └── TaskService.php
│   ├── Http/
│   │   ├── Controllers/          # Тонкие контроллеры
│   │   │   └── TaskController.php
│   │   ├── Requests/             # Валидация
│   │   │   ├── StoreTaskRequest.php
│   │   │   └── UpdateTaskRequest.php
│   │   ├── Resources/            # Форматирование ответов
│   │   │   └── TaskResource.php
│   │   └── Middleware/           # Автоопределение языка
│   │       └── SetLocale.php
│   └── Models/
│       └── Task.php              # Модель данных
├── bootstrap/
│   └── app.php                   # Конфигурация (обработка ошибок)
├── database/
│   ├── factories/                # Фабрики для тестов
│   │   └── TaskFactory.php
│   ├── migrations/               # Миграции
│   │   └── ...create_tasks_table.php
│   └── seeders/                  # Сиды
│       └── TaskSeeder.php
├── resources/
│   └── lang/                     # Файлы локализации
│       ├── en/
│       │   ├── messages.php
│       │   └── validation.php
│       └── ru/
│           ├── messages.php
│           └── validation.php
├── routes/
│   └── api.php                   # Роуты API
├── tests/
│   ├── Feature/
│   │   └── TaskControllerTest.php
│   ├── Unit/
│   │   └── TaskServiceTest.php
│   └── TestCase.php
├── .env.example                  # Пример конфигурации
├── .gitignore                    # Игнорируемые файлы
├── composer.json                 # Зависимости
└── README.md                     # Эта документация
```
## Требования

Для запуска проекта необходимы:

- **PHP** 8.1 или выше
- **Composer** 2.x
- **MySQL** 5.7+ или **MariaDB** 10.3+
- **Web-сервер** (Apache/Nginx) или встроенный сервер PHP

## Пошаговая настройка

### Шаг 1: Клонирование и зависимости

```
git clone https://github.com/yourusername/task-management-api.git
cd task-management-api
composer install --no-dev --optimize-autoloader
```

### Шаг 2: Настройка окружения

```
cp .env.example .env
php artisan key:generate
```
Откройте .env и настройте подключение к базе данных:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_api      # ← Создайте эту БД вручную
DB_USERNAME=root
DB_PASSWORD=              # ← Ваш пароль (если есть)
```

### Шаг 3: База данных

sql
```
CREATE DATABASE task_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

апустите миграции с сидами:

bash
```
php artisan migrate:fresh --seed
```

### Шаг 4: Запуск сервера

bash
```
# Встроенный сервер PHP
php artisan serve

# Или через XAMPP/OpenServer
# Поместите проект в папку htdocs и откройте в браузере:
# http://localhost/task-api/public/api/tasks
```

## 📖 Использование API

## Базовый URL
```
http://localhost:8000/api
```

## Язык интерфейса
Добавьте заголовок Accept-Language в запрос:

| Код | Язык | По умолчанию | Как активировать |
|-----|------|--------------|------------------|
| `en` | 🇬🇧 Английский | ✅ Да | `Accept-Language: en` или любой неизвестный язык |
| `ru` | 🇷🇺 Русский | ❌ Нет | `Accept-Language: ru` |

## Пример (curl):

bash
```
curl -X GET http://localhost:8000/api/tasks \
-H "Accept-Language: ru"
```

## Эндпоинты

GET /api/tasks — Получить все задачи

### Запрос:

bash
```
curl -X GET http://localhost:8000/api/tasks \
  -H "Accept-Language: en"
```

#### Ответ (200):

json
```
{
  "success": true,
  "message": "Task list retrieved successfully",
  "data": [
    {
      "id": 1,
      "title": "Buy groceries",
      "description": "Milk, eggs, bread",
      "status": "pending",
      "created_at": "2026-01-31T12:00:00.000000Z",
      "updated_at": "2026-01-31T12:00:00.000000Z"
    }
  ],
  "count": 1
}
```

POST /api/tasks — Создать задачу

### Запрос:

bash
```
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept-Language: en" \
  -d '{
    "title": "Complete report",
    "description": "Q4 financial report",
    "status": "in_progress"
  }'
```

#### Ответ (201):

json
```
{
  "success": true,
  "message": "Task created successfully",
  "data": {
    "id": 2,
    "title": "Complete report",
    "description": "Q4 financial report",
    "status": "in_progress",
    "created_at": "2026-01-31T12:05:00.000000Z",
    "updated_at": "2026-01-31T12:05:00.000000Z"
  }
}
```

GET /api/tasks/{id} — Получить задачу по ID

### Запрос:

bash
```
curl -X GET http://localhost:8000/api/tasks/1 \
  -H "Accept-Language: en"
```

#### Ответ (200):

json
```
{
  "success": true,
  "message": "Task retrieved successfully",
  "data": {
    "id": 1,
    "title": "Buy groceries",
    "description": "Milk, eggs, bread",
    "status": "pending",
    "created_at": "2026-01-31T12:00:00.000000Z",
    "updated_at": "2026-01-31T12:00:00.000000Z"
  }
}
```

PUT /api/tasks/{id} — Обновить задачу

#### Запрос:

bash
```
curl -X PUT http://localhost:8000/api/tasks/1 \
  -H "Content-Type: application/json" \
  -H "Accept-Language: en" \
  -d '{
    "status": "completed"
  }'
```

#### Ответ (200):

json
```
{
  "success": true,
  "message": "Task updated successfully",
  "data": {
    "id": 1,
    "title": "Buy groceries",
    "description": "Milk, eggs, bread",
    "status": "completed",
    "created_at": "2026-01-31T12:00:00.000000Z",
    "updated_at": "2026-01-31T12:10:00.000000Z"
  }
}
```

DELETE /api/tasks/{id} — Удалить задачу

#### Запрос:

bash
```
curl -X DELETE http://localhost:8000/api/tasks/1 \
  -H "Accept-Language: en"
```


#### Ответ (200):

json
```
{
  "success": true,
  "message": "Task deleted successfully",
  "hint": "Task with ID 1 no longer exists in the database"
}
```

## Обработка ошибок

### Ошибка валидации (422)

#### Запрос (без обязательного поля title):

bash
```
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept-Language: en" \
  -d '{"description": "No title"}'
```

#### Ответ (422):

json
```
{
  "success": false,
  "message": "Validation failed",
  "hint": "Check the input fields",
  "errors": {
    "title": ["The \"title\" field is required"]
  },
  "validation_rules": {
    "title": "Required field, string, maximum 255 characters",
    "description": "Optional field, string",
    "status": "Optional field, one of: pending, in_progress, completed"
  }
}
```

## Задача не найдена (404)

### Запрос (несуществующий ID):

bash
```
curl -X GET http://localhost:8000/api/tasks/999 \
  -H "Accept-Language: en"
```

#### Ответ (404):

json
```
{
  "success": false,
  "message": "Task not found",
  "hint": "Check the ID. The task may have been deleted.",
  "available_ids": [1, 2, 3, 4, 5]
}
```

### Несуществующий эндпоинт (404)

#### Запрос (ошибка в URL):

bash
```
curl -X GET http://localhost:8000/api/tasks22 \
  -H "Accept-Language: en"
```

#### Ответ (404):

json
```
{
  "success": false,
  "message": "Endpoint not found",
  "hint": "Check the URL. You may have made a mistake in the address."
}
```

## 🧪 Тестирование

### Запуск всех тестов

bash
```
php artisan test
```

## Результаты

```
PASS  Tests\Feature\TaskControllerTest
  ✓ can get all tasks
  ✓ can create task
  ✓ validation error when creating task without title
  ✓ can get single task
  ✓ returns 404 for nonexistent task
  ✓ can update task
  ✓ can delete task
  ✓ returns 404 when deleting nonexistent task
  ✓ can get tasks in russian

PASS  Tests\Unit\TaskServiceTest
  ✓ can create task
  ✓ can get task by id
  ✓ throws exception when task not found
  ✓ can update task
  ✓ can delete task
  ✓ can get all task ids

Tests:  15 passed
Time:   3.5s
```

## Запуск конкретного теста

bash
```
# Один тест
php artisan test --filter can_create_task

# Все тесты контроллера
php artisan test --testsuite=Feature

# Все юнит-тесты
php artisan test --testsuite=Unit
```

## Статусы задач

| Статус | Описание | Значение в API |
|--------|----------|----------------|
| 🟡 В ожидании | Задача создана, но ещё не начата | `pending` |
| 🟠 В работе | Работа над задачей начата | `in_progress` |
| 🟢 Завершена | Задача выполнена | `completed` |

## 📁 Файлы локализации

```
resources/lang/
├── en/
│   ├── messages.php      # Сообщения интерфейса
│   └── validation.php    # Ошибки валидации
└── ru/
    ├── messages.php      # Сообщения интерфейса
    └── validation.php    # Ошибки валидации
```

## 🎯 Возможности для развития

- Аутентификация (JWT/Bearer tokens)
- Пагинация списка задач
- Фильтрация и сортировка
- Теги и категории задач
- Экспорт/импорт (CSV, JSON)
- WebSocket-уведомления
- Swagger/OpenAPI документация
- Docker-контейнеризация
- CI/CD пайплайн (GitHub Actions)

## 📝 Лицензия

Этот проект лицензирован под лицензией MIT.
```
MIT License

Copyright (c) 2026 Your Name

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

## 👨‍💻 Автор

Разработано с ❤️для демонстрации лучших практик современной разработки на Laravel.
