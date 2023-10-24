<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

use ArrayIterator;

class ChannelCollection
{
    private array $collection;

    /**
     * @throws IncorrectDataProvidedException
     */
    private function __construct(array $channels)
    {
        foreach ($channels as $channel) {
            if (!($channel instanceof ChannelName)) {
                throw new IncorrectDataProvidedException("Instance of ChannelName was expected.");
            }
            $this->add($channel);
        }
    }

    /**
     * @param array $channels list of channels where the chatbot is active
     * @return ChannelCollection collection of channels where the chatbot is active
     * @throws IncorrectDataProvidedException
     */
    public static function fromArray(array $channels): ChannelCollection
    {
        return new self($channels);
    }

    /**
     * @return ArrayIterator iterator for all channels
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->collection);
    }

    /**
     * @return int number of channels in collection
     */
    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * @param ChannelName $channel channel that needs to be added to the collection
     */
    public function add(ChannelName $channel): void
    {
        $this->collection[] = $channel;
    }
}
