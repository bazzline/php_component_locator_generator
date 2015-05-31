<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator;

use Net\Bazzline\Component\Locator\Process\Transformer\ArgumentsGenerator;
use Net\Bazzline\Component\Locator\Process\Transformer\ConfigurationAssembler;
use Net\Bazzline\Component\Locator\Process\Transformer\FileGenerator;
use Net\Bazzline\Component\Locator\Process\Transformer\LoadBootstrapIfAvailable;
use Net\Bazzline\Component\Locator\Process\Transformer\LoadConfiguration;
use Net\Bazzline\Component\Locator\Process\Validator\ArgumentsValidator;
use Net\Bazzline\Component\Locator\Process\Validator\ConfigurationValidator;
use Net\Bazzline\Component\Locator\Process\Validator\IsCommandLineValidator;
use Net\Bazzline\Component\ProcessPipe\Pipe;

class ProcessPipeFactory
{
    /**
     * @return Pipe
     */
    public function create()
    {
        $configurationFactory   = new ConfigurationFactory();
        $generatorFactory       = new GeneratorFactory();

        $configurationAssembler = new ConfigurationAssembler();
        $configurationAssembler->setConfiguration($configurationFactory->create());

        $fileGenerator  = new FileGenerator();
        $fileGenerator->setGenerator($generatorFactory->create());

        $pipe = new Pipe(
            new IsCommandLineValidator(),
            new ArgumentsGenerator(),
            new ArgumentsValidator(),
            new LoadConfiguration(),
            new ConfigurationValidator(),
            new LoadBootstrapIfAvailable(),
            $configurationAssembler,
            $fileGenerator
        );

        return $pipe;
    }
}