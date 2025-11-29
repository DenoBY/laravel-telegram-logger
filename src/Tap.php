<?php

namespace DenoBY\TelegramLogger;

use Illuminate\Log\Logger;

class Tap
{
    public function __invoke(Logger $logger): void
    {
        $logger->pushHandler(
            new LogHandler(
                config('telegram-logger.level', 'error')
            )
        );
    }
}
