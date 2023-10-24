<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

final class Message
{
    private function __construct(
        private readonly string $messageText,
        private readonly UserName $userName,
        private readonly ChannelName $channelName
    ) {
    }

    /**
     * @param string $messageText text that was sent to live chat
     * @param UserName $userName name of the chat user
     * @param ChannelName $channelName name of the channel where the message was sent
     * @return Message value object
     */
    public static function fromParams(
        string $messageText,
        UserName $userName,
        ChannelName $channelName
    ): Message {
        return new self($messageText, $userName, $channelName);
    }

    /**
     * @return string text that was sent to live chat
     */
    public function __toString(): string
    {
        return $this->messageText;
    }

    /**
     * @return UserName object of the chat user
     */
    public function getUserName(): UserName
    {
        return $this->userName;
    }

    /**
     * @return ChannelName object of the channel where the message was sent
     */
    public function getChannelName(): ChannelName
    {
        return $this->channelName;
    }
}
