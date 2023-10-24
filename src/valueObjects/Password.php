<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

final class Password
{
    private const METHOD = "AES-128-CTR";
    private string $iv;
    private string $value;

    private function __construct(string $value)
    {
        $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::METHOD));
        $this->value = openssl_encrypt($value, self::METHOD, "twitch-live-chat", 0, $this->iv);
    }

    /**
     * @param string $value oauth password of the chat user
     * @return Password value object
     * @throws IncorrectDataProvidedException
     */
    public static function fromString(string $value): Password
    {
        if (!str_starts_with($value, "oauth:")) {
            throw new IncorrectDataProvidedException("Password needs to start with \"oauth:\".");
        }
        return new self($value);
    }

    /**
     * @return string oauth password of the chat user
     */
    public function __toString(): string
    {
        return openssl_decrypt($this->value, "AES-128-CTR", "twitch-live-chat", 0, $this->iv);
    }
}
