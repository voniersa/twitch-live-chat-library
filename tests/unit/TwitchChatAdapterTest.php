<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

use PHPUnit\Framework\TestCase;

class TwitchChatAdapterTest extends TestCase
{
    private int $counter = 1;

    private function createAdapter(IrcConnector $ircConnector, bool $verifyConnection = true): TwitchChatAdapter
    {
        $verifierMock = $this->createMock(ConnectionVerifier::class);
        $verifierMock->method("verifyConnection")->willReturn($verifyConnection);
        return new TwitchChatAdapter($ircConnector, $verifierMock);
    }

    public function testCanOpenConnection(): void
    {
        $username = UserName::fromString("username");
        $password = Password::fromString("oauth:password");
        $channels = ChannelCollection::fromArray([ChannelName::fromString("#channel")]);
        $socketConnection = $this->createMock(SocketConnection::class);

        $ircConnector = $this->createMock(IrcConnector::class);
        $ircConnector->expects($this->once())->method("openConnection")
            ->willReturn($socketConnection);
        $ircConnector->expects($this->exactly(2))
            ->method("fgets")
            ->with($socketConnection)
            ->willReturn(
                ":tmi.twitch.tv 376 user :>\r\n",
                ":user!user@user.tmi.twitch.tv JOIN #channel\r\n"
            );
        $ircConnector->expects($this->exactly(3))->method("fwrite");

        $adapter = $this->createAdapter($ircConnector);
        $this->assertInstanceOf(SocketConnection::class, $adapter->openConnection(
            $username,
            $password,
            $channels
        ));
    }

    public function testCanCloseConnection(): void
    {
        $socketConnection = $this->createMock(SocketConnection::class);
        $ircConnector = $this->createMock(IrcConnector::class);
        $ircConnector->expects($this->once())->method("closeConnection")->with($socketConnection);

        $this->createAdapter($ircConnector)->closeConnection($socketConnection);
    }

    public function testCanWriteMessage(): void
    {
        $channel = ChannelName::fromString("#channel");
        $socketConnection = $this->createMock(SocketConnection::class);
        $ircConnector = $this->createMock(IrcConnector::class);
        $ircConnector->expects($this->once())->method("fwrite")
            ->with($socketConnection, "PRIVMSG #channel :test message\r\n");

        $this->createAdapter($ircConnector)->writeMessage($socketConnection, $channel, "test message");
    }

    public function testCanReadLiveChat(): void
    {
        $socketConnection = $this->createMock(SocketConnection::class);
        $socketConnection->expects($this->once())->method("notify");

        $ircConnector = $this->createMock(IrcConnector::class);
        $ircConnector->method("fgets")->willReturnCallback(function () {
            switch ($this->counter) {
                case 1:
                    $this->counter++;
                    return ":user!user@user.tmi.twitch.tv PRIVMSG #chan :test message\r\n";
                case 2:
                    $this->counter++;
                    return "PING :tmi.twitch.tv";
                default:
                    return "";
            }
        });
        $ircConnector->expects($this->once())->method("fwrite")
            ->with($socketConnection, "PONG :tmi.twitch.tv\r\n");

        $messageCollection = $this->createAdapter($ircConnector)->readLiveChat($socketConnection, 2);
        $iterator = $messageCollection->getIterator();
        $this->assertInstanceOf(MessageCollection::class, $messageCollection);
        $this->assertEquals("test message", (string) $iterator->current());
        $this->assertEquals("#chan", (string) $iterator->current()->getChannelName());
        $this->assertEquals("user", (string) $iterator->current()->getUserName());
    }

    public function testReadLiveChatCanReturnEmptyCollection(): void
    {
        $socketConnection = $this->createMock(SocketConnection::class);
        $ircConnector = $this->createMock(IrcConnector::class);
        $messageCollection = $this->createAdapter($ircConnector, false)->readLiveChat($socketConnection, 1);

        $this->assertEquals($messageCollection, MessageCollection::fromArray([]));
    }
}
