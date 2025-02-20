[Documentation](../Index.md) / [Middleware](../Middleware.md) / PingInterval

# Websocket: PingInterval middleware

This middlewares is included in library and can be added to provide additional functionality.

* Sends Ping messages on Connection at interval, typically used as "heartbeat" to keep connection open
* If `interval` is not specified, it will use connection timeout configuration as interval

Note that interval typically need to be set to a lower value than core timeout configuration.
This is because runner may be blocked up to timeout seconds, preventing PingInterval from running until timeout has passed.

```php
$client_or_server->addMiddleware(new WebSocket\Middleware\PingInterval(interval: 10));
```
