<?php

namespace DenoBY\TelegramLogger;

use Monolog\Logger as MonologLogger;

class Logger
{
    public function __invoke(array $config): MonologLogger
    {
        $handler = new LogHandler(
            logLevel: $config['level'] ?? 'debug',
            threadId: isset($config['thread_id']) ? (int) $config['thread_id'] : null,
        );

        return new MonologLogger(config('telegram-logger.app_name', 'Laravel'), [$handler]);
    }
}
