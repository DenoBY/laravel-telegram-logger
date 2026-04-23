<?php

namespace DenoBY\TelegramLogger;

use Illuminate\Log\Logger;

class Tap
{
    public function __invoke(Logger $logger, ?string $threadId = null, ?string $level = null): void
    {
        $parsedThreadId = ($threadId !== null && $threadId !== '') ? (int) $threadId : null;
        $parsedLevel = ($level !== null && $level !== '') ? $level : null;

        $logger->pushHandler(
            new LogHandler(
                logLevel: $parsedLevel ?? config('telegram-logger.level', 'error'),
                threadId: $parsedThreadId,
            )
        );
    }
}
