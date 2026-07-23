<?php

namespace DenoBY\TelegramLogger\Tests\Fixtures;

use RuntimeException;

/**
 * Ordinary exception: thrown via `throw new` from consumer code, so getFile()
 * already points at the throw site.
 */
final class PlainException extends RuntimeException {}
