<?php

/**
 * Copyright (C) 2014-2025 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

declare(strict_types=1);

namespace WebSocket\Test\Server;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebSocket\Server;

/**
 * Test case for WebSocket\Server: Setup & configuration errors.
 */
class ConfigErrorTest extends TestCase
{
    public function setUp(): void
    {
        error_reporting(-1);
    }

    public function testPortTooLow(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage("Invalid port '-1' provided");
        $server = new Server(-1);
    }

    public function testPortTooHigh(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage("Invalid port '65536' provided");
        $server = new Server(65536);
    }

    public function testInvalidTimeout(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage("Invalid timeout '-1' provided");
        $server = new Server();
        // @phpstan-ignore argument.type
        $server->setTimeout(-1);
    }

    public function testInvalidFrameSize(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage("Invalid frameSize '0' provided");
        $server = new Server();
        // @phpstan-ignore argument.type
        $server->setFrameSize(0);
    }

    public function testInvalidMaxConnections(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage("Invalid maxConnections '0' provided");
        $server = new Server();
        $server->setMaxConnections(0);
    }
}
