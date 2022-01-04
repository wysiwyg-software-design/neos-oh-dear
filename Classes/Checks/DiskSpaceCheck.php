<?php

namespace Wysiwyg\OhDear\Checks;

use Symfony\Component\Process\Process;
use Wysiwyg\OhDear\CheckResult;

class DiskSpaceCheck extends Check
{
    protected int $warningThreshold = 80;
    protected int $errorThreshold = 90;

    public function run(): CheckResult
    {
        $diskSpaceUsedPercentage = $this->getDiskUsagePercentage();

        $result = CheckResult::make('UsedDiskSpace')
            ->label('Used Disk Space')
            ->meta(['disk_space_used_percentage' => $diskSpaceUsedPercentage])
            ->shortSummary($diskSpaceUsedPercentage . '%');

        if ($diskSpaceUsedPercentage > $this->errorThreshold) {
            return $result
                ->status(CheckResult::STATUS_FAILED)
                ->notificationMessage("The disk is almost full ({$diskSpaceUsedPercentage}% used).");
        }

        if ($diskSpaceUsedPercentage > $this->warningThreshold) {
            return $result
                ->status(CheckResult::STATUS_WARNING)
                ->notificationMessage("The disk is almost full ({$diskSpaceUsedPercentage}% used).");
        }

        return $result;
    }

    public function warnWhenUsedSpaceIsAbovePercentage(int $percentage): self
    {
        $this->warningThreshold = $percentage;

        return $this;
    }

    public function failWhenUsedSpaceIsAbovePercentage(int $percentage): self
    {
        $this->errorThreshold = $percentage;

        return $this;
    }

    protected function getDiskUsagePercentage(): int
    {
        $process = Process::fromShellCommandline('df -P .');
        $process->run();
        $output = $process->getOutput();

        $matchResult = [];
        preg_match('/(\d*)%/', $output, $matchResult);

        return (int) $matchResult[1] ?? -1;
    }
}
