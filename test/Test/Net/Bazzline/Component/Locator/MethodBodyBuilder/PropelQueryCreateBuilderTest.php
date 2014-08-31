<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-08-07 
 */

namespace Test\Net\Bazzline\Component\Locator\MethodBodyBuilder;

use Test\Net\Bazzline\Component\Locator\LocatorTestCase;

/**
 * Class PropelQueryCreateBuilderTest
 * @package Test\Net\Bazzline\Component\Locator\MethodBodyBuilder
 */
class PropelQueryCreateBuilderTest extends LocatorTestCase
{

    /**
     * @expectedException \Net\Bazzline\Component\Locator\MethodBodyBuilder\RuntimeException
     * @expectedExceptionMessage property "instance" is mandatory
     */
    public function testBuildWithMissingMandatoryProperties()
    {
        $builder = $this->getPropelQueryCreateBuilder();
        $builder->build($this->getBlockGenerator());
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\MethodBodyBuilder\RuntimeException
     * @expectedExceptionMessage property "instance" is mandatory
     */
    public function testClone()
    {
        $builder = $this->getPropelQueryCreateBuilder();
        $builder->setInstance($this->getInstance());
        $clonedBuilder = clone $builder;
        $clonedBuilder->build($this->getBlockGenerator());
    }

    public function testBuild()
    {
        $block = $this->getBlockGenerator();
        $builder = $this->getPropelQueryCreateBuilder();
        $instance = $this->getMockOfInstance();
        $className = 'FooBar';

        $builder->setInstance($instance);

        $instance->shouldReceive('getClassName')
            ->once()
            ->andReturn($className);

        $expectedGeneratedBlockContent = 'return ' . $className . '::create();';


        $this->assertEquals(
            $block,
            $builder->build($block)
        );
        $this->assertEquals(
            $expectedGeneratedBlockContent,
            $block->generate()
        );
    }
}