<?php

namespace Wysiwyg\OhDear\Checks;

use Wysiwyg\OhDear\CheckResult;

abstract class Check {
    abstract public function run(): CheckResult;
}
