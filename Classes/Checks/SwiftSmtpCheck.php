<?php

namespace Wysiwyg\OhDear\Checks;

use Wysiwyg\OhDear\CheckResult;

class SwiftSmtpCheck extends Check
{
    protected string $host;
    protected int $port;
    protected int $timeout = 10;
    protected ?string $user = null;
    protected ?string $password = null;
    protected ?string $encryption = null;
    protected array $streamOptions = [];

    public function __construct(
        string $host,
        string $encryption = null,
        int $port = 465,
        string $user = null,
        string $password = null,
        array $streamOptions = []
    ) {
        $this->host = $host;
        $this->password = $password;
        $this->user = $user;
        $this->encryption = $encryption;
        $this->port = $port;
        $this->streamOptions = $streamOptions;
    }

    public function setStreamingOptions(array $options): self
    {
        $this->streamOptions = $options;

        return $this;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function run(): CheckResult
    {
        $result = CheckResult::make('Smpt')
            ->label('SMTP Connection')
            ->shortSummary('Conntected');

        if ($exception = $this->getSmtpConnectionException()) {
            return $result
                ->shortSummary('Disconnected')
                ->notificationMessage("Could not connect to SMTP server.")
                ->status(CheckResult::STATUS_FAILED)
                ->meta([
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ]);
        }

        return $result;
    }

    protected function getSmtpConnectionException(): ?\Exception
    {
        try {
            $transport = (new \Swift_SmtpTransport($this->host, $this->port, $this->encryption))
                ->setTimeout($this->timeout)
                ->setStreamOptions($this->streamOptions);

            if ($this->user !== null) {
                $transport->setUsername($this->user);
            }
            if ($this->password !== null) {
                $transport->setPassword($this->password);
            }
            if ($this->user !== null) {
                $transport->setUsername($this->user);
            }

            $transport->start();
        } catch (\Exception $e) {
            return $e;
        }

        return null;
    }
}
