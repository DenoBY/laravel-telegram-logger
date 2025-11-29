<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Token
    |--------------------------------------------------------------------------
    |
    | Your Telegram bot token from @BotFather
    |
    */
    'token' => env('TELEGRAM_LOG_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Chat ID
    |--------------------------------------------------------------------------
    |
    | The chat ID where logs will be sent. Can be a group, supergroup, or channel.
    |
    */
    'chat_id' => env('TELEGRAM_LOG_CHAT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Thread ID (optional)
    |--------------------------------------------------------------------------
    |
    | If you're using a supergroup with topics enabled, specify the topic ID here.
    |
    */
    'thread_id' => env('TELEGRAM_LOG_THREAD_ID'),

    /*
    |--------------------------------------------------------------------------
    | Application Info
    |--------------------------------------------------------------------------
    |
    | These values are used to format the log message.
    |
    */
    'app_name' => env('APP_NAME', 'Laravel'),
    'app_url' => env('APP_URL'),
    'environment' => env('APP_ENV', 'production'),
    'deploy_branch' => env('DEPLOY_BRANCH'),

    /*
    |--------------------------------------------------------------------------
    | Ignored Exceptions
    |--------------------------------------------------------------------------
    |
    | List of exception class names that should not be sent to Telegram.
    | Use fully qualified class names.
    |
    */
    'ignore_exceptions' => [
        // \Symfony\Component\Mailer\Exception\TransportException::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignored Stack Traces
    |--------------------------------------------------------------------------
    |
    | List of exception class names for which stack traces should not be included.
    | The exception message will still be sent, but without the full trace.
    |
    */
    'ignore_stack_trace_for' => [
        // \App\Exceptions\CustomException::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Limits
    |--------------------------------------------------------------------------
    |
    | Maximum length of the log message sent to Telegram.
    | Telegram has a limit of 4096 characters, but we use a lower value
    | to ensure the message fits with formatting.
    |
    */
    'max_message_length' => 3040,

    /*
    |--------------------------------------------------------------------------
    | Include User Info
    |--------------------------------------------------------------------------
    |
    | Whether to include authenticated user information in log messages.
    |
    */
    'include_user_info' => true,

    /*
    |--------------------------------------------------------------------------
    | Connection Timeout
    |--------------------------------------------------------------------------
    |
    | HTTP connection timeout in seconds for Telegram API requests.
    |
    */
    'timeout' => 5,

    /*
    |--------------------------------------------------------------------------
    | Log Level (for TelegramTap)
    |--------------------------------------------------------------------------
    |
    | Minimum log level for TelegramTap. Used when adding Telegram handler
    | to an existing log channel via the 'tap' configuration option.
    |
    */
    'level' => env('TELEGRAM_LOG_LEVEL', 'error'),
];
