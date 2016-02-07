<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-22 
 */

namespace Net\Bazzline\Component\Locator\MethodBodyBuilder;

use Net\Bazzline\Component\CodeGenerator\BlockGenerator;

/**
 * Class FetchFromFactoryInstancePoolBuilder
 * @package Net\Bazzline\Component\Locator\MethodBodyBuilder
 */
class FetchFromFactoryInstancePoolBuilder extends AbstractMethodBodyBuilder
{
    /**
     * @param BlockGenerator $body
     * @return BlockGenerator
     * @throws RuntimeException
     */
    public function build(BlockGenerator $body)
    {
        $this->assertMandatoryProperties();

        $body
            ->add('return $this->fetchFromFactoryInstancePool(\'' . $this->instance->getClassName() . '\')->create();');

        return $body;
    }
}