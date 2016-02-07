<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-22 
 */

namespace Net\Bazzline\Component\Locator\MethodBodyBuilder;

use Net\Bazzline\Component\CodeGenerator\BlockGenerator;

/**
 * Class FetchFromSharedInstancePoolOrCreateByFactoryBuilder
 * @package Net\Bazzline\Component\Locator\MethodBodyBuilder
 */
class FetchFromSharedInstancePoolOrCreateByFactoryBuilder extends AbstractMethodBodyBuilder
{
    /**
     * @param BlockGenerator $body
     * @return BlockGenerator
     * @throws RuntimeException
     */
    public function build(BlockGenerator $body)
    {
        $this->assertMandatoryProperties();

        if ($this->instance->hasReturnValue()) {
            $returnValue = $this->instance->getReturnValue();
        } else {
            throw new RuntimeException(
                'return value in instance is mandatory'
            );
        }

        //@todo does it make sense to store the factory in the instance
        //  pool since we are using it only once?
        $body
            ->add('$className = \'' . $returnValue . '\';')
            ->add('')
            ->add('if ($this->isNotInSharedInstancePool($className)) {')
            ->startIndention()
            ->add('$factoryClassName = \'' . $this->instance->getClassName() . '\';')
            ->add('$factory = $this->fetchFromFactoryInstancePool($factoryClassName);')
            ->add('')
            ->add('$this->addToSharedInstancePool($className, $factory->create());')
            ->stopIndention()
            ->add('}')
            ->add('')
            ->add('return $this->fetchFromSharedInstancePool($className);');

        return $body;
    }
}