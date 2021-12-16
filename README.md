# Neos Oh Dear

This package makes it easy to monitor your neos application vitals and outputs a JSON for Oh Dear.

## Installation

You can easily install this plugin via composer

```bash
composer require wy/neos-oh-dear
```

## Set Up

1. Create a file in the applications root folder and copy following code:

```php
<?php
use \Wysiwyg\OhDear\Checks;

$composerAutoloader = require_once '../Packages/Plugins/Wysiwyg.OhDear/autoload.php';
$app = new \Wysiwyg\OhDear\Application($composerAutoloader);

/** @var array<int, \Wysiwyg\OhDear\Checks\Check> $checks */
$checks = [
    new Checks\DiskSpaceCheck(),
    new Checks\CpuLoadCheck(),
    new Checks\DatabaseCheck(
        $app->getNeosConfig('Neos.Flow.persistence.backendOptions.host'),
        $app->getNeosConfig('Neos.Flow.persistence.backendOptions.user'),
        $app->getNeosConfig('Neos.Flow.persistence.backendOptions.password'),
        $app->getNeosConfig('Neos.Flow.persistence.backendOptions.dbname'),
        (int) $app->getNeosConfig('Neos.Flow.persistence.backendOptions.port')
    ),
    new Checks\RedisCheck(
        $app->getNeosConfig('Neos_Fusion_Content.backendOptions.hostname', 'Caches'),
        $app->getNeosConfig('Neos_Fusion_Content.backendOptions.password', 'Caches'),
        $app->getNeosConfig('Neos_Fusion_Content.backendOptions.database', 'Caches'),
        (int) $app->getNeosConfig('Neos_Fusion_Content.backendOptions.port', 'Caches')
    ),
    new Checks\SwiftSmtpCheck(
        $app->getNeosConfig('Neos.SwiftMailer.transport.options.host'),
        $app->getNeosConfig('Neos.SwiftMailer.transport.options.encryption'),
        $app->getNeosConfig('Neos.SwiftMailer.transport.options.port'),
        $app->getNeosConfig('Neos.SwiftMailer.transport.options.user'),
        $app->getNeosConfig('Neos.SwiftMailer.transport.options.password'),
        $app->getNeosConfig('Neos.SwiftMailer.transport.options.streamOptions'),
    ),
];

$app->process($checks);
```

2. Remove unused Checks
3. Open the file in your web browser, the result should be a json
