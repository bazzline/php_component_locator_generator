<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-13 
 */

namespace Net\Bazzline\Component\Locator\MethodBodyBuilder;

use Net\Bazzline\Component\CodeGenerator\BlockGenerator;

/**
 * Class NewInstanceBuilder
 * @package Net\Bazzline\Component\Locator\MethodBodyBuilder
 */
class NewInstanceBuilder extends AbstractMethodBodyBuilder
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
            ->add('return new ' . $this->instance->getClassName() . '();');

        return $body;
    }
}