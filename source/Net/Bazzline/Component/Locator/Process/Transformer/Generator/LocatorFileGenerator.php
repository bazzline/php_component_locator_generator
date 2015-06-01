<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator\Process\Transformer\Generator;

use Net\Bazzline\Component\Locator\LocatorGenerator;
use Net\Bazzline\Component\ProcessPipe\ExecutableException;
use Net\Bazzline\Component\ProcessPipe\ExecutableInterface;

class LocatorFileGenerator implements ExecutableInterface
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

        $generator = new LocatorGenerator();

        $generator
            ->setBlockGeneratorFactory($input['block_generator_factory'])
            ->setConfiguration($input['configuration'])
            ->setClassGeneratorFactory($input['class_generator_factory'])
            ->setDocumentationGeneratorFactory($input['documentation_generator_factory'])
            ->setFileGeneratorFactory($input['file_generator_factory'])
            ->setFileExistsStrategy($input['file_exists_strategy'])
            ->setMethodGeneratorFactory($input['method_generator_factory'])
            ->setPropertyGeneratorFactory($input['property_generator_factory'])
            ->generate();

        return $input;
    }
}