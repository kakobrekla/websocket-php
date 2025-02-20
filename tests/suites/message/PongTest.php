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
    Pong,
    Message
};

/**
 * Test case for WebSocket\Message\Pong.
 */
class PongTest extends TestCase
{
    public function setUp(): void
    {
        error_reporting(-1);
    }

    public function testPongMessage(): void
    {
        $message = new Pong('Some content');
        $this->assertInstanceOf(Pong::class, $message);
        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('Some content', $message->getContent());
        $this->assertEquals('pong', $message->getOpcode());
        $this->assertEquals(12, $message->getLength());
        $this->assertTrue($message->hasContent());
        $this->assertInstanceOf('DateTimeImmutable', $message->getTimestamp());
        $message->setContent('');
        $this->assertEquals(0, $message->getLength());
        $this->assertFalse($message->hasContent());
        $this->assertInstanceOf(Stringable::class, $message);
        $this->assertEquals('WebSocket\Message\Pong', "{$message}");

        $frames = $message->getFrames();
        $this->assertCount(1, $frames);
        $this->assertContainsOnlyInstancesOf(Frame::class, $frames);
    }

    public function testPongPayload(): void
    {
        $message = new Pong('Some content');
        $payload = $message->getPayload();
        $this->assertEquals('U29tZSBjb250ZW50', base64_encode($payload));
        $message = new Pong();
        $message->setPayload($payload);
        $this->assertEquals('Some content', $message->getContent());
    }
}
