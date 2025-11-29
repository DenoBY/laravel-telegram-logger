# Laravel Telegram Logger

[English](README.md) | [Русский](README.ru.md)

A Laravel package for sending application logs and messages to Telegram.

## Features

- Send Laravel logs to Telegram chat/group/channel
- Support for Telegram topics (threads)
- Fluent message builder with photo/video support
- Configurable exception filtering
- User info in log messages
- Markdown formatting

## Requirements

- PHP 8.1+
- Laravel 10.x, 11.x, or 12.x

## Installation

```bash
composer require denoby/laravel-telegram-logger
```

The package will auto-register its service provider.

### Publish Configuration

```bash
php artisan vendor:publish --provider="DenoBY\TelegramLogger\ServiceProvider"
```

## Configuration

Add the following to your `.env` file:

```env
TELEGRAM_LOG_TOKEN=your-bot-token
TELEGRAM_LOG_CHAT_ID=your-chat-id
TELEGRAM_LOG_THREAD_ID=optional-thread-id
```

### Getting Your Bot Token

1. Open Telegram and search for [@BotFather](https://t.me/BotFather)
2. Send `/newbot` and follow the instructions
3. Copy the token provided

### Getting Your Chat ID

1. Add your bot to the group/channel where you want to receive logs
2. Send a message to the group
3. Visit `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`
4. Find the `chat.id` in the response

## Usage

### As a Log Channel

Add the Telegram channel to your `config/logging.php`:

```php
'channels' => [
    // ... other channels

    'telegram' => [
        'driver' => 'custom',
        'via' => \DenoBY\TelegramLogger\Logger::class,
        'level' => env('LOG_LEVEL', 'error'),
    ],
],
```

You can add it to your stack:

```php
'stack' => [
    'driver' => 'stack',
    'channels' => ['daily', 'telegram'],
    'ignore_exceptions' => false,
],
```

Or use it directly:

```php
Log::channel('telegram')->error('Something went wrong!');
```

### As a Tap (Adding to Existing Channels)

You can add Telegram logging to any existing channel using the `tap` option. This sends logs to both the original channel AND Telegram:

```php
'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => 'debug',
    'tap' => [\DenoBY\TelegramLogger\Tap::class],
],
```

The `Tap` class uses the `telegram-logger.level` config value (default: `error`) to filter which logs are sent to Telegram. You can set it via environment variable:

```env
TELEGRAM_LOG_LEVEL=error
```

### Sending Messages Directly

Use the Facade to send messages:

```php
use DenoBY\TelegramLogger\Facades\Telegram;
use DenoBY\TelegramLogger\Message;

// Simple message
Telegram::sendMessageToChat(
    new Message('Hello from Laravel!'),
    config('telegram-logger.chat_id')
);

// With topic (thread)
Telegram::sendMessageToChat(
    new Message('Hello!'),
    config('telegram-logger.chat_id'),
    threadId: config('telegram-logger.thread_id')
);
```

### Building Messages

```php
use DenoBY\TelegramLogger\Message;

// Create message with photos
$message = (new Message('Check out these photos!'))
    ->addPhoto('https://example.com/image1.jpg')
    ->addPhoto('https://example.com/image2.jpg');

// Append/prepend text
$message = (new Message('Main content'))
    ->prependText('Header:')
    ->appendText('Footer');
```

### Sending Documents

```php
use DenoBY\TelegramLogger\Facades\Telegram;

Telegram::sendDocument(
    filePath: '/path/to/document.pdf',
    chatId: config('telegram-logger.chat_id'),
    caption: 'Here is your document',
    threadId: config('telegram-logger.thread_id')
);
```

## Configuration Options

```php
// config/telegram-logger.php

return [
    // Bot token from @BotFather
    'token' => env('TELEGRAM_LOG_TOKEN'),

    // Chat/Group/Channel ID
    'chat_id' => env('TELEGRAM_LOG_CHAT_ID'),

    // Topic ID (for supergroups with topics)
    'thread_id' => env('TELEGRAM_LOG_THREAD_ID'),

    // App info for log messages
    'app_name' => env('APP_NAME', 'Laravel'),
    'app_url' => env('APP_URL'),
    'environment' => env('APP_ENV', 'production'),
    'deploy_branch' => env('DEPLOY_BRANCH'),

    // Exceptions to ignore completely
    'ignore_exceptions' => [
        // \Symfony\Component\Mailer\Exception\TransportException::class,
    ],

    // Exceptions to log without stack trace
    'ignore_stack_trace_for' => [
        // \App\Exceptions\CustomException::class,
    ],

    // Maximum message length (Telegram limit is 4096)
    'max_message_length' => 3040,

    // Include authenticated user info
    'include_user_info' => true,

    // HTTP timeout in seconds
    'timeout' => 5,

    // Minimum log level for TelegramTap
    'level' => env('TELEGRAM_LOG_LEVEL', 'error'),
];
```

## Log Message Format

Log messages are formatted in Markdown with the following information:

- **App URL** - Clickable link to your application
- **Environment** - Current environment (production, staging, etc.)
- **Log Level** - As a hashtag (#ERROR, #WARNING, etc.)
- **Branch** - Deploy branch (if configured)
- **User ID** - Authenticated user info (if enabled)
- **File** - File and line number where the exception occurred
- **Message** - The log message
- **Stack Trace** - Filtered to show only app-related traces

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
