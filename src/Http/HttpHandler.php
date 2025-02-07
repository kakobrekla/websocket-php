<?php

/**
 * Copyright (C) 2014-2024 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Http;

use Phrity\Net\{
    SocketStream,
    Uri
};
use Psr\Http\Message\MessageInterface;
use Psr\Log\{
    LoggerInterface,
    LoggerAwareInterface,
};
use RuntimeException;
use Stringable;
use WebSocket\Trait\StringableTrait;

/**
 * WebSocket\Http\HttpHandler class.
 * Reads and writes HTTP message to/from stream.
 * @deprecated Remove LoggerAwareInterface in v4
 */
class HttpHandler implements LoggerAwareInterface, Stringable
{
    use StringableTrait;

    private SocketStream $stream;
    private bool $ssl;

    public function __construct(SocketStream $stream, bool $ssl = false)
    {
        $this->stream = $stream;
        $this->ssl = $ssl;
    }

    /**
     * @deprecated Remove in v4
     */
    public function setLogger(LoggerInterface $logger): void
    {
    }

    public function pull(): MessageInterface
    {
        $status = $this->readLine();
        $path = $version = null;

        // Pulling server request
        preg_match('!^(?P<method>[A-Z]+) (?P<path>[^ ]*) HTTP/(?P<version>[0-9/.]+)!', $status, $matches);
        if (!empty($matches)) {
            $message = new ServerRequest($matches['method']);
            $path = $matches['path'];
            $version = $matches['version'];
        }

        // Pulling response
        preg_match('!^HTTP/(?P<version>[0-9/.]+) (?P<code>[0-9]*)($|\s(?P<reason>.*))!', $status, $matches);
        if (!empty($matches)) {
            $message = new Response((int)$matches['code'], $matches['reason']);
            $version = $matches['version'];
        }

        if (empty($message)) {
            throw new RuntimeException('Invalid Http request.');
        }

        $message = $message->withProtocolVersion($version);

        while ($header = $this->readLine()) {
            $parts = explode(':', $header, 2);
            if (count($parts) == 2) {
                if ($message->getheaderLine($parts[0]) === '') {
                    $message = $message->withHeader($parts[0], trim($parts[1]));
                } else {
                    $message = $message->withAddedHeader($parts[0], trim($parts[1]));
                }
            }
        }
        if ($message instanceof Request) {
            $scheme = $this->ssl ? 'wss' : 'ws';
            $uri = new Uri("{$scheme}://{$message->getHeaderLine('Host')}{$path}");
            $message = $message->withUri($uri, true);
        }

        return $message;
    }

    /**
     * @param MessageInterface $message
     * @return MessageInterface
     */
    public function push(MessageInterface $message): MessageInterface
    {
        if (!$message instanceof Message) {
            throw new RuntimeException('Generic MessageInterface currently not supported.');
        }
        $data = implode("\r\n", $message->getAsArray()) . "\r\n\r\n";
        $this->stream->write($data);
        return $message;
    }

    private function readLine(): string
    {
        $data = '';
        do {
            $buffer = $this->stream->readLine(1024);
            if (is_null($buffer)) {
                throw new RuntimeException('Could not read Http request.');
            }
            $data .= $buffer;
        } while (!str_ends_with($data, "\n"));
        return trim($data);
    }
}
