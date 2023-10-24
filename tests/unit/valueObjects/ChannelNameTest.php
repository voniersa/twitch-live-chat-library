<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

use PHPUnit\Framework\TestCase;

class ChannelNameTest extends TestCase
{
    public function testCanCreateFromString(): void
    {
        $channelName = ChannelName::fromString("#testName");

        $this->assertInstanceOf(ChannelName::class, $channelName);
        $this->assertEquals("#testname", (string) $channelName);
    }

    public function testThrowsExceptionIfHashtagIsMissing(): void
    {
        $this->expectException(IncorrectDataProvidedException::class);
        $this->expectExceptionMessage("ChannelName has to start with #");
        ChannelName::fromString("testName");
    }
}
