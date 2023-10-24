<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testCanCreateFromString(): void
    {
        $message = Message::fromParams(
            "test message",
            UserName::fromString("user"),
            ChannelName::fromString("#channel")
        );

        $this->assertInstanceOf(Message::class, $message);
        $this->assertInstanceOf(UserName::class, $message->getUserName());
        $this->assertInstanceOf(ChannelName::class, $message->getChannelName());
        $this->assertEquals("test message", (string) $message);
        $this->assertEquals("user", (string) $message->getUserName());
        $this->assertEquals("#channel", (string) $message->getChannelName());
    }
}
