<?php

namespace DenoBY\TelegramLogger;

use Illuminate\Log\Logger;

class Tap
{
    private ?int $threadId;

    private ?string $level;

    public function __construct(?string $threadId = null, ?string $level = null)
    {
        $this->threadId = ($threadId !== null && $threadId !== '') ? (int) $threadId : null;
        $this->level = ($level !== null && $level !== '') ? $level : null;
    }

    public function __invoke(Logger $logger): void
    {
        $logger->pushHandler(
            new LogHandler(
                logLevel: $this->level ?? config('telegram-logger.level', 'error'),
                threadId: $this->threadId,
            )
        );
    }
}
