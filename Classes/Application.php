<?php

namespace Wysiwyg\OhDear;

use Composer\Autoload\ClassLoader;
use Neos\Flow\Core\Bootstrap as NeosFlowBootstrap;

class Application
{
    protected string $context;
    protected ?ConfigLoader $configLoader = null;
    protected NeosFlowBootstrap $neosFlowBootstrap;
    protected const HTTP_SECRET_HEADER = 'OH-DEAR-HEALTH-CHECK-SECRET';

    public static function make(ClassLoader $composerAutoloader): self
    {
        return new self($composerAutoloader);
    }

    public function __construct(ClassLoader $composerAutoloader)
    {
        $this->context = NeosFlowBootstrap::getEnvironmentConfigurationSetting('FLOW_CONTEXT') ?: 'Development';
        // bootstrap app for app constants
        $this->neosFlowBootstrap = new NeosFlowBootstrap($this->context, $composerAutoloader);
        $this->configLoader = new ConfigLoader($this->context);
    }

    protected function verifyOhDearSecret(string $secret): bool
    {
        $headers = array_change_key_case(getallheaders(), CASE_UPPER);
        if (isset($headers[self::HTTP_SECRET_HEADER]) &&
            $headers[self::HTTP_SECRET_HEADER] === $secret
        ) {
            return true;
        }

        return  false;
    }

    public function getNeosConfig(string $path, string $type = 'Settings')
    {
        return $this->configLoader->getConfig($path, $type);
    }

    public function process(array $checks)
    {
        $ohDearSecret = $this->configLoader->getConfig('Wysiwyg.OhDear.healthSecret');
        // check oh dear health secret
        if ($ohDearSecret !== null && !$this->verifyOhDearSecret($ohDearSecret)) {
            http_response_code(403);
            die('Forbidden access – Secret not correct');
        }

        $checkResults = [];

        foreach ($checks as $check) {
            $checkResults[] = $check->run();
        }

        header('Content-Type: application/json; charset=utf-8');
        echo new ChecksResponse(null, $checkResults);
    }
}
