<?php

/**
 * Copyright (C) 2014-2025 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

/**
 * Simple send & receive client for test purpose.
 * Run in console: php examples/send.php <options> <message>
 *
 * Console options:
 *  --uri <uri> : The URI to connect to, default ws://localhost:80
 *  --opcode <string> : Opcode to send, default 'text'
 *  --timeout <int> : Timeout in seconds
 *  --framesize <int> : Frame payload size in bytes
 *  --deflate : Add support for per-message deflate compression
 *  --debug : Output log data (if logger is available)
 */

namespace WebSocket;

use Throwable;
use WebSocket\Middleware\{
    CloseHandler,
    CompressionExtension,
    PingResponder,
};
use WebSocket\Middleware\CompressionExtension\DeflateCompressor;
use WebSocket\Test\EchoLog;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(-1);

echo "# Send client! [phrity/websocket]\n";

// Client options specified or default
/**
 * @var array{
 *     uri: string,
 *     opcode: string,
 *     timeout: int<0, max>,
 *     framesize: int<1, max>,
 *     deflate: bool,
 *     debug: bool,
 * } $options
 */
$options = array_merge([
    'uri'       => 'ws://localhost:80',
    'opcode'    => 'text',
], getopt('', ['uri:', 'opcode:', 'timeout:', 'framesize:', 'deflate', 'debug']));

$message = array_pop($argv);

// Initiate client.
try {
    $client = new Client($options['uri']);
    $client
        ->addMiddleware(new CloseHandler())
        ->addMiddleware(new PingResponder())
        ->onText(function ($client, $connection, $message) {
            echo "> Received '{$message->getContent()}' [opcode: {$message->getOpcode()}]\n";
            echo "< Closing client\n";
            $client->close();
        })
        ->onBinary(function ($client, $connection, $message) {
            echo "> Received '{$message->getContent()}' [opcode: {$message->getOpcode()}]\n";
            echo "< Closing client\n";
            $client->close();
        })
        ->onPing(function ($client, $connection, $message) {
            echo "> Received '{$message->getContent()}' [opcode: {$message->getOpcode()}]\n";
            echo "< Closing client\n";
            $client->close();
        })
        ->onPong(function ($client, $connection, $message) {
            echo "> Received '{$message->getContent()}' [opcode: {$message->getOpcode()}]\n";
            echo "< Closing client\n";
            $client->close();
        })
        ->onClose(function ($client, $connection, $message) {
            echo "> Received '{$message->getContent()}' [opcode: {$message->getOpcode()}]\n";
        })
        ;
    if (isset($options['deflate'])) {
        $client->addMiddleware(new CompressionExtension(new DeflateCompressor()));
        echo "# Using per-message: deflate compression\n";
    }

    // If debug mode and logger is available
    if (isset($options['debug']) && class_exists('WebSocket\Test\EchoLog')) {
        $client->setLogger(new EchoLog());
        echo "# Using logger\n";
    }
    if (isset($options['timeout'])) {
        $client->setTimeout($options['timeout']);
        echo "# Set timeout: {$options['timeout']}\n";
    }
    if (isset($options['framesize'])) {
        $client->setFrameSize($options['framesize']);
        echo "# Set frame size: {$options['framesize']}\n";
    }

    $type = $options['opcode'];
    $message = $client->$type($message);
    echo "< Sent '{$message->getContent()}' [opcode: {$message->getOpcode()}]\n";

    $client->start(); // Wait for close confirmation
} catch (Throwable $e) {
    echo "# ERROR: {$e->getMessage()} [{$e->getCode()}]\n";
}
