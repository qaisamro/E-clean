<?php

namespace App\Services\Smtp;

class SmtpProbe
{
    public function __construct(private SmtpConnector $connector)
    {
    }

    /**
     * Verify TCP connectivity, TLS and authentication without sending a
     * message. This deliberately stops before MAIL FROM/RCPT TO/DATA.
     */
    public function test(array $config): array
    {
        $host = trim((string) ($config['host'] ?? ''));
        $port = (int) ($config['port'] ?? 0);
        $encryption = strtolower(trim((string) ($config['encryption'] ?? 'tls')));
        $username = (string) ($config['username'] ?? '');
        $password = (string) ($config['password'] ?? '');
        $timeout = max(1, (int) ($config['timeout'] ?? 15));

        if ($host === '' || $port < 1 || $port > 65535) {
            return $this->result('not_configured', 'SMTP host or port is missing.');
        }

        if (($username === '') !== ($password === '')) {
            return $this->result('not_configured', 'SMTP username and password must both be provided.');
        }

        try {
            $connection = $this->connector->connect($host, $port, $encryption, $timeout);
        } catch (\Throwable) {
            return $this->result('failed', 'SMTP connection failed. Check the host and port.');
        }

        try {
            $greeting = $connection->readLine();
            if ($this->code($greeting) !== 220) {
                return $this->result('failed', 'SMTP server did not return a ready greeting.');
            }

            $ehlo = $this->command($connection, 'EHLO ' . $this->heloHost());
            if ($this->code($ehlo) !== 250) {
                return $this->result('failed', 'SMTP server rejected the EHLO command.');
            }

            if (in_array($encryption, ['tls', 'starttls'], true) && $port !== 465) {
                $startTls = $this->command($connection, 'STARTTLS');
                if ($this->code($startTls) !== 220) {
                    return $this->result('failed', 'SMTP server rejected STARTTLS.');
                }

                if (!$connection->enableCrypto()) {
                    return $this->result('failed', 'SMTP TLS handshake failed.');
                }

                $ehlo = $this->command($connection, 'EHLO ' . $this->heloHost());
                if ($this->code($ehlo) !== 250) {
                    return $this->result('failed', 'SMTP server rejected EHLO after TLS.');
                }
            }

            if ($username !== '' && $password !== '') {
                $auth = $this->command($connection, 'AUTH LOGIN');
                $authCode = $this->code($auth);

                if ($authCode === 235) {
                    return $this->result('ok', 'SMTP connection and authentication succeeded (no email sent).');
                }

                if ($authCode !== 334) {
                    return $this->result('failed', 'SMTP server rejected AUTH LOGIN. Check the encryption and authentication settings.');
                }

                $userResponse = $this->command($connection, base64_encode($username));
                if ($this->code($userResponse) !== 334) {
                    return $this->result('failed', 'SMTP authentication failed. Check the username.');
                }

                $passwordResponse = $this->command($connection, base64_encode($password));
                if ($this->code($passwordResponse) !== 235) {
                    return $this->result('failed', 'SMTP authentication failed. Check the password.');
                }
            }

            return $this->result('ok', 'SMTP connection succeeded (no email sent).');
        } catch (\Throwable) {
            return $this->result('failed', 'SMTP connection was interrupted.');
        } finally {
            $connection->close();
        }
    }

    private function command(SmtpConnection $connection, string $command): string
    {
        $connection->writeLine($command);

        $first = '';
        while (true) {
            $line = $connection->readLine();

            if ($first === '') {
                $first = $line;
            }

            if ($line === '' || strlen($line) < 4 || $line[3] !== '-') {
                break;
            }
        }

        return $first;
    }

    private function code(string $line): int
    {
        return preg_match('/^(\d{3})/', $line, $matches) ? (int) $matches[1] : 0;
    }

    private function heloHost(): string
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        return is_string($host) && $host !== '' ? $host : 'localhost';
    }

    private function result(string $status, string $message): array
    {
        return ['status' => $status, 'message' => $message];
    }
}
