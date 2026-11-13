<?php

namespace Tests\Support;

use App\Services\Smtp\SmtpConnection;

class FakeSmtpConnection implements SmtpConnection
{
    public array $written = [];

    public bool $closed = false;

    public function __construct(
        private array $responses,
        private bool $crypto = true
    ) {
    }

    public function readLine(): string
    {
        return array_shift($this->responses) ?? '';
    }

    public function writeLine(string $line): void
    {
        $this->written[] = $line;
    }

    public function enableCrypto(): bool
    {
        return $this->crypto;
    }

    public function close(): void
    {
        $this->closed = true;
    }
}
