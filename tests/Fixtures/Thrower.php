<?php

namespace DenoBY\TelegramLogger\Tests\Fixtures;

/**
 * A separate file standing in for consumer code: its path is what should land
 * in `File`, not the exception's own file.
 */
class Thrower
{
    public static function viaFactory(): FactoryException
    {
        return FactoryException::make('boom');
    }

    public static function viaThrowNew(): PlainException
    {
        return new PlainException('boom');
    }
}
