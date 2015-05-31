<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator\Process\Transformer;

use Net\Bazzline\Component\ProcessPipe\ExecutableException;
use Net\Bazzline\Component\ProcessPipe\ExecutableInterface;

class LoadBootstrapIfAvailable implements ExecutableInterface
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

        if (isset($input['bootstrap_file'])) {
            if (!is_file($input['bootstrap_file'])) {
                throw new ExecutableException(
                    'provided bootstrap path "' . $input['bootstrap_file'] . '" must be a valid file'
                );
            }

            if (!is_readable($input['bootstrap_file'])) {
                throw new ExecutableException(
                    'provided bootstrap file "' . $input['bootstrap_file'] . '" must be readable'
                );
            }

            require_once $input['bootstrap_file'];
        }

        return $input;
    }
}