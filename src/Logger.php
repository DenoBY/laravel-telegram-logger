<?php

namespace DenoBY\TelegramLogger;

use Monolog\Logger as MonologLogger;

class Logger
{
    public function __invoke(array $config): MonologLogger
    {
        $handler = new LogHandler($config['level'] ?? 'debug');

        return new MonologLogger(config('telegram-logger.app_name', 'Laravel'), [$handler]);
    }
}
