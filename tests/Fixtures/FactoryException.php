<?php

namespace DenoBY\TelegramLogger\Tests\Fixtures;

use RuntimeException;

/**
 * Exception with a named constructor: `new self` runs inside this file, so
 * getFile() points here rather than at the throw site.
 */
final class FactoryException extends RuntimeException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function make(string $message): self
    {
        return new self($message);
    }
}
