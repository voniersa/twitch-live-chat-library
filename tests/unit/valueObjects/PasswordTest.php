<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public function testCanCreateFromString(): void
    {
        $password = Password::fromString("oauth:password");

        $this->assertInstanceOf(Password::class, $password);
        $this->assertEquals("oauth:password", (string) $password);
    }

    public function testInvalidPasswordThrowsException(): void
    {
        $this->expectException(IncorrectDataProvidedException::class);
        $this->expectExceptionMessage("Password needs to start with \"oauth:\".");

        Password::fromString("password");
    }
}
