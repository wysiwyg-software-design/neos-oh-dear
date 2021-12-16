<?php

namespace Wysiwyg\OhDear;

use Symfony\Component\Process\Process;
use Neos\Utility\Arrays;

class ConfigLoader
{
    protected string $context;
    protected array $config;

    public function __construct(string $context)
    {
        $this->context = $context;
        $configFile = $this->getConfigFilePath();

        if (!file_exists($configFile)) {
            $this->warmupCache();
        }

        $this->config = require($configFile);
    }

    public function getConfig(string $path, string $type = 'Settings')
    {
        return Arrays::getValueByPath($this->config, $type . '.' . $path);
    }

    protected function getConfigFilePath(): string
    {
        $contextParts = explode('/', $this->context);
        $path = FLOW_PATH_DATA . 'Temporary';
        $path.= '/' . array_shift($contextParts);

        foreach ($contextParts as $part) {
            $path.= '/SubContext' . $part;
        }

        $path.= '/Configuration';
        $filename = str_replace('/', '_', $this->context).'Configurations.php';

        return $path . '/' . $filename;
    }

    protected function warmupCache(): int
    {
        $process = new Process(
            ['php', FLOW_PATH_ROOT.'flow', 'cache:warmup'],
            FLOW_PATH_ROOT,
            ['FLOW_CONTEXT' => $this->context]
        );

        return $process->run();
    }
}
