<?php

namespace Wysiwyg\OhDear;

use Neos\Flow\Http\Client\Browser;
use Neos\Flow\Http\Client\CurlEngine;

class ScheduledTaskMonitor
{
    protected string $id;
    protected float $startTimeInMicroSeconds = 0.0;
    const BASE_URL = 'https://ping.ohdear.app/';

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function make(string $id)
    {
        return new static($id);
    }

    public function start(): self
    {
        $this->startTimeInMicroSeconds = microtime(true);

        return $this;
    }

    public function finish(int $exitCode =  0, string $errorMessage = null): self
    {
        $memoryUsage = memory_get_peak_usage();
        $timeElapsedInSeconds = round((microtime(true) - $this->startTimeInMicroSeconds) / (10**6), 2);
        $requestBody = [
            'memory' => $memoryUsage,
            'runtime' => $timeElapsedInSeconds,
            'exit_code' => $exitCode,
        ];

        if ($errorMessage !== null) {
            $requestBody['failure_message'] = $errorMessage;
        }

        $this->getRequestEngine()
            ->request($this->getPingUrl(), 'POST', $requestBody);

        return $this;
    }

    protected function getPingUrl(): string
    {
        return static::BASE_URL . $this->id;
    }

    protected function getRequestEngine(): Browser
    {
        $browser = new Browser();
        $browser->setRequestEngine(new CurlEngine());

        return $browser;
    }
}
