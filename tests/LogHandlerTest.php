<?php

namespace DenoBY\TelegramLogger\Tests;

use DateTimeImmutable;
use DenoBY\TelegramLogger\LogHandler;
use DenoBY\TelegramLogger\Tests\Fixtures\PlainException;
use DenoBY\TelegramLogger\Tests\Fixtures\Thrower;
use Illuminate\Support\Facades\Http;
use Monolog\Level;
use Monolog\LogRecord;
use ReflectionMethod;

class LogHandlerTest extends TestCase
{
    private function resolveOrigin(\Throwable $exception): string
    {
        $method = new ReflectionMethod(LogHandler::class, 'resolveOrigin');

        return $method->invoke(new LogHandler('error'), $exception);
    }

    public function test_named_constructor_reports_the_throw_site_not_the_exception_file(): void
    {
        $exception = Thrower::viaFactory();

        $origin = $this->resolveOrigin($exception);

        $this->assertStringContainsString('Thrower.php', $origin);
        $this->assertStringNotContainsString('FactoryException.php', $origin);
    }

    public function test_plain_throw_new_is_left_untouched(): void
    {
        $exception = Thrower::viaThrowNew();

        $origin = $this->resolveOrigin($exception);

        // For a plain throw new getFile() is already accurate — behaviour is unchanged.
        $this->assertSame($exception->getFile().':'.$exception->getLine(), $origin);
        $this->assertStringContainsString('Thrower.php', $origin);
    }

    public function test_it_sends_a_formatted_message_with_the_throw_site(): void
    {
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);

        $this->handle('Database is down', ['exception' => Thrower::viaFactory()]);

        Http::assertSent(function ($request) {
            $text = $request['text'];

            return str_contains($text, '*Message:*')
                && str_contains($text, 'Database is down')
                && str_contains($text, 'Thrower.php')
                && ! str_contains($text, 'FactoryException.php');
        });
    }

    public function test_it_sends_nothing_when_token_is_missing(): void
    {
        config()->set('telegram-logger.token', null);
        Http::fake();

        $this->handle('anything', ['exception' => new PlainException('x')]);

        Http::assertNothingSent();
    }

    public function test_it_skips_exceptions_listed_in_ignore_exceptions(): void
    {
        config()->set('telegram-logger.ignore_exceptions', [PlainException::class]);
        Http::fake();

        $this->handle('ignored', ['exception' => new PlainException('x')]);

        Http::assertNothingSent();
    }

    private function handle(string $message, array $context): void
    {
        (new LogHandler('error'))->handle(new LogRecord(
            new DateTimeImmutable('2026-01-01 00:00:00'),
            'testing',
            Level::Error,
            $message,
            $context,
        ));
    }
}
