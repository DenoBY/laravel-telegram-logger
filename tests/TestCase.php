<?php

namespace DenoBY\TelegramLogger\Tests;

use DenoBY\TelegramLogger\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [ServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('telegram-logger.token', 'test-token');
        $app['config']->set('telegram-logger.chat_id', '123456');
        $app['config']->set('telegram-logger.app_url', 'https://example.test');
        $app['config']->set('telegram-logger.environment', 'testing');
        $app['config']->set('telegram-logger.include_user_info', false);
    }
}
