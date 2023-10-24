<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

final class ChannelName
{
    private function __construct(private readonly string $value)
    {
    }

    /**
     * @param string $value name of the channel
     * @return ChannelName value object
     * @throws IncorrectDataProvidedException
     */
    public static function fromString(string $value): ChannelName
    {
        if (!str_starts_with($value, '#')) {
            throw new IncorrectDataProvidedException('ChannelName has to start with #');
        }
        return new self(strtolower($value));
    }

    /**
     * @return string name of the channel
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
