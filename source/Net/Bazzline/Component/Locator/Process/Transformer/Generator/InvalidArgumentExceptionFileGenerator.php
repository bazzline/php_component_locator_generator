<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator\Process\Transformer\Generator;

use Net\Bazzline\Component\Locator\Configuration;
use Net\Bazzline\Component\Locator\InvalidArgumentExceptionGenerator;
use Net\Bazzline\Component\ProcessPipe\ExecutableException;
use Net\Bazzline\Component\ProcessPipe\ExecutableInterface;

class InvalidArgumentExceptionFileGenerator implements ExecutableInterface
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

        /** @var Configuration $configuration */
        $configuration = $input['configuration'];

        if (($configuration->hasFactoryInstances())
            || ($configuration->hasSharedInstances())) {
            $generator  = new InvalidArgumentExceptionGenerator();
            $strategy   = $input['file_exists_strategy'];

            $generator
                ->setClassGeneratorFactory($input['class_generator_factory'])
                ->setDocumentationGeneratorFactory($input['documentation_generator_factory'])
                ->setConfiguration($configuration)
                ->setFileExistsStrategy($strategy)
                ->setFileGeneratorFactory($input['file_generator_factory'])
                ->generate();
        }

        return $input;
    }
}