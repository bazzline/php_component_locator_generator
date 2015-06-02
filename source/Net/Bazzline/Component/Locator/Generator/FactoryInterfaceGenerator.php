<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-17 
 */

namespace Net\Bazzline\Component\Locator\Generator;

use Net\Bazzline\Component\CodeGenerator\InterfaceGenerator;
use Net\Bazzline\Component\CodeGenerator\Factory\DocumentationGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\MethodGeneratorFactory;
use Net\Bazzline\Component\Locator\Configuration\Configuration;
use Net\Bazzline\Component\Locator\Generator\AbstractInterfaceGenerator;

/**
 * Class FactoryInterfaceGenerator
 * @package Net\Bazzline\Component\Locator
 */
class FactoryInterfaceGenerator extends AbstractInterfaceGenerator
{
    /**
     * @param string $name
     * @param InterfaceGenerator $interfaceGenerator
     * @param Configuration $configuration
     * @param DocumentationGeneratorFactory $documentationGeneratorFactory
     * @param MethodGeneratorFactory $methodGeneratorFactory
     * @return InterfaceGenerator
     */
    protected function createInterface($name, InterfaceGenerator $interfaceGenerator, Configuration $configuration, DocumentationGeneratorFactory $documentationGeneratorFactory, MethodGeneratorFactory $methodGeneratorFactory)
    {
        $interfaceGenerator->setDocumentation($documentationGeneratorFactory->create());
        $interfaceGenerator->setName($name);

        if ($configuration->hasNamespace()) {
            $interfaceGenerator->setNamespace($configuration->getNamespace());
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

        $interfaceGenerator->addMethod($setLocator);
        $interfaceGenerator->addMethod($create);

        return $interfaceGenerator;
    }

    /**
     * @return string
     */
    protected function getInterfaceName()
    {
        return 'FactoryInterface';
    }
}