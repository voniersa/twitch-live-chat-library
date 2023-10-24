<?php

declare(strict_types=1);

use voniersa\twitch\livechat\ChannelCollection;
use voniersa\twitch\livechat\ChannelName;
use voniersa\twitch\livechat\ConnectionVerifier;
use voniersa\twitch\livechat\IncorrectDataProvidedException;
use voniersa\twitch\livechat\IrcConnector;
use voniersa\twitch\livechat\Message;
use voniersa\twitch\livechat\MessageObserver;
use voniersa\twitch\livechat\Password;
use voniersa\twitch\livechat\SocketConnectionException;
use voniersa\twitch\livechat\TwitchChatAdapter;
use voniersa\twitch\livechat\UserName;

require "./vendor/autoload.php";

class ExampleObserver implements MessageObserver
{
    public function update(Message $message): void
    {
        echo sprintf(
            "%s:%s: \"%s\"\n",
            $message->getChannelName(),
            $message->getUserName(),
            $message
        );
    }
}

$adapter = new TwitchChatAdapter(new IrcConnector(), new ConnectionVerifier());

try {
    $connection = $adapter->openConnection(
        UserName::fromString($argv[1]),
        Password::fromString($argv[2]),
        ChannelCollection::fromArray([ChannelName::fromString($argv[3])])
    );
    $connection->attach(new ExampleObserver());

    $adapter->writeMessage($connection, ChannelName::fromString($argv[3]), "Start");

    $messages = $adapter->readLiveChat($connection, 30);
    $messageIterator = $messages->getIterator();

    echo PHP_EOL;
    echo "These are all messages sent in the last 30 seconds:";
    echo PHP_EOL;

    while ($messageIterator->valid()) {
        $messageIterator->current();
        echo sprintf(
            "%s:%s: \"%s\"\n",
            $messageIterator->current()->getChannelName(),
            $messageIterator->current()->getUserName(),
            $messageIterator->current()
        );
        $messageIterator->next();
    }

    $adapter->writeMessage($connection, ChannelName::fromString($argv[3]), "End");

    $adapter->closeConnection($connection);
} catch (IncorrectDataProvidedException|SocketConnectionException $e) {
    echo $e->getMessage();
}
