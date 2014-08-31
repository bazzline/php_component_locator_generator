<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-08-07 
 */

namespace Test\Net\Bazzline\Component\Locator\MethodBodyBuilder;

use Test\Net\Bazzline\Component\Locator\LocatorTestCase;

/**
 * Class FetchFromFactoryInstancePoolBuilderTest
 * @package Test\Net\Bazzline\Component\Locator\MethodBodyBuilder
 */
class FetchFromFactoryInstancePoolBuilderTest extends LocatorTestCase
{
    /**
     * @expectedException \Net\Bazzline\Component\Locator\MethodBodyBuilder\RuntimeException
     * @expectedExceptionMessage property "instance" is mandatory
     */
    public function testBuildWithMissingMandatoryProperties()
    {
        $builder = $this->getFetchFromFactoryInstancePoolBuilder();
        $builder->build($this->getBlockGenerator());
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\MethodBodyBuilder\RuntimeException
     * @expectedExceptionMessage property "instance" is mandatory
     */
    public function testClone()
    {
        $builder = $this->getFetchFromFactoryInstancePoolBuilder();
        $builder->setInstance($this->getInstance());
        $clonedBuilder = clone $builder;
        $clonedBuilder->build($this->getBlockGenerator());
    }

    public function testBuild()
    {
        $block = $this->getBlockGenerator();
        $builder = $this->getFetchFromFactoryInstancePoolBuilder();
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
            'return $this->fetchFromFactoryInstancePool(\'' . $className . '\')->create();',
            $block->generate()
        );
    }
} 