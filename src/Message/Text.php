<?php

/**
 * Copyright (C) 2014-2025 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Message;

/**
 * WebSocket\Message\Text class.
 * A Text WebSocket message.
 */
class Text extends Message
{
    protected string $opcode = 'text';
}
