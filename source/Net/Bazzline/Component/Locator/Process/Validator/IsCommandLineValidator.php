<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator\Process\Validator;

use Net\Bazzline\Component\ProcessPipe\ExecutableException;
use Net\Bazzline\Component\ProcessPipe\ExecutableInterface;

class IsCommandLineValidator implements ExecutableInterface
{
    /**
     * @param mixed $input
     * @return mixed
     * @throws ExecutableException
     */
    public function execute($input = null)
    {
        $isNotCalledFromCommandLineInterface = (PHP_SAPI !== 'cli');

        if ($isNotCalledFromCommandLineInterface) {
            throw new ExecutableException(
                'This script can only be called from the command line'
            );
        }

        return $input;
    }
}