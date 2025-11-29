<?php

namespace DenoBY\TelegramLogger;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/telegram-logger.php',
            'telegram-logger'
        );

        $this->app->singleton(Client::class, function ($app) {
            return new Client(
                config('telegram-logger.token')
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/telegram-logger.php' => config_path('telegram-logger.php'),
            ], 'telegram-logger-config');
        }
    }
}
