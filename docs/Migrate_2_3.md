[Documentation](Index.md) / Migration v2 -> v3

Version 3.x has few changes compared to previous version.

# Breaking changes

## setLogger

```php
Client->setLogger(LoggerInterface $logger): void
Server->setLogger(LoggerInterface $logger): void
MiddlewareHandler->setLogger(LoggerInterface $logger): void
```

These methods now return `void` instead of `self.`
This means method return can not be chained.

The change make v3 complient with `psr/log v3`.

## receive

```php
Client->receive(): Message
```

The method no longer has `Message|null` as return type.
It never returned `null` before, so only the method profile has changed.

# Extending

When extending classes in this repo, you might need to implement typed properties in child class.
