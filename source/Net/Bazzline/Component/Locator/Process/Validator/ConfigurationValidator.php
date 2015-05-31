<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator\Process\Validator;

use Net\Bazzline\Component\Locator\Configuration;
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

        if (!($input['configuration'] instanceof Configuration)) {
            throw new ExecutableException(
                'input must an instance of Configuration'
            );
        }
        /** @var Configuration $configuration */
        $configuration  = $input['configuration'];
        $path           = $configuration->getFilePath();

        if (is_file($path)) {
            $message = 'provided path "' . $path . '" is an existing file';

            throw new ExecutableException($message);
        }

        if (!is_dir($path)) {
            $couldNotCreateNotExistingDirectory = !(mkdir($path));

            if ($couldNotCreateNotExistingDirectory) {
                $message = 'could not create directory "' . $path . '"';

                throw new ExecutableException($message);
            }
        }

        if (!is_writable($path)) {
            $message = 'provided directory "' . $path . '" is not writable';

            throw new ExecutableException($message);
        }

        return $input;
    }
}