<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-12-20 
 */

namespace Net\Bazzline\Component\Locator\Configuration\Validator;

/**
 * Class ArrayKeyIsValidClassName
 * @package Net\Bazzline\Component\Locator\Configuration\Validator
 */
class ArrayKeyIsValidClassName
{
    /**
     * @param array $array
     * @param string $key
     */
    public function validate(array $array, $key)
    {
        if (!isset($array[$key])) {
            throw new RuntimeException(
                'array must contain content for key "' . $key . '"'
            );
        }

        if (!class_exists($array[$key])) {
            throw new RuntimeException(
                'provided ' . $key . ' "' . $array['assembler'] . '" does not exist'
            );
        }
    }
}