<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

use SplObjectStorage;

class SocketConnection
{
    private SplObjectStorage $observers;

    private function __construct(private readonly mixed $resource)
    {
        $this->observers = new SplObjectStorage();
    }

    /**
     * @param mixed $resource stream resource
     * @param ConnectionVerifier $verifier verifier for the connection
     * @return SocketConnection active connection
     * @throws IncorrectDataProvidedException
     */
    public static function fromResource(mixed $resource, ConnectionVerifier $verifier): SocketConnection
    {
        if (!$verifier->verifyConnection($resource)) {
            throw new IncorrectDataProvidedException("Resource of type stream was expected.");
        }
        return new self($resource);
    }

    /**
     * @return mixed stream resource
     */
    public function getResource(): mixed
    {
        return $this->resource;
    }

    /**
     * @param MessageObserver $observer message observer that reacts to incoming messages
     */
    public function attach(MessageObserver $observer): void
    {
        $this->observers->attach($observer);
    }

    /**
     * @param MessageObserver $observer message observer that reacts to incoming messages
     */
    public function detach(MessageObserver $observer): void
    {
        $this->observers->detach($observer);
    }

    /**
     * @param Message $message message that was sent
     */
    public function notify(Message $message): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($message);
        }
    }
}
