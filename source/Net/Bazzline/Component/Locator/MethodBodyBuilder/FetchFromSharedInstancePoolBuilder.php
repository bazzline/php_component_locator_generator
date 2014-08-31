<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-22 
 */

namespace Net\Bazzline\Component\Locator\MethodBodyBuilder;

use Net\Bazzline\Component\CodeGenerator\BlockGenerator;

/**
 * Class FetchFromSharedInstancePoolBuilder
 * @package Net\Bazzline\Component\Locator\MethodBodyBuilder
 */
class FetchFromSharedInstancePoolBuilder extends AbstractMethodBodyBuilder
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
            ->add('return $this->fetchFromSharedInstancePool(\'' . $this->instance->getClassName() . '\');');

        return $body;
    }
}