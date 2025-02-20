<?php

/**
 * Copyright (C) 2014-2025 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Trait;

use Closure;
use Psr\Http\Message\{
    RequestInterface,
    ResponseInterface,
};
use Throwable;
use WebSocket\Connection;
use WebSocket\Message\Message;

/**
 * WebSocket\Trait\ListenerTrait trait.
 * Provides listener functions.
 * @template T
 */
trait ListenerTrait
{
    /** @var array<string, Closure> $listeners */
    private array $listeners = [];

    /* @todo: Deprecate and remove in v4 */
    /** @param Closure(T, Connection, RequestInterface|ResponseInterface): void $closure */
    public function onConnect(Closure $closure): self
    {
        $msg = 'onConnect() is deprecated and will be removed in v4. Use onHandshake() instead.';
        trigger_error($msg, E_USER_DEPRECATED);
        $this->listeners['connect'] = $closure;
        return $this;
    }

    /** @param Closure(T, Connection): void $closure */
    public function onDisconnect(Closure $closure): self
    {
        $this->listeners['disconnect'] = $closure;
        return $this;
    }

    /** @param Closure(T, Connection, RequestInterface, ResponseInterface): void $closure */
    public function onHandshake(Closure $closure): self
    {
        $this->listeners['handshake'] = $closure;
        return $this;
    }

    /** @param Closure(T, Connection, Message): void $closure */
    public function onText(Closure $closure): self
    {
        $this->listeners['text'] = $closure;
        return $this;
    }

    /** @param Closure(T, Connection, Message): void $closure */
    public function onBinary(Closure $closure): self
    {
        $this->listeners['binary'] = $closure;
        return $this;
    }

    /** @param Closure(T, Connection, Message): void $closure */
    public function onPing(Closure $closure): self
    {
        $this->listeners['ping'] = $closure;
        return $this;
    }

    /** @param Closure(T, Connection, Message): void $closure */
    public function onPong(Closure $closure): self
    {
        $this->listeners['pong'] = $closure;
        return $this;
    }

    /** @param Closure(T, Connection, Message): void $closure */
    public function onClose(Closure $closure): self
    {
        $this->listeners['close'] = $closure;
        return $this;
    }

    /** @param Closure(T, Connection|null, Throwable): void $closure */
    public function onError(Closure $closure): self
    {
        $this->listeners['error'] = $closure;
        return $this;
    }

    /** @param Closure(T): void $closure */
    public function onTick(Closure $closure): self
    {
        $this->listeners['tick'] = $closure;
        return $this;
    }

    /**
     * @param array{
     *   0: T,
     *   1?: Connection|null,
     *   2?: Message|RequestInterface|ResponseInterface|Throwable|null,
     *   3?: ResponseInterface|null
     * } $args
     */
    private function dispatch(string $type, array $args): void
    {
        if (array_key_exists($type, $this->listeners)) {
            $closure = $this->listeners[$type];
            call_user_func_array($closure, $args);
        }
    }
}
