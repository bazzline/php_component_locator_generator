<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator\Process\Transformer;

use Net\Bazzline\Component\Locator\Configuration;
use Net\Bazzline\Component\Locator\Generator;
use Net\Bazzline\Component\ProcessPipe\ExecutableException;
use Net\Bazzline\Component\ProcessPipe\ExecutableInterface;

class FileGenerator implements ExecutableInterface
{
    /** @var Generator */
    private $generator;

    /**
     * @param Generator $generator
     */
    public function setGenerator(Generator $generator)
    {
        $this->generator = $generator;
    }

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

        if (!($input['configuration'] instanceof Configuration)) {
            throw new ExecutableException(
                'input must an instance of Configuration'
            );
        }

        /** @var \Net\Bazzline\Component\Locator\FileExistsStrategy\FileExistsStrategyInterface $fileExistsStrategy */
        $fileExistsStrategy = new $input['file_exists_strategy'];
        $generator          = $this->generator;

        $generator->setConfiguration($input['configuration']);
        $generator->setFileExistsStrategy($fileExistsStrategy);
        $generator->generate();

        return $input['configuration'];
    }
}