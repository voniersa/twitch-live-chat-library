<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

final class ServerAddress
{
    private function __construct(private readonly string $value)
    {
    }

    /**
     * @param string $value address to message server
     * @return ServerAddress value object
     */
    public static function fromString(string $value): ServerAddress
    {
        return new self($value);
    }

    /**
     * @return string address to message server
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
