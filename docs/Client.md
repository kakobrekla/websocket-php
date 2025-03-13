[Documentation](Index.md) / Client

# Websocket: Client

The client can read and write on a WebSocket stream.

## Basic operation

Set up a WebSocket client for request/response strategy.

```php
$client = new WebSocket\Client("wss://echo.websocket.org/");
$client
    // Add standard middlewares
    ->addMiddleware(new WebSocket\Middleware\CloseHandler())
    ->addMiddleware(new WebSocket\Middleware\PingResponder())
    ;

// Send a message
$client->text("Hello WebSocket.org!");

// Read response (this is blocking)
$message = $client->receive();
echo "Got message: {$message->getContent()} \n";

// Close connection
$client->close();
```

## Subscribe operation

If you want to subscribe to messages sent by server at any point, use the listener functions.

```php
$client = new WebSocket\Client("wss://echo.websocket.org/");
$client
    // Add standard middlewares
    ->addMiddleware(new WebSocket\Middleware\CloseHandler())
    ->addMiddleware(new WebSocket\Middleware\PingResponder())
    // Listen to incoming Text messages
    ->onText(function (WebSocket\Client $client, WebSocket\Connection $connection, WebSocket\Message\Message $message) {
        // Act on incoming message
        echo "Got message: {$message->getContent()} \n";
        // Possibly respond to server
        $client->text("I got your your message");
    })
    ->start();
```

## Middlewares

Middlewares provide additional functionality when sending or receiving messages.
This repo comes with two middlewares that provide standard operability according to WebSocket protocol.

* `CloseHandler` - Automatically acts on incoming and outgoing Close requests, as specified in WebSocket protocol
* `PingResponder` - Responds with Pong message when receiving a Ping message, as specified in WebSocket protocol

If not added, you need to handle close operation and respond to ping requests in your own implementation.

```php
$client = new WebSocket\Client("wss://echo.websocket.org/");
$client
    // Add CloseHandler middleware
    ->addMiddleware(new WebSocket\Middleware\CloseHandler())
    // Add PingResponder middleware
    ->addMiddleware(new WebSocket\Middleware\PingResponder())
    ;
```

Read more on [Middlewares](Middleware.md).

## Listeners

The message listeners are used by specifying a callback function that will be called
whenever the server receives a method of the same type.
All message listeners receive Client, Connection and Message as arguments.

```php
$client = new WebSocket\Client("wss://echo.websocket.org/");
$client
    // Listen to incoming Text messages
    ->onText(function (WebSocket\Client $client, WebSocket\Connection $connection, WebSocket\Message\Text $message) {
        // Act on incoming message
    })
    // Listen to incoming Binary messages
    ->onBinary(function (WebSocket\Client $client, WebSocket\Connection $connection, WebSocket\Message\Binary $message) {
        // Act on incoming message
    })
    ->start();
    ;
$client->isRunning(); // => True if currently running
```

Read more on [Listeners](Listener.md).

## Messages

WebSocket messages comes as any of five types; Text, Binary, Ping, Pong and Close.
The type is defined as opcode in WebSocket standard, and each classname corresponds to current message opcode.

Text and Binary are the main content message. The others are used for internal communication and typically do not contain content.
All provide the same methods, except Close that have an additional method not present on other types of messages.

```php
echo "opcode:       {$message->getOpcode()}\n";
echo "length:       {$message->getLength()}\n";
echo "timestamp:    {$message->getTimestamp()}\n";
echo "content:      {$message->getContent()}\n";
echo "close status: {$close->getCloseStatus()}\n";
```

Read more on [Messages](Message.md).

## Sending messages

To send a message to a server, call the send() method with a Message instance.
Any of the five message types can be sent this way.

```php
$client->send(new WebSocket\Message\Text("Server sends a message"));
$client->send(new WebSocket\Message\Binary($binary));
$client->send(new WebSocket\Message\Ping("My ping"));
$client->send(new WebSocket\Message\Text("My pong"));
$client->send(new WebSocket\Message\Close(1000, "Closing now"));
```
There are also convenience methods available for all types.
```php
$client->text("Server sends a message");
$client->binary($binary);
$client->ping("My ping");
$client->pong("My pong");
$client->close(1000, "Closing now");
```

## Configuration

The Client takes one argument: [URI](http://tools.ietf.org/html/rfc3986) as a class implementing [UriInterface](https://www.php-fig.org/psr/psr-7/#35-psrhttpmessageuriinterface) or as string.
The client support `ws` (`tcp`) and `wss` (`ssl`) schemas, depending on SSL configuration.
Other options are available runtime by calling configuration methods.

### Logger

Client support adding any [PSR-4 compatible](https://www.php-fig.org/psr/psr-3/) logger.

```php
$client->setLogger(Psr\Log\LoggerInterface $logger);
```

### Timeout

Timeout for various operations can be specified in seconds.
This affects how long Client will wait for connection, read and write operations, and listener scope.
Default is `60` seconds. Minimum is `0` seconds.
Avoid setting very low values as it will cause a read loop to use all
available processing power even when there's nothing to read.

```php
$client->setTimeout(300); // set timeout in seconds
$client->getTimeout(); // => current timeout in seconds
```

### Frame size

Defines the maximum payload per frame size in bytes.
Default is `4096` bytes. Minimum is `1` byte.
Do not change unless you have a strong reason to do so.

```php
$client->setFrameSize(1024); // set maximum payload frame size in bytes
$client->getFrameSize(); // => current maximum payload frame size in bytes
```

### Persistent connection

If set to true, the underlying connection will be kept open if possible.
This means that if Client closes and is then restarted, it may use the same connection.
Do not change unless you have a strong reason to do so.

```php
$client->setPersistent(true);
```

### Context

Client support adding [context options and parameters](https://www.php.net/manual/en/context.php)
using the [Phrity\Net\Context](https://www.php.net/manual/en/context.php) class.

```php
$context = new Phrity\Net\Context();
$context->setOptions([
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
    ],
]);
$client->setContext($context); // set context
$client->getContext(); // => currently used Phrity\Net\Context
```

### Handshake headers

Extra HTTP headers can be added, and used during handshake.

```php
$client->addHeader("Sec-WebSocket-Protocol", "soap");
```

## Connection control

Client will automatically connect when sending a message or starting the listner.
You may also connect and disconnect manually.

```php
if (!$client->isConnected()) {
    $client->connect();
}
$client->disconnect();
```

When connected, there are additional info to retrieve.

```php
// View client name
echo "local:    {$client->getName()}\n";

// View server name
echo "remote:   {$client->getRemoteName()}\n";

// Get meta data by key
echo "meta:   {$client->getMeta('some-metadata')}\n";

// Get response on handshake
$response = $client->getHandshakeResponse();
```

Read more on [Connection](Connection.md).

