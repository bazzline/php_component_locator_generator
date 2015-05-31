<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator\Process\Validator;

use Net\Bazzline\Component\ProcessPipe\ExecutableException;
use Net\Bazzline\Component\ProcessPipe\ExecutableInterface;

class ConfigurationValidator implements ExecutableInterface
{
    /**
     * @param array $input
     * @return array
     * @throws ExecutableException
     */
    public function execute($input = null)
    {
        if (!is_array($input)) {
            throw new ExecutableException(
                'input must be an array'
            );
        }

        if (!$this->isValidClassName($input, 'assembler')) {
            throw new ExecutableException(
                'array must contain key "assembler"'
            );
        }

        if (!$this->isValidClassName($input, 'file_exists_strategy')) {
            throw new ExecutableException(
                'array must contain key "file_exists_strategy"'
            );
        }

        return $input;
    }

    /**
     * @param array $array
     * @param string $key
     * @return boolean
     */
    private function isValidClassName(array $array, $key)
    {
        return ((isset($array[$key])) && (class_exists($array[$key])));
    }
}