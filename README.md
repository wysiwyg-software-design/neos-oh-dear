# Neos Oh Dear

This package makes it easy to monitor your Neos application vitals and outputs a JSON for Oh Dear.

## Installation

You can easily install this plugin via composer

```bash
composer require wy/neos-oh-dear
```

## Set Up

1. Create a file in the applications root folder
    1. Require `../Packages/Plugins/Wysiwyg.OhDear/autoload.php`
    2. Initialize the `\Wysiwyg\OhDear\Application` class with the Composer autoloader (see below for an example)
    3. Initialize your checks with the correct configuration values
    4. Process the steps
2. Configure your Oh Dear secret at the following config path in Neos: `Wysiwyg.OhDear.healthSecret` (only for production)
3. Open the file in your web browser, the result should be a JSON string (see example below)

### Available Checks
- [CpuLoadCheck](./Classes/Checks/CpuLoadCheck.php)
- [DatabaseCheck](./Classes/Checks/DatabaseCheck.php)
- [DiskSpaceCheck](./Classes/Checks/DiskSpaceCheck.php)
- [RedisCheck](./Classes/Checks/RedisCheck.php)
- [SwiftSmtpCheck](./Classes/Checks/SwiftSmtpCheck.php)

### Example File

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

### JSON Output example
```json
{
    "finishedAt": 1639735170,
    "checkResults": [
        {
            "name": "Disk space",
            "label": "",
            "notificationMessage": "The disk is almost full (90% used).",
            "shortSummary": "90%",
            "status": "warning",
            "meta": {
                "disk_space_used_percentage": 90
            }
        },
        {
            "name": "CPU Load",
            "label": "",
            "notificationMessage": "",
            "shortSummary": "3.31982421875 2.98046875 2.98046875",
            "status": "ok",
            "meta": {
                "lastMinute": 3.31982421875,
                "last5Minutes": 2.98046875,
                "last15Minutes": 2.98046875
            }
        },
        {
            "name": "Database Connection",
            "label": "",
            "notificationMessage": "",
            "shortSummary": "Conntected",
            "status": "ok",
            "meta": []
        }
    ]
}
```
