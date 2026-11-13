<?php

namespace App\Services\Smtp;

interface SmtpConnector
{
    public function connect(string $host, int $port, string $encryption, int $timeout): SmtpConnection;
}
