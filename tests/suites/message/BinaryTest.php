<?php

/**
 * Copyright (C) 2014-2025 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

declare(strict_types=1);

namespace WebSocket\Test\Message;

use PHPUnit\Framework\TestCase;
use Stringable;
use WebSocket\BadOpcodeException;
use WebSocket\Frame\Frame;
use WebSocket\Message\{
    Binary,
    Message
};

/**
 * Test case for WebSocket\Message\Binary.
 */
class BinaryTest extends TestCase
{
    public function setUp(): void
    {
        error_reporting(-1);
    }

    public function testBinaryMessage(): void
    {
        $bin = base64_encode('Some content');
        $message = new Binary($bin);
        $this->assertInstanceOf(Binary::class, $message);
        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($bin, $message->getContent());
        $this->assertEquals('binary', $message->getOpcode());
        $this->assertEquals(16, $message->getLength());
        $this->assertTrue($message->hasContent());
        $this->assertInstanceOf('DateTimeImmutable', $message->getTimestamp());
        $message->setContent('');
        $this->assertEquals(0, $message->getLength());
        $this->assertFalse($message->hasContent());
        $this->assertInstanceOf(Stringable::class, $message);
        $this->assertEquals('WebSocket\Message\Binary', "{$message}");

        $frames = $message->getFrames();
        $this->assertCount(1, $frames);
        $this->assertContainsOnlyInstancesOf(Frame::class, $frames);
    }

    public function testBinaryPayload(): void
    {
        $message = new Binary('Some content');
        $payload = $message->getPayload();
        $this->assertEquals('U29tZSBjb250ZW50', base64_encode($payload));
        $message = new Binary();
        $message->setPayload($payload);
        $this->assertEquals('Some content', $message->getContent());
    }
}
