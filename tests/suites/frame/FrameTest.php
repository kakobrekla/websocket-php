<?php

/**
 * Copyright (C) 2014-2025 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

declare(strict_types=1);

namespace WebSocket\Test\Frame;

use PHPUnit\Framework\TestCase;
use Stringable;
use WebSocket\BadOpcodeException;
use WebSocket\Frame\Frame;

/**
 * Test case for WebSocket\Frame\Frame.
 */
class FrameTest extends TestCase
{
    public function setUp(): void
    {
        error_reporting(-1);
    }

    public function testTextFrame(): void
    {
        $frame = new Frame('text', 'Text message', true);
        $this->assertInstanceOf(Frame::class, $frame);
        $this->assertInstanceOf(Stringable::class, $frame);
        $this->assertTrue($frame->isFinal());
        $this->assertFalse($frame->getRsv1());
        $this->assertFalse($frame->getRsv2());
        $this->assertFalse($frame->getRsv3());
        $this->assertFalse($frame->isContinuation());
        $this->assertEquals('text', $frame->getOpcode());
        $this->assertEquals('Text message', $frame->getPayload());
        $this->assertEquals(12, $frame->getPayloadLength());
        $this->assertEquals('WebSocket\Frame\Frame(text)', "{$frame}");
    }

    public function testContinuationFrame(): void
    {
        $frame = new Frame('continuation', '0123456789', false);
        $this->assertInstanceOf(Frame::class, $frame);
        $this->assertInstanceOf(Stringable::class, $frame);
        $this->assertFalse($frame->isFinal());
        $this->assertTrue($frame->isContinuation());
        $this->assertEquals('continuation', $frame->getOpcode());
        $this->assertEquals('0123456789', $frame->getPayload());
        $this->assertEquals(10, $frame->getPayloadLength());
        $this->assertEquals('WebSocket\Frame\Frame(continuation)', "{$frame}");
    }
}
