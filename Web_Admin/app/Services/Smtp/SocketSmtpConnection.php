<?php

namespace App\Services\Smtp;

class SocketSmtpConnection implements SmtpConnection
{
    /** @param resource $stream */
    public function __construct(private $stream)
    {
    }

    public function readLine(): string
    {
        $line = @fgets($this->stream, 2048);

        return $line === false ? '' : rtrim($line, "\r\n");
    }

    public function writeLine(string $line): void
    {
        @fwrite($this->stream, $line . "\r\n");
    }

    public function enableCrypto(): bool
    {
        return (bool) @stream_socket_enable_crypto($this->stream, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
    }

    public function close(): void
    {
        if (is_resource($this->stream)) {
            @fwrite($this->stream, "QUIT\r\n");
            @fclose($this->stream);
        }
    }
}
