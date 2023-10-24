<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

use ArrayIterator;

class MessageCollection
{
    private array $collection;

    /**
     * @throws IncorrectDataProvidedException
     */
    private function __construct(array $messages)
    {
        foreach ($messages as $message) {
            if (!($message instanceof Message)) {
                throw new IncorrectDataProvidedException("Instance of Message was expected.");
            }
            $this->add($message);
        }
    }

    /**
     * @param array $messages array of messages that were sent
     * @return MessageCollection collection of messages that were sent
     * @throws IncorrectDataProvidedException
     */
    public static function fromArray(array $messages): MessageCollection
    {
        return new self($messages);
    }

    /**
     * @return ArrayIterator iterator for all sent messages
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->collection);
    }

    /**
     * @return int number of messages in the collection
     */
    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * @param Message $Message message that was sent
     */
    public function add(Message $Message): void
    {
        $this->collection[] = $Message;
    }
}
