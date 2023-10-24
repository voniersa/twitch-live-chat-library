<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

class TestObserver implements MessageObserver
{
    public function update(Message $message): void
    {
        echo sprintf(
            "This is only a test: %s,%s,%s",
            (string) $message,
            (string) $message->getChannelName(),
            (string) $message->getUserName()
        );
    }
}
