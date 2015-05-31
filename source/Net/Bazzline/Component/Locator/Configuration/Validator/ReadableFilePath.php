<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-12-20 
 */

namespace Net\Bazzline\Component\Locator\Configuration\Validator;

/**
 * Class ReadableFilePath
 * @package Net\Bazzline\Component\Locator\Configuration\Validator
 */
class ReadableFilePath
{
    public function validate($path)
    {
        if (!is_file($path)) {
            throw new RuntimeException(
                'provided path "' . $path . '" is not a file'
            );
        }

        if (!is_readable($path)) {
            throw new RuntimeException(
                'file "' . $path . '" is not readable'
            );
        }
    }
}