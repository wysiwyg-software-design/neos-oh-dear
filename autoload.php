<?php
$rootPath = isset($_SERVER['FLOW_ROOTPATH']) ? $_SERVER['FLOW_ROOTPATH'] : false;
$autloaderFilePath = 'Packages/Libraries/autoload.php';
$currentDir = dirname(__FILE__);

if ($rootPath === false && isset($_SERVER['REDIRECT_FLOW_ROOTPATH'])) {
    $rootPath = $_SERVER['REDIRECT_FLOW_ROOTPATH'];
}
if ($rootPath === false && file_exists($currentDir . '/../../' . $autloaderFilePath)) {
    $rootPath = dirname(__FILE__) . '/../../';
} elseif ($rootPath === false && file_exists($currentDir . '/../../../' . $autloaderFilePath)) {
    $rootPath = dirname(__FILE__) . '/../../../';
} elseif (substr($rootPath, -1) !== '/') {
    $rootPath .= '/';
}

return require($rootPath . 'Packages/Libraries/autoload.php');
