<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator\Process\Transformer;

use Net\Bazzline\Component\Locator\Configuration;
use Net\Bazzline\Component\Locator\FileExistsStrategy\FileExistsStrategyInterface;
use Net\Bazzline\Component\Locator\LocatorInterfaceGenerator;
use Net\Bazzline\Component\ProcessPipe\ExecutableException;
use Net\Bazzline\Component\ProcessPipe\ExecutableInterface;

class LocatorInterfaceFileGenerator implements ExecutableInterface
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

        if ($configuration->createLocatorGeneratorInterface()) {
            $generator  = new LocatorInterfaceGenerator();
            /** @var FileExistsStrategyInterface $strategy */
            $strategy   = $input['file_exists_strategy'];

            $generator
                ->setConfiguration($configuration)
                ->setDocumentationGeneratorFactory($input['documentation_generator_factory'])
                ->setFileExistsStrategy($strategy)
                ->setFileGeneratorFactory($input['file_generator_factory'])
                ->setInterfaceGeneratorFactory($input['interface_generator_factory'])
                ->setMethodGeneratorFactory($input['method_generator_factory'])
                ->generate();
        }

        return $input;
    }
}