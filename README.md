# Twitch Live Chat Library
[![Unit tests](https://github.com/voniersa/twitch-live-chat-library/actions/workflows/unit-tests.yml/badge.svg?event=push)](https://github.com/voniersa/twitch-live-chat-library/actions/workflows/unit-tests.yml)

A simple and easy to use library for reading from and writing to the twitch irc live chats by partially using the observer design pattern.

With this library you can easily build your own twitch chatbot with PHP.

## Requirements
PHP Version 8.2 or higher

## How to use
You can install the library with composer:

```bash
composer require voniersa/twitch-live-chat
```

## Functionalities
Firstly you have to create a new [TwitchChatAdapter](src/TwitchChatAdapter.php):
```php
use voniersa\twitch\livechat\TwitchChatAdapter;
use voniersa\twitch\livechat\IrcConnector;
use voniersa\twitch\livechat\ConnectionVerifier;

$adapter = new TwitchChatAdapter(new IrcConnector(), new ConnectionVerifier());
```
With this Adapter you can open a connection to twitch live chats by executing the [openConnection()](src/TwitchChatAdapter.php#L27) function. This function needs the parameters
* [__UserName__](src/valueObjects/UserName.php) - the username of your account which you want to use as your chatbot
* [__Password__](src/valueObjects/Password.php) - the authentication code of your chatbot account. You can get your oauth code at https://twitchtokengenerator.com/
* [__ChannelCollection__](src/ChannelCollection.php) - a collection of all channels where your chatbot should be active. A channel always needs a __#__ in front of the channel name

You can open multiple connections with different users and channels.
```php
$connection = $adapter->openConnection(
    UserName::fromString("xxxxxx"),
    Password::fromString("oauth:xxxxxx"),
    ChannelCollection::fromArray([
        ChannelName::fromString("#xxxxxx"),
        ChannelName::fromString("#xxxxxx")
    ])
);
```
To this connection you can add as many observers as you want by executing the [attach()](src/SocketConnection.php#L43) function and later also the [detach()](src/SocketConnection.php#L51) function to remove an observer.
```php
$exampleObserver = new ExampleObserver();
$connection->attach($exampleObserver);

$connection->detach($exampleObserver);
```
You have to write the Observers by yourself. For that simply create a new class which implements the [__MessageObserver__](src/MessageObserver.php) interface. You need to add the Method update() with the parameter of type [__Message__](src/valueObjects/Message.php).
```php
use voniersa\twitch\livechat\MessageObserver;

class ExampleObserver implements MessageObserver
{
    public function update(Message $message): void
    {
        // do here what ever you want ...
    }
}
```
This update() method gets called everytime when a message is written to the live chats. Here you can define what should happen if a message appears.

To actually start reading the chat simply call the function [readLiveChat()](src/TwitchChatAdapter.php#L83) of the Adapter. You need to provide the connection and a time after how many seconds the reading process should stop. The function returns a [__MessageCollection__](src/MessageCollection.php) which contains all messages that are written to the chat in that time period:
```php
$adapter->readLiveChat($connection, 30);
```
With this library you cannot only read from specific live chats. You can also write something to the chat by simply call the [writeMessage()](src/TwitchChatAdapter.php#L69) function.
```php
$adapter->writeMessage($connection, ChannelName::fromString("#xxxxxx"), "This message is send to the live chat!");
```

## Example
In the [demo.php](example/demo.php) you can find a simple code snippet, how you can use this library.

To run this example script, execute the following command:
```
make demo username="xxxxxx" password="oauth:xxxxxx" channel="#xxxxxx"
```

## License
This library is released under the MIT Licence. See the bundled [LICENSE file](LICENSE) for details.

## Author
Sascha Vonier ([@voniersa](https://github.com/voniersa))
