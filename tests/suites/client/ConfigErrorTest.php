<?php

/**
 * Copyright (C) 2014-2025 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

declare(strict_types=1);

namespace WebSocket\Test\Client;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Phrity\Net\Mock\StreamFactory;
use Phrity\Net\Mock\Stack\{
    ExpectSocketClientTrait,
    ExpectSocketStreamTrait,
    ExpectStreamCollectionTrait,
    ExpectStreamFactoryTrait
};
use WebSocket\Client;
use WebSocket\Exception\{
    BadOpcodeException,
    BadUriException,
    ClientException,
    ConnectionClosedException,
    ConnectionTimeoutException,
    HandshakeException
};
use WebSocket\Test\MockStreamTrait;

/**
 * Test case for WebSocket\Client: Setup & configuration errors.
 */
class ConfigErrorTest extends TestCase
{
    use ExpectSocketClientTrait;
    use ExpectSocketStreamTrait;
    use ExpectStreamCollectionTrait;
    use ExpectStreamFactoryTrait;
    use MockStreamTrait;

    public function setUp(): void
    {
        error_reporting(-1);
        $this->setUpStack();
    }

    public function tearDown(): void
    {
        $this->tearDownStack();
    }

    public function testUriInvalid(): void
    {
        $this->expectException(BadUriException::class);
        $this->expectExceptionMessage("Invalid URI '--:this is not an uri:--' provided.");
        $client = new Client('--:this is not an uri:--');
    }

    public function testUriInvalidScheme(): void
    {
        $this->expectException(BadUriException::class);
        $this->expectExceptionMessage("Invalid URI scheme, must be 'ws' or 'wss'.");
        $client = new Client('bad://localhost:8000/my/mock/path');
    }

    public function testUriInvalidHost(): void
    {
        $this->expectException(BadUriException::class);
        $this->expectExceptionMessage("Invalid URI host.");
        $client = new Client('ws:///my/mock/path');
    }

    public function testInvalidTimeout(): void
    {
        $client = new Client('ws://localhost:8000/my/mock/path');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid timeout '-10' provided");
        $client->setTimeout(-10);
    }

    public function testInvalidFrameSize(): void
    {
        $client = new Client('ws://localhost:8000/my/mock/path');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid frameSize '-10' provided");
        $client->setFrameSize(-10);
    }
}
