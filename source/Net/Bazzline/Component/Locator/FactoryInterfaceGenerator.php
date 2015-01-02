<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-17 
 */

namespace Net\Bazzline\Component\Locator;

use Net\Bazzline\Component\CodeGenerator\ClassGenerator;
use Net\Bazzline\Component\CodeGenerator\Factory\ClassGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\DocumentationGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\FileGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\MethodGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\FileGenerator;

/**
 * Class FactoryInterfaceGenerator
 * @package Net\Bazzline\Component\Locator
 */
class FactoryInterfaceGenerator extends AbstractInterfaceGenerator
{
    /**
     * @param string $name
     * @param ClassGenerator $classGenerator
     * @param Configuration $configuration
     * @param DocumentationGeneratorFactory $documentationGeneratorFactory
     * @param MethodGeneratorFactory $methodGeneratorFactory
     * @return ClassGenerator
     */
    protected function createInterface($name, ClassGenerator $classGenerator, Configuration $configuration, DocumentationGeneratorFactory $documentationGeneratorFactory, MethodGeneratorFactory $methodGeneratorFactory)
    {
        $classGenerator->markAsInterface();
        $classGenerator->setDocumentation($documentationGeneratorFactory->create());
        $classGenerator->setName($name);

        if ($configuration->hasNamespace()) {
            $classGenerator->setNamespace($configuration->getNamespace());
        }

        $setLocator = $methodGeneratorFactory->create();
        $setLocator->setDocumentation($documentationGeneratorFactory->create());
        $setLocator->setName('setLocator');
        $setLocator->addParameter('locator', null, $configuration->getClassName());
        $setLocator->markAsPublic();
        $setLocator->markAsHasNoBody();
        $setLocator->getDocumentation()->setReturn(array('$this'));

        $create = $methodGeneratorFactory->create();
        $create->setDocumentation($documentationGeneratorFactory->create());
        $create->setName('create');
        $create->markAsPublic();
        $create->markAsHasNoBody();
        $create->getDocumentation()->setReturn(array('null', 'object'));

        $classGenerator->addMethod($setLocator);
        $classGenerator->addMethod($create);

        return $classGenerator;
    }

    /**
     * @return string
     */
    protected function getInterfaceName()
    {
        return 'FactoryInterface';
    }
}