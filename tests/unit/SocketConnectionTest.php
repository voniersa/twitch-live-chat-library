<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

use PHPUnit\Framework\TestCase;

class SocketConnectionTest extends TestCase
{
    public function testCanGetResource(): void
    {
        $resource = "test";
        $verifierMock = $this->createMock(ConnectionVerifier::class);
        $verifierMock->expects($this->once())->method("verifyConnection")->with($resource)
            ->willReturn(true);

        $socketConnection = SocketConnection::fromResource($resource, $verifierMock);
        $this->assertEquals($resource, $socketConnection->getResource());
    }

    public function testCanObserve(): void
    {
        $resource = "test";
        $verifierMock = $this->createMock(ConnectionVerifier::class);
        $verifierMock->expects($this->once())->method("verifyConnection")->with($resource)
            ->willReturn(true);

        $observer = new TestObserver();
        $message = Message::fromParams(
            "test",
            UserName::fromString("user"),
            ChannelName::fromString("#chan")
        );
        $socketConnection = SocketConnection::fromResource($resource, $verifierMock);
        $socketConnection->attach($observer);
        $socketConnection->notify($message);
        $socketConnection->detach($observer);
        $socketConnection->notify($message);
        $this->expectOutputString("This is only a test: test,#chan,user");
    }

    public function testInvalidConnectionThrowsException(): void
    {
        $resource = "test";
        $verifierMock = $this->createMock(ConnectionVerifier::class);
        $verifierMock->expects($this->once())->method("verifyConnection")->with($resource)
            ->willReturn(false);

        $this->expectException(IncorrectDataProvidedException::class);
        $this->expectExceptionMessage("Resource of type stream was expected.");

        SocketConnection::fromResource($resource, $verifierMock);
    }
}
