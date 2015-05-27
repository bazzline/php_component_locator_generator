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
    public function generateTestDataProvider()
    {
        return array(
            'just locator generator' => array(
                'hasFactoryInstances'               => false,
                'hasSharedInstances'                => false,
                'createLocatorGeneratorInterface'   => false
            ),
            'with factory interface' => array(
                'hasFactoryInstances'               => true,
                'hasSharedInstances'                => false,
                'createLocatorGeneratorInterface'   => false
            ),
            'with shared instances' => array(
                'hasFactoryInstances'               => false,
                'hasSharedInstances'                => true,
                'createLocatorGeneratorInterface'   => false
            ),
            'with interface generation' => array(
                'hasFactoryInstances'               => false,
                'hasSharedInstances'                => false,
                'createLocatorGeneratorInterface'   => true
            ),
            'with factory interface and shared instances' => array(
                'hasFactoryInstances'               => true,
                'hasSharedInstances'                => true,
                'createLocatorGeneratorInterface'   => false
            ),
            'with all' => array(
                'hasFactoryInstances'               => true,
                'hasSharedInstances'                => true,
                'createLocatorGeneratorInterface'   => true
            )
        );
    }

    /**
     * @dataProvider generateTestDataProvider
     * @param bool $hasFactoryInstance
     * @param bool $hasSharedInstances
     * @param bool $createLocatorGeneratorInterface
     */
    public function testGenerate($hasFactoryInstance, $hasSharedInstances, $createLocatorGeneratorInterface)
    {
        $generator          = $this->getGenerator();
        $configuration      = $this->getMockOfConfiguration();
        $fileExistsStrategy = $this->getMockOfFileExistsStrategyInterface();
        $locatorGenerator   = $this->getMockOfLocatorGenerator();

        $configuration->shouldReceive('getFilePath')
            ->andReturn(sys_get_temp_dir())
            ->twice();
        $configuration->shouldReceive('hasFactoryInstances')
            ->andReturn($hasFactoryInstance)
            ->once();
        $configuration->shouldReceive('hasSharedInstances')
            ->andReturn($hasSharedInstances)
            ->atMost(2);
        $configuration->shouldReceive('createLocatorGeneratorInterface')
            ->andReturn($createLocatorGeneratorInterface)
            ->once();

        $locatorGenerator->shouldReceive('setConfiguration')
            ->with($configuration)
            ->once();
        $locatorGenerator->shouldReceive('setFileExistsStrategy')
            ->with($fileExistsStrategy)
            ->once();
        $locatorGenerator->shouldReceive('generate')
            ->once();

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

        if ($createLocatorGeneratorInterface) {
            $interfaceGenerator = $this->getMockOfLocatorInterfaceGenerator();

            $interfaceGenerator->shouldReceive('setConfiguration')
                ->with($configuration)
                ->once();
            $interfaceGenerator->shouldReceive('setFileExistsStrategy')
                ->with($fileExistsStrategy)
                ->once();
            $interfaceGenerator->shouldReceive('generate')
                ->once();

            $generator->setLocatorInterfaceGenerator($interfaceGenerator);
        }

        $generator->setConfiguration($configuration);
        $generator->setFileExistsStrategy($fileExistsStrategy);
        $generator->setLocatorGenerator($locatorGenerator);

        $generator->generate();
    }
}