<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator\Application;

use Net\Bazzline\Component\Cli\Arguments\Arguments;
use Net\Bazzline\Component\Locator\Command;

class Application
{
    /** @var Arguments */
    private $arguments;

    /** @var Command */
    private $command;

    /**
     * @param Arguments $arguments
     */
    public function setArguments(Arguments $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @param Command $command
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;
    }

    public function run()
    {
        $command = $this->command;

        $command->execute();
        $configuration = $command->getConfiguration();

        return 'locator "' . $configuration->getFileName() . '" written into "' . realpath($configuration->getFilePath()) . '"' . PHP_EOL;
    }
}