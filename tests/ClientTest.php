<?php

namespace DenoBY\TelegramLogger\Tests;

use DenoBY\TelegramLogger\Client;
use DenoBY\TelegramLogger\Message;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class ClientTest extends TestCase
{
    public function test_it_sends_a_plain_text_message(): void
    {
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);

        (new Client('bot-token'))->sendMessageToChat(
            message: new Message('hello'),
            chatId: '123',
            threadId: 7,
            parseMode: 'Markdown',
        );

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://api.telegram.org/botbot-token/sendMessage'
                && $request['text'] === 'hello'
                && $request['parse_mode'] === 'Markdown'
                && $request['chat_id'] === '123'
                && $request['message_thread_id'] === 7;
        });
    }

    public function test_it_sends_media_as_a_group(): void
    {
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);

        (new Client('bot-token'))->sendMessageToChat(
            message: (new Message('caption'))->addPhoto('https://example.test/a.jpg'),
            chatId: '123',
        );

        Http::assertSent(function (Request $request) {
            return str_ends_with($request->url(), '/sendMediaGroup')
                && $request['media'][0]['media'] === 'https://example.test/a.jpg'
                && $request['media'][0]['caption'] === 'caption';
        });
    }

    public function test_it_returns_empty_array_on_connection_failure(): void
    {
        Http::fake(fn () => throw new ConnectionException('down'));

        $result = (new Client('bot-token'))->sendMessageToChat(
            message: new Message('hello'),
            chatId: '123',
        );

        $this->assertSame([], $result);
    }
}
