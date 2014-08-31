<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-08-07 
 */

namespace Test\Net\Bazzline\Component\Locator\MethodBodyBuilder;

use Test\Net\Bazzline\Component\Locator\LocatorTestCase;

/**
 * Class FetchFromSharedInstancePoolOrCreateByFactoryBuilderTest
 * @package Test\Net\Bazzline\Component\Locator\MethodBodyBuilder
 */
class FetchFromSharedInstancePoolOrCreateByFactoryBuilderTest extends LocatorTestCase
{
    /**
     * @expectedException \Net\Bazzline\Component\Locator\MethodBodyBuilder\RuntimeException
     * @expectedExceptionMessage property "instance" is mandatory
     */
    public function testBuildWithMissingMandatoryProperties()
    {
        $builder = $this->getFetchFromSharedInstancePoolOrCreateByFactoryBuilder();
        $builder->build($this->getBlockGenerator());
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\MethodBodyBuilder\RuntimeException
     * @expectedExceptionMessage property "instance" is mandatory
     */
    public function testClone()
    {
        $builder = $this->getFetchFromSharedInstancePoolOrCreateByFactoryBuilder();
        $builder->setInstance($this->getInstance());
        $clonedBuilder = clone $builder;
        $clonedBuilder->build($this->getBlockGenerator());
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\MethodBodyBuilder\RuntimeException
     * @expectedExceptionMessage return value in instance is mandatory
     */
    public function testBuildWithInvalidInstance()
    {
        $block = $this->getBlockGenerator();
        $builder = $this->getFetchFromSharedInstancePoolOrCreateByFactoryBuilder();
        $instance = $this->getMockOfInstance();

        $instance->shouldReceive('hasReturnValue')
            ->once()
            ->andReturn(false);

        $builder->setInstance($instance);

        $this->assertFalse($block->hasContent());
        $builder->build($block);
    }

    public function testBuild()
    {
        $block = $this->getBlockGenerator();
        $builder = $this->getFetchFromSharedInstancePoolOrCreateByFactoryBuilder();
        $className = 'FooBar';
        $instance = $this->getMockOfInstance();
        $returnValue = 'Bar';

        $expectedGeneratedBlockContent = '$className = \'' . $returnValue . '\';' . PHP_EOL .
            PHP_EOL .
            'if ($this->isNotInSharedInstancePool($className)) {' . PHP_EOL .
            '    $factoryClassName = \'' . $className . '\';' . PHP_EOL .
            '    $factory = $this->fetchFromFactoryInstancePool($factoryClassName);' . PHP_EOL .
            '    ' . PHP_EOL .
            '    $this->addToSharedInstancePool($className, $factory->create());' . PHP_EOL .
            '}' . PHP_EOL .
            PHP_EOL .
            'return $this->fetchFromSharedInstancePool($className);';

        $instance->shouldReceive('getClassName')
            ->once()
            ->andReturn($className);
        $instance->shouldReceive('getReturnValue')
            ->once()
            ->andReturn($returnValue);
        $instance->shouldReceive('hasReturnValue')
            ->once()
            ->andReturn(true);

        $builder->setInstance($instance);

        $this->assertFalse($block->hasContent());
        $builder->build($block);
        $this->assertTrue($block->hasContent());
        $this->assertEquals(
            $expectedGeneratedBlockContent,
            $block->generate()
        );
    }
}