<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

class TwitchChatAdapter
{
    /**
     * @param IrcConnector $ircConnector connector for the irc protocol
     * @param ConnectionVerifier $verifier verifier for the connection
     */
    public function __construct(
        private readonly IrcConnector $ircConnector,
        private readonly ConnectionVerifier $verifier
    ) {
    }

    /**
     * @param UserName $userName username of the chatbot user
     * @param Password $password oauth code of the chatbot user
     * @param ChannelCollection $channels a collection of all the channels where the chatbot is active
     * @return SocketConnection active connection
     * @throws IncorrectDataProvidedException
     * @throws SocketConnectionException
     */
    public function openConnection(
        UserName $userName,
        Password $password,
        ChannelCollection $channels
    ): SocketConnection {
        $socketConnection = $this->ircConnector->openConnection();

        $this->ircConnector->fwrite($socketConnection, sprintf("PASS %s\r\n", $password));
        $this->ircConnector->fwrite($socketConnection, sprintf("NICK %s\r\n", $userName));

        do {
            $read = $this->ircConnector->fgets($socketConnection);
        } while (!preg_match("/:\S+ 376 \S+ :.*/i", $read));

        $channelIterator = $channels->getIterator();
        while ($channelIterator->valid()) {
            $this->ircConnector->fwrite(
                $socketConnection,
                sprintf("JOIN %s\r\n", $channelIterator->current())
            );
            do {
                $read = $this->ircConnector->fgets($socketConnection);
            } while (!preg_match("/:(\S+)!\S+@\S+ JOIN (#\S+)/i", $read));
            $channelIterator->next();
        }

        return $socketConnection;
    }

    /**
     * @param SocketConnection $socketConnection active connection
     */
    public function closeConnection(SocketConnection $socketConnection): void
    {
        $this->ircConnector->closeConnection($socketConnection);
    }

    /**
     * @param SocketConnection $socketConnection active connection
     * @param ChannelName $channelName name of the channel where the message is posted
     * @param string $message message that is sent
     */
    public function writeMessage(SocketConnection $socketConnection, ChannelName $channelName, string $message): void
    {
        $this->ircConnector->fwrite(
            $socketConnection,
            sprintf("PRIVMSG %s :%s\r\n", $channelName, $message)
        );
    }

    /**
     * @param SocketConnection $socketConnection active connection
     * @param int $disconnect seconds until the reading process ends
     * @return MessageCollection collection of all the messages that were send during the reading process
     * @throws IncorrectDataProvidedException
     */
    public function readLiveChat(
        SocketConnection $socketConnection,
        int $disconnect
    ): MessageCollection {
        $messageCollection = MessageCollection::fromArray([]);
        $startTime = microtime(true);
        while ($this->verifier->verifyConnection($socketConnection->getResource())) {
            $read = $this->ircConnector->fgets($socketConnection);

            if (microTime(true) - $disconnect >= $startTime) {
                return $messageCollection;
            } else {
                if (preg_match("/:(\S+)!\S+@\S+ PRIVMSG (#\S+) :(.*)/i", $read, $match)) {
                    $message = Message::fromParams(
                        substr($match[3], 0, -1),
                        UserName::fromString($match[1]),
                        ChannelName::fromString($match[2]),
                    );
                    $messageCollection->add($message);
                    $socketConnection->notify($message);
                }

                if (preg_match("/PING :(.*)/i", $read, $match)) {
                    $this->ircConnector->fwrite($socketConnection, sprintf("PONG :%s\r\n", $match[1]));
                }
            }
        }
        return $messageCollection;
    }
}
