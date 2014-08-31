<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-09 
 */

namespace Test\Net\Bazzline\Component\Locator;

use org\bovigo\vfs\vfsStream;

/**
 * Class LocatorGeneratorTest
 * @package Test\Net\Bazzline\Component\Locator
 */
class LocatorGeneratorTest extends LocatorTestCase
{
    public function testSetter()
    {
        $blockGeneratorFactory = $this->getBlockGeneratorFactory();
        $classGeneratorFactory = $this->getClassGeneratorFactory();
        $deleteStrategy = $this->getDeleteStrategy();
        $documentationGeneratorFactory = $this->getDocumentationGeneratorFactory();
        $fileGeneratorFactory = $this->getFileGeneratorFactory();
        $generator = $this->getLocatorGenerator();
        $methodGeneratorFactory = $this->getMethodGeneratorFactory();
        $propertyGeneratorFactory = $this->getPropertyGeneratorFactory();

        $this->assertEquals(
            $generator,
            $generator->setBlockGeneratorFactory($blockGeneratorFactory)
        );
        $this->assertEquals(
            $generator,
            $generator->setClassGeneratorFactory($classGeneratorFactory)
        );
        $this->assertEquals(
            $generator,
            $generator->setDocumentationGeneratorFactory($documentationGeneratorFactory)
        );
        $this->assertEquals(
            $generator,
            $generator->setFileExistsStrategy($deleteStrategy)
        );
        $this->assertEquals(
            $generator,
            $generator->setFileGeneratorFactory($fileGeneratorFactory)
        );
        $this->assertEquals(
            $generator,
            $generator->setMethodGeneratorFactory($methodGeneratorFactory)
        );
        $this->assertEquals(
            $generator,
            $generator->setPropertyGeneratorFactory($propertyGeneratorFactory)
        );
    }

    public function testGenerate()
    {
        $blockGeneratorFactory = $this->getBlockGeneratorFactory();
        $configuration = $this->getMockOfConfiguration();
        $classGeneratorFactory = $this->getClassGeneratorFactory();
        $deleteStrategy = $this->getDeleteStrategy();
        $documentationGeneratorFactory = $this->getDocumentationGeneratorFactory();
        $fileGeneratorFactory = $this->getFileGeneratorFactory();
        $generator = $this->getLocatorGenerator();
        $methodGeneratorFactory = $this->getMethodGeneratorFactory();
        $propertyGeneratorFactory = $this->getPropertyGeneratorFactory();
        $root = vfsStream::setup('root', 0777);

        $generator->setBlockGeneratorFactory($blockGeneratorFactory);
        $generator->setConfiguration($configuration);
        $generator->setClassGeneratorFactory($classGeneratorFactory);
        $generator->setDocumentationGeneratorFactory($documentationGeneratorFactory);
        $generator->setFileExistsStrategy($deleteStrategy);
        $generator->setFileGeneratorFactory($fileGeneratorFactory);
        $generator->setMethodGeneratorFactory($methodGeneratorFactory);
        $generator->setPropertyGeneratorFactory($propertyGeneratorFactory);

        $className = 'MyLocator';
        $extends = 'BaseLocator';
        $fileName = 'MyGenerator.php';
        $implements = array('MyInterface');

        $instance = $this->getInstance();
        $instance->setClassName('MyInstance');
        $instance->setAlias('MyInstanceAlias');
        $instance->setIsFactory(false);
        $instance->setIsShared(false);
        $instance->setMethodBodyBuilder($this->getNewInstanceBuilder());
        $instance->setReturnValue('MyInstance');
        $instances = array($instance);

        $methodPrefix = 'myMethodPrefix';
        $namespace = 'My\Namespace';

        $use = $this->getUses();
        $use->setAlias('AliasOfMyUse');
        $use->setClassName('My\Use');
        $uses = array($use);

        $configuration->shouldReceive('getClassName')
            ->times(2)
            ->andReturn($className);
        $configuration->shouldReceive('getExtends')
            ->once()
            ->andReturn($extends);
        $configuration->shouldReceive('getFileName')
            ->times(2)
            ->andReturn($fileName);
        $configuration->shouldReceive('getFilePath')
            ->times(2)
            ->andReturn($root->url());
        $configuration->shouldReceive('getImplements')
            ->once()
            ->andReturn($implements);
        $configuration->shouldReceive('getInstances')
            ->once()
            ->andReturn($instances);
        $configuration->shouldReceive('getMethodPrefix')
            ->once()
            ->andReturn($methodPrefix);
        $configuration->shouldReceive('getNamespace')
            ->times(2)
            ->andReturn($namespace);
        $configuration->shouldReceive('getUseCollection')
            ->once()
            ->andReturn($uses);
        $configuration->shouldReceive('hasExtends')
            ->once()
            ->andReturn(true);
        $configuration->shouldReceive('hasFactoryInstances')
            ->once()
            ->andReturn(false);
        $configuration->shouldReceive('hasImplements')
            ->once()
            ->andReturn(true);
        $configuration->shouldReceive('hasInstances')
            ->once()
            ->andReturn(true);
        $configuration->shouldReceive('hasNamespace')
            ->times(2)
            ->andReturn(true);
        $configuration->shouldReceive('hasSharedInstances')
            ->once()
            ->andReturn(false);
        $configuration->shouldReceive('hasUses')
            ->once()
            ->andReturn(true);

        $expectedFileContent = '<?php' . PHP_EOL .
            '/**' . PHP_EOL .
            ' * @author Net\Bazzline\Component\Locator' . PHP_EOL .
            ' * @since 2014-08-31' . PHP_EOL .
            ' */' . PHP_EOL .
            '' . PHP_EOL .
            'namespace My\Namespace;' . PHP_EOL .
            '' . PHP_EOL .
            'use My\Use as AliasOfMyUse;' . PHP_EOL .
            '' . PHP_EOL .
            '/**' . PHP_EOL .
            ' * Class MyLocator' . PHP_EOL .
            ' *' . PHP_EOL .
            ' * @package My\Namespace' . PHP_EOL .
            ' */' . PHP_EOL .
            'class MyLocator extends BaseLocator implements MyInterface' . PHP_EOL .
            '{' . PHP_EOL .
            '    /**' . PHP_EOL .
            '     * @return MyInstance' . PHP_EOL .
            '     */' . PHP_EOL .
            '    public function myMethodPrefixMyInstanceAlias()' . PHP_EOL .
            '    {' . PHP_EOL .
            '        return new MyInstance();' . PHP_EOL .
            '    }' . PHP_EOL .
            '}';

        $generator->generate();
        /** @var \org\bovigo\vfs\vfsStreamFile $expectedFile */
        $expectedFile = $root->getChild($fileName);

        $this->assertNotNull($expectedFile);
        $this->assertEquals(
            $expectedFileContent,
            $expectedFile->getContent()
        );
    }
}
