<?php

namespace Wysiwyg\OhDear\Checks;

use Wysiwyg\OhDear\CheckResult;

class CpuLoadCheck extends Check
{
    protected ?float $failWhenLoadIsHigherInTheLastMinute = null;
    protected ?float $failWhenLoadIsHigherInTheLast5Minutes = null;
    protected ?float $failWhenLoadIsHigherInTheLast15Minutes = null;

    public function failWhenLoadIsHigherInTheLastMinute(float $load): self
    {
        $this->failWhenLoadIsHigherInTheLastMinute = $load;

        return $this;
    }

    public function failWhenLoadIsHigherInTheLast5Minutes(float $load): self
    {
        $this->failWhenLoadIsHigherInTheLast5Minutes = $load;

        return $this;
    }

    public function failWhenLoadIsHigherInTheLast15Minutes(float $load): self
    {
        $this->failWhenLoadIsHigherInTheLast15Minutes = $load;

        return $this;
    }

    public function run(): CheckResult
    {
        $cpuLoad = $this->measureCpuLoad();

        $result = CheckResult::make('CpuLoad')
            ->label('CPU Load')
            ->shortSummary(
                "{$cpuLoad['lastMinute']} {$cpuLoad['last5Minutes']} {$cpuLoad['last15Minutes']}"
            )
            ->meta($cpuLoad);

        if ($this->failWhenLoadIsHigherInTheLastMinute) {
            if ($cpuLoad['lastMinute'] > ($this->failWhenLoadIsHigherInTheLastMinute)) {
                return $result
                    ->status(CheckResult::STATUS_FAILED)
                    ->notificationMessage("The CPU load of the last minute is {$cpuLoad['lastMinute']} which is higher than the allowed {$this->failWhenLoadIsHigherInTheLastMinute}");
            }
        }

        if ($this->failWhenLoadIsHigherInTheLast5Minutes) {
            if ($cpuLoad['last5Minutes'] > ($this->failWhenLoadIsHigherInTheLast5Minutes)) {
                return $result
                    ->status(CheckResult::STATUS_FAILED)
                    ->notificationMessage("The CPU load of the last 5 minutes is {$cpuLoad['last5Minutes']} which is higher than the allowed {$this->failWhenLoadIsHigherInTheLast5Minutes}");
            }
        }

        if ($this->failWhenLoadIsHigherInTheLast15Minutes) {
            if ($cpuLoad['last15Minutes'] > ($this->failWhenLoadIsHigherInTheLast15Minutes)) {
                return $result
                    ->status(CheckResult::STATUS_FAILED)
                    ->notificationMessage("The CPU load of the last 15 minutes is {$cpuLoad['last15Minutes']} which is higher than the allowed {$this->failWhenLoadIsHigherInTheLast15Minutes}");
            }
        }

        return $result;
    }

    protected function measureCpuLoad(): array
    {
        $load = sys_getloadavg();

        return [
            'lastMinute' => $load[0],
            'last5Minutes' => $load[1],
            'last15Minutes' => $load[1],
        ];
    }
}
