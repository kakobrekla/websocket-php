# WebSocket\Client

```php
// Magic methods
public function __construct(Psr\Http\Message\UriInterface|string $uri);
public function __toString(): string

// Configuration
public function setStreamFactory(StreamFactory $streamFactory): self;
public function setLogger(LoggerInterface $logger): void;
public function setTimeout(int $timeout): self;
public function getTimeout(): int;
public function setFrameSize(int $frameSize): self;
public function getFrameSize(): int;
public function setPersistent(bool $persistent): self;
public function setContext(array $context): self;
public function addHeader(string $name, string $content): self;
public function addMiddleware(MiddlewareInterface $middleware): self;
```

# WebSocket\Connection

```php
// @todo
```

# WebSocket\Server

```php
// @todo
```