<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

use ArrayIterator;
use PHPUnit\Framework\TestCase;

class MessageCollectionTest extends TestCase
{
    private MessageCollection $collection;

    public function setUp(): void
    {
        $this->collection = MessageCollection::fromArray([Message::fromParams(
            "first message",
            UserName::fromString("userName"),
            ChannelName::fromString("#channelName")
        )]);
    }

    public function testFirstSuggestion(): void
    {
        $iterator = $this->collection->getIterator();

        $this->assertEquals(1, $this->collection->count());
        $this->assertInstanceOf(ArrayIterator::class, $iterator);
        $this->assertInstanceOf(Message::class, $iterator->current());
        $this->assertEquals("first message", (string) $iterator->current());
    }

    /**
     * @depends testFirstSuggestion
     */
    public function testSecondSuggestion(): void
    {
        $this->collection->add(Message::fromParams(
            "second message",
            UserName::fromString("user2"),
            ChannelName::fromString("#channel2")
        ));

        $iterator = $this->collection->getIterator();
        $iterator->next();

        $this->assertInstanceOf(Message::class, $iterator->current());
        $this->assertEquals("second message", (string) $iterator->current());
        $this->assertEquals(2, $this->collection->count());
    }

    public function testInvalidObjectGeneratesException(): void
    {
        $this->expectException(IncorrectDataProvidedException::class);
        $this->expectExceptionMessage("Instance of Message was expected.");

        MessageCollection::fromArray([
            UserName::fromString("user"),
        ]);
    }
}
