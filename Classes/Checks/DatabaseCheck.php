<?php

namespace Wysiwyg\OhDear\Checks;

use Wysiwyg\OhDear\CheckResult;

class DatabaseCheck extends Check
{
    protected string $driver;
    protected string $host;
    protected string $user;
    protected string $password;
    protected string $database;
    protected int $port;
    protected int $timeout = 5;

    public function __construct(
        string $driver,
        string $host,
        string $user,
        string $password,
        string $database,
        int $port = 3306
    ) {
        $this->driver = $driver;
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->port = $port;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function run(): CheckResult
    {
        $result = CheckResult::make('Database')
            ->label('Database Connection')
            ->shortSummary('Conntected');

        try {
            $connection = $this->getConnection();
        } catch (\Exception $exception) {
            return $result
                ->shortSummary('Disconnected')
                ->notificationMessage("Could not connect to mysql database ({$exception->getCode()}).")
                ->status(CheckResult::STATUS_FAILED)
                ->meta([
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ]);
        }

        return $result;
    }

    protected function getConnection(): \PDO
    {
        return new \PDO(
            $this->getPdoDsn(),
            $this->user,
            $this->password,
            [ \PDO::ATTR_TIMEOUT => $this->timeout ]
        );
    }

    protected function getPdoDsn(): string
    {
        return sprintf(
            '%s:dbname=%s;host=%s;port=%d',
            $this->driver,
            $this->database,
            $this->host,
            $this->port
        );
    }
}
