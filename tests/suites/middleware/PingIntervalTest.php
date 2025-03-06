<?php

/**
 * Copyright (C) 2014-2025 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

declare(strict_types=1);

namespace WebSocket\Test\Middleware;

use PHPUnit\Framework\TestCase;
use Phrity\Net\Mock\SocketStream;
use Phrity\Net\Mock\Stack\{
    ExpectContextTrait,
    ExpectSocketStreamTrait,
};
use Stringable;
use WebSocket\Connection;
use WebSocket\Middleware\PingInterval;

/**
 * Test case for WebSocket\Middleware\PingIntervalTest
 */
class PingIntervalTest extends TestCase
{
    use ExpectContextTrait;
    use ExpectSocketStreamTrait;

    public function setUp(): void
    {
        error_reporting(-1);
        $this->setUpStack();
    }

    public function tearDown(): void
    {
        $this->tearDownStack();
    }

    public function testPingInterval(): void
    {
        $temp = tmpfile();

        $middleware = new PingInterval(1);
        $this->assertEquals('WebSocket\Middleware\PingInterval', "{$middleware}");
        $this->assertInstanceOf(Stringable::class, $middleware);

        $this->expectSocketStream();
        $this->expectSocketStreamGetMetadata();
        $this->expectContext();
        $stream = new SocketStream($temp);

        $this->expectSocketStreamGetLocalName();
        $this->expectSocketStreamGetRemoteName();
        $connection = new Connection($stream, false, false);
        $connection->addMiddleware($middleware);

        // First tick set interval
        $this->expectSocketStreamIsWritable();
        $connection->tick();

        // Next tick should not trigger ping
        $this->expectSocketStreamIsWritable();
        $connection->tick();

        sleep(1); // Simulate inactivity

        // This tick should now trigger ping
        $this->expectSocketStreamIsWritable();
        $this->expectSocketStreamWrite()->addAssert(function ($method, $params) {
            $this->assertEquals(base64_decode('iQA='), $params[0]);
        });
        $connection->tick();

        $this->expectSocketStreamIsConnected();
        $this->expectSocketStreamClose();
        unset($stream);
    }
}
