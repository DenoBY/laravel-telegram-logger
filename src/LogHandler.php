<?php

namespace DenoBY\TelegramLogger;

use Illuminate\Support\Str;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;

class LogHandler extends AbstractProcessingHandler
{
    private array $ignoreExceptions;

    private array $ignoreExceptionCallStacks;

    private string $applicationUrl;

    private string $applicationEnvironment;

    private int $maxMessageLength;

    public function __construct(int|string|Level $logLevel, bool $bubble = true)
    {
        $monologLevel = Logger::toMonologLevel($logLevel);

        parent::__construct($monologLevel, $bubble);

        $this->applicationUrl = config('telegram-logger.app_url', config('app.url', ''));
        $this->applicationEnvironment = config('telegram-logger.environment', config('app.env', 'production'));
        $this->ignoreExceptions = config('telegram-logger.ignore_exceptions', []);
        $this->ignoreExceptionCallStacks = config('telegram-logger.ignore_stack_trace_for', []);
        $this->maxMessageLength = config('telegram-logger.max_message_length', 3040);
    }

    protected function write(LogRecord $record): void
    {
        if ($this->ignoreExceptions($record)) {
            return;
        }

        (new Client(config('telegram-logger.token')))
            ->sendMessageToChat(
                message: new Message($this->formatLogText($record)),
                chatId: config('telegram-logger.chat_id'),
                threadId: config('telegram-logger.thread_id'),
                parseMode: 'Markdown',
            );
    }

    protected function formatLogText(LogRecord $record): string
    {
        [$file, $context] = $this->getContextAndFile($record);

        $logText = '*App URL:* ['.$this->applicationUrl.']('.$this->applicationUrl.')'.PHP_EOL;
        $logText .= '*Environment:* '.$this->applicationEnvironment.PHP_EOL;
        $logText .= '*Log Level:* #'.$record['level_name'].PHP_EOL;

        if (config('telegram-logger.deploy_branch')) {
            $logText .= '*Branch:* '.config('telegram-logger.deploy_branch').PHP_EOL;
        }

        if (config('telegram-logger.include_user_info', true) && function_exists('auth') && auth()->check()) {
            $logText .= '*User ID:* '.auth()->id().'('.auth()->user()::class.')'.PHP_EOL;
        }

        if ($file) {
            $logText .= '*File:* `'.$file.'`'.PHP_EOL;
        }

        $logText .= '*Message:* '.'``` '.str_replace('`', '', $record['message']).'```'.PHP_EOL.PHP_EOL;

        if ($context && ! $this->ignoreExceptionCallStack($record)) {
            if (! $this->isValidJson($context)) {
                preg_match_all('/#.*\/app\/.*/', $context, $matches);
                $context = implode("\n", $matches[0]);
            }

            if ($context) {
                $logText .= '```'.PHP_EOL.$context.'```'.PHP_EOL;
            }
        }

        return Str::limit($logText, $this->maxMessageLength, PHP_EOL.'```');
    }

    private function isValidJson(string $string): bool
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }

    protected function getContextAndFile(LogRecord $record): array
    {
        $file = null;
        $context = null;

        if (! empty($record->context)) {
            if (isset($record->context['exception'])) {
                $exception = $record->context['exception'];
                $context = $exception->getTraceAsString();
                $file = $exception->getFile().':'.$exception->getLine();
            } else {
                $context = json_encode($record['context']);
            }
        }

        return [$file, $context];
    }

    private function ignoreExceptions(LogRecord $record): bool
    {
        if (empty($record->context) || ! isset($record->context['exception'])) {
            return false;
        }

        return in_array(get_class($record->context['exception']), $this->ignoreExceptions);
    }

    private function ignoreExceptionCallStack(LogRecord $record): bool
    {
        if (empty($record->context) || ! isset($record->context['exception'])) {
            return false;
        }

        return in_array(get_class($record->context['exception']), $this->ignoreExceptionCallStacks);
    }
}
