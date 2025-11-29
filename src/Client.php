<?php

namespace DenoBY\TelegramLogger;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class Client
{
    private string $baseUrl;

    public function __construct(
        private readonly string $token,
    ) {
        $this->baseUrl = 'https://api.telegram.org/bot'.$this->token.'/';
    }

    public function sendMessageToChat(
        Message $message,
        string $chatId,
        ?int $threadId = null,
        mixed $replyMessageId = null,
        mixed $messageId = null,
        string $parseMode = 'HTML'
    ): array {
        $sendMediaGroupBool = false;

        if (! $message->hasMediaFiles()) {
            $method = 'sendMessage';
            $data = [
                'text' => $message->getText(),
                'parse_mode' => $parseMode,
            ];
        } else {
            $method = 'sendMediaGroup';
            $data = [
                'media' => array_map(function ($media) use ($message, &$sendMediaGroupBool) {
                    $item = [
                        'type' => 'photo',
                        'media' => $media,
                    ];

                    if (! $sendMediaGroupBool) {
                        $item['caption'] = mb_substr(trim($message->getText()), 0, 1024);
                        $item['parse_mode'] = 'HTML';
                        $sendMediaGroupBool = true;
                    }

                    return $item;
                }, $message->getMediaFiles()),
            ];
        }

        $data['chat_id'] = $chatId;
        $data['disable_notification'] = false;
        $data['disable_web_page_preview'] = true;

        if ($threadId !== null) {
            $data['message_thread_id'] = $threadId;
        }

        if ($replyMessageId !== null) {
            $data['reply_to_message_id'] = $replyMessageId;
        }

        if ($messageId !== null) {
            $data['message_id'] = $messageId;
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(config('telegram-logger.timeout', 5))
                ->post($this->baseUrl.$method, $data);

            return $response->json() ?? [];
        } catch (ConnectionException) {
            return [];
        }
    }

    public function sendDocument(string $filePath, string $chatId, ?string $caption = null, ?int $threadId = null): array
    {
        try {
            $request = Http::withoutVerifying()
                ->timeout(config('telegram-logger.timeout', 5))
                ->attach('document', file_get_contents($filePath), basename($filePath));

            $data = ['chat_id' => $chatId];

            if ($caption !== null) {
                $data['caption'] = $caption;
            }

            if ($threadId !== null) {
                $data['message_thread_id'] = $threadId;
            }

            $response = $request->post($this->baseUrl.'sendDocument', $data);

            return $response->json() ?? [];
        } catch (ConnectionException) {
            return [];
        }
    }
}
