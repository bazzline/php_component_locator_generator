<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-12 
 */

namespace Net\Bazzline\Component\Locator\MethodBodyBuilder;

use Net\Bazzline\Component\CodeGenerator\BlockGenerator;

/**
 * Class PropelQueryCreateBuilder
 * @package Net\Bazzline\Component\Locator\MethodBodyBuilder
 */
class PropelQueryCreateBuilder extends AbstractMethodBodyBuilder
{
    /**
     * @param BlockGenerator $body
     * @return BlockGenerator
     * @throws RuntimeException
     */
    public function build(BlockGenerator $body)
    {
        $this->assertMandatoryProperties();

        $body->add('return ' . $this->instance->getClassName() . '::create();');

        return $body;
    }
}