<?php

namespace App\Services\Smtp;

class SocketSmtpConnector implements SmtpConnector
{
    public function connect(string $host, int $port, string $encryption, int $timeout): SmtpConnection
    {
        $encryption = strtolower($encryption);
        $scheme = ($encryption === 'ssl' || $port === 465) ? 'ssl://' : 'tcp://';

        $errno = 0;
        $errstr = '';
        $stream = @stream_socket_client(
            $scheme . $host . ':' . $port,
            $errno,
            $errstr,
            max(1, $timeout),
            STREAM_CLIENT_CONNECT
        );

        if ($stream === false) {
            throw new \RuntimeException('SMTP connection failed.');
        }

        stream_set_timeout($stream, max(1, $timeout));

        return new SocketSmtpConnection($stream);
    }
}
