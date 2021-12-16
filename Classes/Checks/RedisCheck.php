<?php

namespace Wysiwyg\OhDear\Checks;

use Wysiwyg\OhDear\CheckResult;

class RedisCheck extends Check
{
    protected string $host;
    protected ?string $password;
    protected string $database;
    protected int $port;
    protected int $timeout = 5;

    public function __construct(
        string $host,
        string $database,
        ?string $password = null,
        int $port = 6379,
        int $timeout = 5
    ) {
        $this->host = $host;
        $this->database = $database;
        $this->password = $password;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function run(): CheckResult
    {
        $result = CheckResult::make('Redis Connection')
            ->shortSummary('Conntected');

        try {
            $redis = new \Redis();
            $redis->connect($this->host, $this->port, $this->timeout, null, 0, $this->timeout);
            if ($this->password !== null) {
                $redis->auth($this->password);
            }
            $redis->select($this->database);
        } catch (\Exception $exception) {
            return $result
                ->shortSummary('Disconnected')
                ->notificationMessage("Could not connect to redis database ({$exception->getCode()}).")
                ->status(CheckResult::STATUS_FAILED)
                ->meta([
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ]);
        }

        return $result;
    }
}
