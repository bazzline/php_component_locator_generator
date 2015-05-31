<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator\Process\Transformer;

use Net\Bazzline\Component\ProcessPipe\ExecutableException;
use Net\Bazzline\Component\ProcessPipe\ExecutableInterface;

class FileExistsStrategyGenerator implements ExecutableInterface
{
    /**
     * @param mixed $input
     * @return mixed
     * @throws ExecutableException
     */
    public function execute($input = null)
    {
        if (!is_array($input)) {
            throw new ExecutableException(
                'input must be an array'
            );
        }

        $input['file_exists_strategy'] = new $input['file_exists_strategy'];

        return $input;
    }
}