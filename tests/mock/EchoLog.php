<?php

/**
 * Copyright (C) 2014-2025 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Test;

use Psr\Log\{
    LoggerInterface,
    LoggerTrait
};

/**
 * Simple echo logger (only available when running in dev environment)
 */
class EchoLog implements LoggerInterface
{
    use LoggerTrait;

    public function log($level, $message, array $context = [])
    {
        $message = $this->interpolate($message, $context);
        $context_string = empty($context) ? '' : json_encode($context);
        echo str_pad($level, 8) . " | {$message} {$context_string}\n";
    }

    public function interpolate($message, array $context = [])
    {
        // Build a replacement array with braces around the context keys
        $replace = [];
        foreach ($context as $key => $val) {
            // Check that the value can be cast to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // Interpolate replacement values into the message and return
        return strtr($message, $replace);
    }
}
