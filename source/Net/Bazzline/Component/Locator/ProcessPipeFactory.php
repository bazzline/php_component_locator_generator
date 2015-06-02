<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator;

use Net\Bazzline\Component\Locator\Process\Transformer\Generator\ArgumentsGenerator;
use Net\Bazzline\Component\Locator\Process\Transformer\Assembler\ConfigurationAssembler;
use Net\Bazzline\Component\Locator\Process\Transformer\Generator\FactoryGenerator;
use Net\Bazzline\Component\Locator\Process\Transformer\Generator\FileExistsStrategyGenerator;
use Net\Bazzline\Component\Locator\Process\Transformer\Generator\InvalidArgumentExceptionFileGenerator;
use Net\Bazzline\Component\Locator\Process\Transformer\FileLoader\IfAvailableBootstrapFileLoader;
use Net\Bazzline\Component\Locator\Process\Transformer\FileLoader\ConfigurationFileLoader;
use Net\Bazzline\Component\Locator\Process\Transformer\Generator\LocatorFileGenerator;
use Net\Bazzline\Component\Locator\Process\Transformer\Generator\LocatorInterfaceFileGenerator;
use Net\Bazzline\Component\Locator\Process\Validator\ArgumentsValidator;
use Net\Bazzline\Component\Locator\Process\Validator\ConfigurationDataValidator;
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

        $configurationAssembler = new ConfigurationAssembler();
        $configurationAssembler->setConfiguration($configurationFactory->create());

//--------------------------------
//bin/generator
// -> fetch cli arguments
// -> fetch configuration as object
// -> generate
//generate
// -> setup
// -> assemble data
// -> generate files
//assemble
// -> assert mandatory properties
// -> validate data
// -> map data
//generate
// -> validate output path
// -> generate locator
// -> generate factory interface
// -> generate exception
//--------------------------------

        $pipe = new Pipe(
            new IsCommandLineValidator(),
            new ArgumentsGenerator(),
            new ArgumentsValidator(),
            new ConfigurationFileLoader(),
            new ConfigurationDataValidator(),
            new IfAvailableBootstrapFileLoader(),
            $configurationAssembler,
            new FileExistsStrategyGenerator(),
            new ConfigurationValidator(),
            new FactoryGenerator(),
            new LocatorFileGenerator(),
            new InvalidArgumentExceptionFileGenerator(),
            new LocatorInterfaceFileGenerator()
        );

        return $pipe;
    }
}