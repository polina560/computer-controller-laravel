# Laravel 12 Backend Template

Шаблон для быстрого старта разработки бэкенд-приложений на Laravel 12.

## Требования к ПО

Для работы с проектом необходимо:

- **PHP 8.3** или новее
- **Node.js 20** или новее
- **MySQL 8.0** или новее

---

## Настройка OpenServer

Для запуска проекта на OpenServer выполните следующие шаги:

1. Скопируйте папку `.osp.example` в `.osp`.
2. В файлах `.osp/project.ini`, `.osp/tasks.ini` и в имени файла `.osp/Nginx/domain.loc.conf` замените `domain.loc` на нужный вам домен.
3. Включите модули Nginx и PHP-FCGI в настройках OpenServer.
4. Перезапустите OpenServer, чтобы изменения вступили в силу.

---

## Установка и запуск проекта

### 1. Клонирование репозитория

```
git clone [URL репозитория]
cd [название папки проекта]
```

### 2. Установка PHP зависимостей

```shell
composer install
```

### 3. Установка JavaScript зависимостей

```shell
npm install
```

### 4. Настройка окружения

- Скопируйте файл `.env.example` в `.env`
- Настройте параметры базы данных в `.env`
- Сгенерируйте ключ приложения:
```shell
php artisan key:generate
```
- Создайте symlink на папку storage/app/public:
```shell
php artisan storage:link
```

### 5. Запуск миграций

```shell
php artisan migrate
```

### 6. Создание администратора Moonshine

```shell
php artisan moonshine:create-user
```
Команда создаст администратора согласно env переменным:
 - `MOONSHINE_USERNAME`
 - `MOONSHINE_NAME`
 - `MOONSHINE_PASSWORD`

Либо доступы можно передать атрибутами:
- `{--u|username= : Username}`
- `{--N|name= : Name}`
- `{--p|password= : Password}`

---

## Локальный запуск проекта

1. Запуск Laravel-сервера:
   ```shell
   php artisan serve
   ```

2. Запуск среды разработки Vite:
   ```shell
   npm run dev
   ```

3. Админ панель Moonshine будет доступна по адресу:
   ```
   http://localhost:8000/admin
   ```
---

## Оптимизация и управление очередями

- Оптимизация приложения (кэширование конфигураций, роутов и пр.):

```shell
php artisan optimize
```

- Запуск обработки очередей (worker для очередей Laravel):

```shell
php artisan queue:work
```

---

### Полезные команды

- Сборка фронтенда для production:
```shell
npm run build
```

- Запуск линтеров:
```shell
npm run lint
```

- Форматирование кода:
```shell
npm run format
```
