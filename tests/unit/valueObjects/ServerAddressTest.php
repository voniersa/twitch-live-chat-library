<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

use PHPUnit\Framework\TestCase;

class ServerAddressTest extends TestCase
{
    public function testCanCreateFromString(): void
    {
        $serverAddress = ServerAddress::fromString("ServerAddress");

        $this->assertInstanceOf(ServerAddress::class, $serverAddress);
        $this->assertEquals("ServerAddress", (string) $serverAddress);
    }
}
