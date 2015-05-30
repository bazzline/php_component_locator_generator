<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator\Application;

use Net\Bazzline\Component\Cli\Arguments\Arguments;
use Net\Bazzline\Component\Locator\Command;

class ApplicationFactory
{
    /**
     * @return Application
     */
    public function create()
    {
        global $argv;

        $application    = new Application();
        $arguments      = new Arguments($argv);
        $command        = new Command();

        $application->setCommand($command);
        $command->setArguments($arguments);

        return $application;
    }
}