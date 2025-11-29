<?php

namespace DenoBY\TelegramLogger\Facades;

use DenoBY\TelegramLogger\Client;
use DenoBY\TelegramLogger\Message;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array sendMessageToChat(Message $message, string $chatId, ?int $threadId = null, mixed $replyMessageId = null, mixed $messageId = null, string $parseMode = 'HTML')
 * @method static array sendDocument(string $filePath, string $chatId, ?string $caption = null, ?int $threadId = null)
 *
 * @see \DenoBY\TelegramLogger\Client
 */
class Telegram extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Client::class;
    }
}
