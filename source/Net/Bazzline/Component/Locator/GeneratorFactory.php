<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-05-26 
 */

namespace Net\Bazzline\Component\Locator;

use Net\Bazzline\Component\CodeGenerator\Factory\BlockGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\ClassGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\DocumentationGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\FileGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\MethodGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\PropertyGeneratorFactory;

/**
 * Class GeneratorFactory
 * @package Net\Bazzline\Component\Locator
 */
class GeneratorFactory
{
    /**
     * @return Generator
     */
    public function create()
    {
        //@todo why not rename to GeneratorCollection?
        $generator = new Generator();

        $factoryInterfaceGenerator          = new FactoryInterfaceGenerator();
        $invalidArgumentExceptionGenerator  = new InvalidArgumentExceptionGenerator();
        $locatorGenerator                   = new LocatorGenerator();
        $locatorInterfaceGenerator          = new LocatorInterfaceGenerator();

        $blockGeneratorFactory          = new BlockGeneratorFactory();
        $classGeneratorFactory          = new ClassGeneratorFactory();
        $documentationGeneratorFactory  = new DocumentationGeneratorFactory();
        $fileGeneratorFactory           = new FileGeneratorFactory();
        $methodGeneratorFactory         = new MethodGeneratorFactory();
        $propertyGeneratorFactory       = new PropertyGeneratorFactory();

        $factoryInterfaceGenerator
            ->setInterfaceGeneratorFactory($classGeneratorFactory)
            ->setDocumentationGeneratorFactory($documentationGeneratorFactory)
            ->setFileGeneratorFactory($fileGeneratorFactory)
            ->setMethodGeneratorFactory($methodGeneratorFactory);

        $invalidArgumentExceptionGenerator
            ->setClassGeneratorFactory($classGeneratorFactory)
            ->setDocumentationGeneratorFactory($documentationGeneratorFactory)
            ->setFileGeneratorFactory($fileGeneratorFactory);

        $locatorGenerator
            ->setBlockGeneratorFactory($blockGeneratorFactory)
            ->setClassGeneratorFactory($classGeneratorFactory)
            ->setDocumentationGeneratorFactory($documentationGeneratorFactory)
            ->setFileGeneratorFactory($fileGeneratorFactory)
            ->setMethodGeneratorFactory($methodGeneratorFactory)
            ->setPropertyGeneratorFactory($propertyGeneratorFactory);

        $locatorInterfaceGenerator
            ->setInterfaceGeneratorFactory($classGeneratorFactory)
            ->setDocumentationGeneratorFactory($documentationGeneratorFactory)
            ->setFileGeneratorFactory($fileGeneratorFactory)
            ->setMethodGeneratorFactory($methodGeneratorFactory);

        $generator
            ->setFactoryInterfaceGenerator($factoryInterfaceGenerator)
            ->setInvalidArgumentExceptionGenerator($invalidArgumentExceptionGenerator)
            ->setLocatorGenerator($locatorGenerator)
            ->setLocatorInterfaceGenerator($locatorInterfaceGenerator);

        return $generator;
    }
} 