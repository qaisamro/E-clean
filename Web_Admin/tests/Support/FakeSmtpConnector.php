<?php

namespace Tests\Support;

use App\Services\Smtp\SmtpConnection;
use App\Services\Smtp\SmtpConnector;

class FakeSmtpConnector implements SmtpConnector
{
    public function __construct(private ?SmtpConnection $connection = null)
    {
    }

    public function connect(string $host, int $port, string $encryption, int $timeout): SmtpConnection
    {
        if ($this->connection === null) {
            throw new \RuntimeException('SMTP connection refused.');
        }

        return $this->connection;
    }
}
