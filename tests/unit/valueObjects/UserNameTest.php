<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

use PHPUnit\Framework\TestCase;

class UserNameTest extends TestCase
{
    public function testCanCreateFromString(): void
    {
        $userName = UserName::fromString("testName");

        $this->assertInstanceOf(UserName::class, $userName);
        $this->assertEquals("testname", (string) $userName);
    }
}
