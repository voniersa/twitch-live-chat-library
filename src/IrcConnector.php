<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

/** @codeCoverageIgnore */
class IrcConnector
{
    private ServerAddress $serverAddress;
    private Port $port;

    public function __construct()
    {
        $this->serverAddress = ServerAddress::fromString('irc.twitch.tv');
        $this->port = Port::fromInt(6667);
    }

    /**
     * @return SocketConnection active connection
     * @throws SocketConnectionException
     * @throws IncorrectDataProvidedException
     */
    public function openConnection(): SocketConnection
    {
        $socketConnection = fsockopen(
            (string) $this->serverAddress,
            $this->port->toInt(),
            $errorCode,
            $errorMessage
        );

        if (!$socketConnection) {
            throw new SocketConnectionException("Error: $errorCode - $errorMessage");
        }

        return SocketConnection::fromResource($socketConnection, new ConnectionVerifier());
    }

    /**
     * @param SocketConnection $socketConnection active connection
     * @param string $data data that is written to the connection
     * @return int number of bytes that are written
     */
    public function fwrite(SocketConnection $socketConnection, string $data): int
    {
        do {
            $bytes = fwrite($socketConnection->getResource(), $data);
        } while (!$bytes);
        return $bytes;
    }

    /**
     * @param SocketConnection $socketConnection active connection
     * @return string value that is written
     */
    public function fgets(SocketConnection $socketConnection): string
    {
        $value = fgets($socketConnection->getResource());
        if (!$value) {
            return "";
        }
        return $value;
    }

    /**
     * @param SocketConnection $socketConnection active connection
     */
    public function closeConnection(SocketConnection $socketConnection): void
    {
        if (get_resource_type($socketConnection->getResource()) === "stream") {
            fclose($socketConnection->getResource());
            unset($socketConnection);
        }
    }
}
