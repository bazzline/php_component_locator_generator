<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-08-07 
 */

namespace Test\Net\Bazzline\Component\Locator\MethodBodyBuilder;

use Test\Net\Bazzline\Component\Locator\LocatorTestCase;

/**
 * Class AbstractMethodBodyBuilderTest
 * @package Test\Net\Bazzline\Component\Locator\MethodBodyBuilder
 */
class AbstractMethodBodyBuilderTest extends LocatorTestCase
{
    public function testSetInstance()
    {
        $builder = $this->getAbstractMethodBodyBuilder();
        $this->assertEquals($builder, $builder->setInstance($this->getMockOfInstance()));
    }

    public function extend()
    {
        $builder = $this->getAbstractMethodBodyBuilder();
        $documentation = $this->getMockOfDocumentationGenerator();

        $this->assertEquals($documentation, $builder->extend($documentation));
    }
} 