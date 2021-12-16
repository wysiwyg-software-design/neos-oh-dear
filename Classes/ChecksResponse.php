<?php

namespace Wysiwyg\OhDear;

class ChecksResponse
{
    protected \DateTimeInterface $finishedAt;

    /** @var array */
    protected array $checkResults;

    /**
     * @param \DateTimeInterface|null $finishedAt
     * @param array $checkResults
     */
    public function __construct(\DateTimeInterface $finishedAt = null, array $checkResults = [])
    {
        $this->finishedAt = $finishedAt ?? new \DateTimeImmutable();
        $this->checkResults = $checkResults;
    }

    public function addCheckResult(CheckResult $checkResult): self
    {
        $this->checkResults[] = $checkResult;

        return $this;
    }

    /**
     * @return array
     */
    public function checkResults(): array
    {
        return $this->checkResults;
    }

    public function toJson(): string
    {
        $checkResults = array_map(function (CheckResult $checkResult) {
            return $checkResult->toArray();
        }, $this->checkResults);

        return (string) json_encode([
            'finishedAt' => $this->finishedAt->getTimestamp(),
            'checkResults' => $checkResults,
        ]);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }
}
