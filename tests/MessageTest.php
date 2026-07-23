<?php

namespace DenoBY\TelegramLogger\Tests;

use DenoBY\TelegramLogger\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function test_it_starts_empty(): void
    {
        $message = new Message;

        $this->assertSame('', $message->getText());
        $this->assertFalse($message->hasMediaFiles());
        $this->assertSame([], $message->getMediaFiles());
    }

    public function test_it_appends_and_prepends_text(): void
    {
        $message = (new Message('body'))
            ->appendText('after')
            ->prependText('before');

        $this->assertSame("before\nbodyafter\n", $message->getText());
    }

    public function test_it_collects_photos_and_videos(): void
    {
        $message = (new Message)
            ->addPhoto('https://example.test/a.jpg')
            ->addVideo('https://example.test/b.mp4');

        $this->assertTrue($message->hasMediaFiles());
        $this->assertSame(
            ['https://example.test/a.jpg', 'https://example.test/b.mp4'],
            $message->getMediaFiles(),
        );
    }
}
