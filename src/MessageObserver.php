<?php

declare(strict_types=1);

namespace voniersa\twitch\livechat;

interface MessageObserver
{
    /**
     * @param Message $message message that was sent
     */
    public function update(Message $message): void;
}
