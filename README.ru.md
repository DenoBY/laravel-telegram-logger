# Laravel Telegram Logger

[English](README.md) | [Русский](README.ru.md)

Laravel пакет для отправки логов и сообщений приложения в Telegram.

## Возможности

- Отправка Laravel логов в Telegram чат/группу/канал
- Поддержка топиков (тредов) Telegram
- Fluent-конструктор сообщений с поддержкой фото/видео
- Настраиваемая фильтрация исключений
- Информация о пользователе в сообщениях
- Markdown форматирование

## Требования

- PHP 8.1+
- Laravel 10.x, 11.x или 12.x

## Установка

```bash
composer require denoby/laravel-telegram-logger
```

Пакет автоматически зарегистрирует свой сервис-провайдер.

### Публикация конфигурации

```bash
php artisan vendor:publish --provider="DenoBY\TelegramLogger\ServiceProvider"
```

## Настройка

Добавьте в файл `.env`:

```env
TELEGRAM_LOG_TOKEN=токен-вашего-бота
TELEGRAM_LOG_CHAT_ID=id-вашего-чата
TELEGRAM_LOG_THREAD_ID=id-топика-опционально
```

### Получение токена бота

1. Откройте Telegram и найдите [@BotFather](https://t.me/BotFather)
2. Отправьте `/newbot` и следуйте инструкциям
3. Скопируйте полученный токен

### Получение Chat ID

1. Добавьте бота в группу/канал, куда хотите получать логи
2. Отправьте сообщение в группу
3. Откройте `https://api.telegram.org/bot<ТОКЕН_БОТА>/getUpdates`
4. Найдите `chat.id` в ответе

## Использование

### Как канал логирования

Добавьте Telegram канал в `config/logging.php`:

```php
'channels' => [
    // ... другие каналы

    'telegram' => [
        'driver' => 'custom',
        'via' => \DenoBY\TelegramLogger\Logger::class,
        'level' => env('LOG_LEVEL', 'error'),
    ],
],
```

Можно добавить в стек:

```php
'stack' => [
    'driver' => 'stack',
    'channels' => ['daily', 'telegram'],
    'ignore_exceptions' => false,
],
```

Или использовать напрямую:

```php
Log::channel('telegram')->error('Что-то пошло не так!');
```

### Как Tap (добавление к существующим каналам)

Можно добавить Telegram логирование к любому существующему каналу через опцию `tap`. Логи будут отправляться и в оригинальный канал, и в Telegram:

```php
'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => 'debug',
    'tap' => [\DenoBY\TelegramLogger\Tap::class],
],
```

`Tap` использует значение конфига `telegram-logger.level` (по умолчанию: `error`) для фильтрации логов. Можно задать через переменную окружения:

```env
TELEGRAM_LOG_LEVEL=error
```

### Прямая отправка сообщений

Используйте фасад для отправки сообщений:

```php
use DenoBY\TelegramLogger\Facades\Telegram;
use DenoBY\TelegramLogger\Message;

// Простое сообщение
Telegram::sendMessageToChat(
    new Message('Привет из Laravel!'),
    config('telegram-logger.chat_id')
);

// С топиком (тредом)
Telegram::sendMessageToChat(
    new Message('Привет!'),
    config('telegram-logger.chat_id'),
    threadId: config('telegram-logger.thread_id')
);
```

### Создание сообщений

```php
use DenoBY\TelegramLogger\Message;

// Сообщение с фото
$message = (new Message('Посмотрите эти фото!'))
    ->addPhoto('https://example.com/image1.jpg')
    ->addPhoto('https://example.com/image2.jpg');

// Добавление текста в начало/конец
$message = (new Message('Основной контент'))
    ->prependText('Заголовок:')
    ->appendText('Подвал');
```

### Отправка документов

```php
use DenoBY\TelegramLogger\Facades\Telegram;

Telegram::sendDocument(
    filePath: '/path/to/document.pdf',
    chatId: config('telegram-logger.chat_id'),
    caption: 'Ваш документ',
    threadId: config('telegram-logger.thread_id')
);
```

## Параметры конфигурации

```php
// config/telegram-logger.php

return [
    // Токен бота от @BotFather
    'token' => env('TELEGRAM_LOG_TOKEN'),

    // ID чата/группы/канала
    'chat_id' => env('TELEGRAM_LOG_CHAT_ID'),

    // ID топика (для супергрупп с топиками)
    'thread_id' => env('TELEGRAM_LOG_THREAD_ID'),

    // Информация о приложении для сообщений
    'app_name' => env('APP_NAME', 'Laravel'),
    'app_url' => env('APP_URL'),
    'environment' => env('APP_ENV', 'production'),
    'deploy_branch' => env('DEPLOY_BRANCH'),

    // Исключения, которые полностью игнорируются
    'ignore_exceptions' => [
        // \Symfony\Component\Mailer\Exception\TransportException::class,
    ],

    // Исключения без стек-трейса
    'ignore_stack_trace_for' => [
        // \App\Exceptions\CustomException::class,
    ],

    // Максимальная длина сообщения (лимит Telegram — 4096)
    'max_message_length' => 3040,

    // Включать информацию об авторизованном пользователе
    'include_user_info' => true,

    // HTTP таймаут в секундах
    'timeout' => 5,

    // Минимальный уровень логов для TelegramTap
    'level' => env('TELEGRAM_LOG_LEVEL', 'error'),
];
```

## Формат сообщений

Сообщения форматируются в Markdown и содержат:

- **App URL** — кликабельная ссылка на приложение
- **Environment** — текущее окружение (production, staging и т.д.)
- **Log Level** — уровень как хэштег (#ERROR, #WARNING и т.д.)
- **Branch** — ветка деплоя (если настроена)
- **User ID** — информация об авторизованном пользователе (если включено)
- **File** — файл и строка, где произошло исключение
- **Message** — текст сообщения
- **Stack Trace** — отфильтрованный стек-трейс (только файлы приложения)

## Лицензия

MIT License. Подробнее в файле [LICENSE](LICENSE).
