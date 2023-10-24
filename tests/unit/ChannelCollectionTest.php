<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

use ArrayIterator;
use PHPUnit\Framework\TestCase;

class ChannelCollectionTest extends TestCase
{
    private ChannelCollection $collection;

    public function setUp(): void
    {
        $this->collection = ChannelCollection::fromArray([
            ChannelName::fromString("#channelName"),
        ]);
    }

    public function testFirstSuggestion(): void
    {
        $iterator = $this->collection->getIterator();

        $this->assertEquals(1, $this->collection->count());
        $this->assertInstanceOf(ArrayIterator::class, $iterator);
        $this->assertInstanceOf(ChannelName::class, $iterator->current());
        $this->assertEquals("#channelname", (string) $iterator->current());
    }

    /**
     * @depends testFirstSuggestion
     */
    public function testSecondSuggestion(): void
    {
        $this->collection->add(ChannelName::fromString("#testName"));

        $iterator = $this->collection->getIterator();
        $iterator->next();

        $this->assertInstanceOf(ChannelName::class, $iterator->current());
        $this->assertEquals("#testname", (string) $iterator->current());
        $this->assertEquals(2, $this->collection->count());
    }

    public function testInvalidObjectGeneratesException(): void
    {
        $this->expectException(IncorrectDataProvidedException::class);
        $this->expectExceptionMessage("Instance of ChannelName was expected.");

        ChannelCollection::fromArray([
            UserName::fromString("user"),
        ]);
    }
}
