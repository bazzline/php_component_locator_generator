<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator\Process\Transformer;

use Net\Bazzline\Component\ProcessPipe\ExecutableException;
use Net\Bazzline\Component\ProcessPipe\ExecutableInterface;

class LoadConfiguration implements ExecutableInterface
{
    /**
     * @param string $input
     * @return array
     * @throws ExecutableException
     */
    public function execute($input = null)
    {
        if (!is_string($input)) {
            throw new ExecutableException(
                'input must be a string'
            );
        }

        if ($this->pathIsExistingAndReadableFile($input)) {
            $configuration = require_once $input;
        } else {
            throw new ExecutableException(
                'provided path must be a readable file'
            );
        }

        return $configuration;
    }

    /**
     * @param string $path
     * @return bool
     */
    private function pathIsExistingAndReadableFile($path)
    {
        return ((is_file($path)) && (is_readable($path)));
    }
}