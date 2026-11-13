<?php

namespace App\Services\Smtp;

interface SmtpConnection
{
    public function readLine(): string;

    public function writeLine(string $line): void;

    public function enableCrypto(): bool;

    public function close(): void;
}
