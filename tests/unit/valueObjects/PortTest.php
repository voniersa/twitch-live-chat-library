<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

use PHPUnit\Framework\TestCase;

class PortTest extends TestCase
{
    public function testCanCreateFromInt(): void
    {
        $port = Port::fromInt(9876);

        $this->assertInstanceOf(Port::class, $port);
        $this->assertEquals(9876, $port->toInt());
    }
}
