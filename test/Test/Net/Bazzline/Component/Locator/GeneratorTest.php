<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-26 
 */

namespace Test\Net\Bazzline\Component\Locator;

use org\bovigo\vfs\vfsStream;

/**
 * Class GeneratorTest
 * @package Test\Net\Bazzline\Component\Locator
 */
class GeneratorTest extends LocatorTestCase
{
    public function testSetters()
    {
        $generator = $this->getGenerator();

        $this->assertEquals($generator, $generator->setFactoryInterfaceGenerator($this->getMockOfFactoryInterfaceGenerator()));
        $this->assertEquals($generator, $generator->setInvalidArgumentExceptionGenerator($this->getMockOfInvalidArgumentExceptionGenerator()));
        $this->assertEquals($generator, $generator->setLocatorGenerator($this->getMockOfLocatorGenerator()));
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\RuntimeException
     * @expectedExceptionMessage provided path "vfs://foo/bar" is not a directory
     */
    public function testGenerateWithInvalidFilePath()
    {
        $generator = $this->getGenerator();
        $configuration = $this->getMockOfConfiguration();

        $path = 'foo';
        $permissions = 0755;
        $root = vfsStream::setup($path, $permissions);
        $name = 'bar';
        $file = vfsStream::newFile($name);
        $root->addChild($file);

        $configuration->shouldReceive('getFilePath')
            ->andReturn($file->url());

        $generator->setConfiguration($configuration);

        $generator->generate();
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\RuntimeException
     * @expectedExceptionMessage provided directory "vfs://foo" is not writable
     */
    public function testGenerateWithNotWritableFilePath()
    {
        $generator = $this->getGenerator();
        $configuration = $this->getMockOfConfiguration();

        $path = 'foo';
        $permissions = 0400;
        $root = vfsStream::setup($path, $permissions);

        $configuration->shouldReceive('getFilePath')
            ->andReturn($root->url());

        $generator->setConfiguration($configuration);

        $generator->generate();
    }

    /**
     * @return array
     */
    public static function generateTestDataProvider()
    {
        return array(
            'just locator generator' => array(
                'hasFactoryInstances' => false,
                'hasSharedInstances' => false
            ),
            'with factory interface' => array(
                'hasFactoryInstances' => true,
                'hasSharedInstances' => false
            ),
            'with shared instances' => array(
                'hasFactoryInstances' => false,
                'hasSharedInstances' => true
            ),
            'with factory interface and shared instances' => array(
                'hasFactoryInstances' => true,
                'hasSharedInstances' => true
            )
        );
    }

    /**
     * @dataProvider generateTestDataProvider
     * @param bool $hasFactoryInstance
     * @param bool $hasSharedInstances
     */
    public function testGenerate($hasFactoryInstance, $hasSharedInstances)
    {
        $generator = $this->getGenerator();
        $configuration = $this->getMockOfConfiguration();
        $fileExistsStrategy = $this->getMockOfFileExistsStrategyInterface();
        $locatorGenerator = $this->getMockOfLocatorGenerator();

        $configuration->shouldReceive('getFilePath')
            ->andReturn(sys_get_temp_dir())
            ->twice();
        $configuration->shouldReceive('hasFactoryInstances')
            ->andReturn($hasFactoryInstance)
            ->twice();
        $configuration->shouldReceive('hasSharedInstances')
            ->andReturn($hasSharedInstances)
            ->atMost();

        $locatorGenerator->shouldReceive('setConfiguration')
            ->with($configuration)
            ->once();
        $locatorGenerator->shouldReceive('setFileExistsStrategy')
            ->with($fileExistsStrategy)
            ->once();
        $locatorGenerator->shouldReceive('generate')
            ->once();

        if ($hasFactoryInstance) {
            $factoryInterfaceGenerator = $this->getMockOfFactoryInterfaceGenerator();

            $factoryInterfaceGenerator->shouldReceive('setConfiguration')
                ->with($configuration)
                ->once();
            $factoryInterfaceGenerator->shouldReceive('setFileExistsStrategy')
                ->with($fileExistsStrategy)
                ->once();
            $factoryInterfaceGenerator->shouldReceive('generate')
                ->once();

            $generator->setFactoryInterfaceGenerator($factoryInterfaceGenerator);
        }

        if ($hasFactoryInstance || $hasSharedInstances) {
            $invalidArgumentExceptionGenerator = $this->getMockOfInvalidArgumentExceptionGenerator();

            $invalidArgumentExceptionGenerator->shouldReceive('setConfiguration')
                ->with($configuration)
                ->once();
            $invalidArgumentExceptionGenerator->shouldReceive('setFileExistsStrategy')
                ->with($fileExistsStrategy)
                ->once();
            $invalidArgumentExceptionGenerator->shouldReceive('generate')
                ->once();

            $generator->setInvalidArgumentExceptionGenerator($invalidArgumentExceptionGenerator);
        }

        $generator->setConfiguration($configuration);
        $generator->setFileExistsStrategy($fileExistsStrategy);
        $generator->setLocatorGenerator($locatorGenerator);

        $generator->generate();
    }
}