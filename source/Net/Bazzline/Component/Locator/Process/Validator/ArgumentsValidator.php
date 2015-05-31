<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator\Process\Validator;

use Net\Bazzline\Component\Cli\Arguments\Arguments;
use Net\Bazzline\Component\ProcessPipe\ExecutableException;
use Net\Bazzline\Component\ProcessPipe\ExecutableInterface;

class ArgumentsValidator implements ExecutableInterface
{
    /**
     * @param Arguments $input
     * @return array
     * @throws ExecutableException
     */
    public function execute($input = null)
    {
        if (!($input instanceof Arguments)) {
            throw new ExecutableException(
                'input must be instance of Arguments'
            );
        }

        if (count($input->getValues()) != 1) {
            throw new ExecutableException(
                'no path to configuration file provided'
            );
        }

        $path   = $this->buildPathToConfigurationFromArguments($input);

        return $path;
    }

    /**
     * @param Arguments $arguments
     * @return string
     */
    private function buildPathToConfigurationFromArguments(Arguments $arguments)
    {
        $cwd            = getcwd();
        $values         = $arguments->getValues();
        $path           = $values[0];
        $isRelativePath = ($path[0] !== '/');

        if ($isRelativePath) {
            $pathToConfigurationFile = realpath($cwd . DIRECTORY_SEPARATOR . $path);
        } else {
            $pathToConfigurationFile = realpath($path);
        }

        return $pathToConfigurationFile;
    }
}