<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-08-07 
 */

namespace Test\Net\Bazzline\Component\Locator\MethodBodyBuilder;

use Test\Net\Bazzline\Component\Locator\LocatorTestCase;

/**
 * Class FetchFromSharedInstancePoolBuilderTest
 * @package Test\Net\Bazzline\Component\Locator\MethodBodyBuilder
 */
class FetchFromSharedInstancePoolBuilderTest extends LocatorTestCase
{
    /**
     * @expectedException \Net\Bazzline\Component\Locator\MethodBodyBuilder\RuntimeException
     * @expectedExceptionMessage property "instance" is mandatory
     */
    public function testBuildWithMissingMandatoryProperties()
    {
        $builder = $this->getFetchFromSharedInstancePoolBuilder();
        $builder->build($this->getBlockGenerator());
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\MethodBodyBuilder\RuntimeException
     * @expectedExceptionMessage property "instance" is mandatory
     */
    public function testClone()
    {
        $builder = $this->getFetchFromSharedInstancePoolBuilder();
        $builder->setInstance($this->getInstance());
        $clonedBuilder = clone $builder;
        $clonedBuilder->build($this->getBlockGenerator());
    }

    public function testBuild()
    {
        $block = $this->getBlockGenerator();
        $builder = $this->getFetchFromSharedInstancePoolBuilder();
        $className = 'FooBar';
        $instance = $this->getMockOfInstance();

        $instance->shouldReceive('getClassName')
            ->once()
            ->andReturn($className);

        $builder->setInstance($instance);

        $this->assertFalse($block->hasContent());
        $builder->build($block);
        $this->assertTrue($block->hasContent());
        $this->assertEquals(
            'return $this->fetchFromSharedInstancePool(\'' . $className . '\');',
            $block->generate()
        );
    }
} 