[Documentation](../Index.md) / [Middleware](../Middleware.md) / CompressionExtension

# Websocket: CompressionExtension middleware

This middlewares is included in library and can be added to provide additional functionality.

Add support for message compression.
Each supported compression method is added as argument.
Client will request compression method according to order added.
Server will then respond with the first compression method it can match.
If Client and Server can not agree on a compression method, messages will not be compressed.

## DeflateCompressor compression method

The [permessage-deflate](https://datatracker.ietf.org/doc/html/rfc7692#section-7) compression extension is available in library.
Add it as argument to the CompressionExtension middleware to deflate compression support.

```php
$client_or_server->addMiddleware(new WebSocket\Middleware\CompressionExtension(
    new WebSocket\Middleware\CompressionExtension\DeflateCompressor()
));
```

### Configuration

The compressor support four optional arguments specifying compression options.

```php
$deflateCompressor = new WebSocket\Middleware\CompressionExtension\DeflateCompressor(
    serverNoContextTakeover: false, // bool - Server disables context takeover
    clientNoContextTakeover: false, // bool - Client disables context takeover
    serverMaxWindowBits: 15, // int<8, 15> - Maximum bits for LZ77 sliding window used by Server
    clientMaxWindowBits: 15, // int<8, 15> - Maximum bits for LZ77 sliding window used by Client
);
```

### Priority

It is possible to add multiple DeflateCompressor instances with different configuration.
Client will request compression method according to order added.
Server will then respond with the first compression method it can match.

```php
$client->addMiddleware(new WebSocket\Middleware\CompressionExtension(
    new WebSocket\Middleware\CompressionExtension\DeflateCompressor(
        serverNoContextTakeover: true,
        clientNoContextTakeover: true,
    ),
    new WebSocket\Middleware\CompressionExtension\DeflateCompressor(),
));
```

In the example, Client tells Server it prefers that context takeover is disabled.
But if Server do not support this configuration, it will fallback to default settings.
