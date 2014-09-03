<?php
#!/bin/php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-10 
 */

use Net\Bazzline\Component\Locator\Command;

//check if is installed as composer component - taken from pdepend
if (file_exists(__DIR__ . '/../../../autoload.php')) {
    require_once __DIR__ . '/../../../autoload.php';
} else {
    require_once __DIR__ . '/../vendor/autoload.php';
}

global $argc, $argv;

$usageMessage = 'Usage: ' . PHP_EOL .
    basename(__FILE__) . ' <path to configuration file>' . PHP_EOL;

try {
    $command = new Command();
    $command->setArguments($argv);
    $command->execute();
    $configuration = $command->getConfiguration();
    echo 'locator "' . $configuration->getFileName() . '" written into "' . realpath($configuration->getFilePath()) . '"' . PHP_EOL;
    exit(0);
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
    exit(1);
}