<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

final class Port
{
    private function __construct(private readonly int $value)
    {
    }

    /**
     * @param int $value port number
     * @return Port value object
     */
    public static function fromInt(int $value): Port
    {
        return new self($value);
    }

    /**
     * @return int port number
     */
    public function toInt(): int
    {
        return $this->value;
    }
}
