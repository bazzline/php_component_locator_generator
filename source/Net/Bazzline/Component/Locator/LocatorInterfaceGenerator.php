<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-01-02 
 */

namespace Net\Bazzline\Component\Locator;

use Net\Bazzline\Component\CodeGenerator\ClassGenerator;
use Net\Bazzline\Component\CodeGenerator\Factory\DocumentationGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\MethodGeneratorFactory;

/**
 * Class LocatorInterfaceGenerator
 * @package Net\Bazzline\Component\Locator
 */
class LocatorInterfaceGenerator extends AbstractInterfaceGenerator
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

        if ($configuration->hasImplements()) {
            foreach ($configuration->getImplements() as $interfaceName) {
                //@todo bug!
                //if the class generator is an interface, we have to add
                // multiple extends
                $classGenerator->addImplements($interfaceName);
            }
        }

        if ($configuration->hasInstances()) {
            foreach ($configuration->getInstances() as $instance) {
                $method = $methodGeneratorFactory->create();

                if ($instance->hasAlias()) {
                    $methodName = $instance->getAlias();
                } else {
                    $methodName = (str_replace('\\', '' , $instance->getClassName()));
                }

                $methodName = $configuration->getMethodPrefix() . ucfirst($methodName);
                $returnValue = ($instance->hasReturnValue()) ? $instance->getReturnValue() : $instance->getClassName();

                $method->setDocumentation($documentationGeneratorFactory->create());
                $method->setName($methodName);
                $method->markAsPublic();
                $method->markAsHasNoBody();
                $method->getDocumentation()->setReturn(array($returnValue));

                $classGenerator->addMethod($method);
            }
        }

        return $classGenerator;
    }

    /**
     * @return string
     */
    protected function getInterfaceName()
    {
        return $this->configuration->getClassName() . 'Interface';
    }
}