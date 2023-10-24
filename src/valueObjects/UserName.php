<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

final class UserName
{
    private function __construct(private readonly string $value)
    {
    }

    /**
     * @param string $value name of the chat user
     * @return UserName value object
     */
    public static function fromString(string $value): UserName
    {
        return new self(strtolower($value));
    }

    /**
     * @return string name of the chat user
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
