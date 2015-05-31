<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator\Process\Transformer;

use Net\Bazzline\Component\CodeGenerator\Factory\BlockGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\ClassGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\DocumentationGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\FileGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\InterfaceGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\MethodGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\PropertyGeneratorFactory;
use Net\Bazzline\Component\ProcessPipe\ExecutableException;
use Net\Bazzline\Component\ProcessPipe\ExecutableInterface;

class FactoryGenerator implements ExecutableInterface
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

        $input['block_generator_factory']           = new BlockGeneratorFactory();
        $input['class_generator_factory']           = new ClassGeneratorFactory();
        $input['interface_generator_factory']       = new InterfaceGeneratorFactory();
        $input['documentation_generator_factory']   = new DocumentationGeneratorFactory();
        $input['file_generator_factory']            = new FileGeneratorFactory();
        $input['method_generator_factory']          = new MethodGeneratorFactory();
        $input['property_generator_factory']        = new PropertyGeneratorFactory();

        return $input;
    }
}