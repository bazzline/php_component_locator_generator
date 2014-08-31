<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-08-07 
 */

namespace Test\Net\Bazzline\Component\Locator;

use org\bovigo\vfs\vfsStream;

/**
 * Class FactoryInterfaceGeneratorTest
 * @package Test\Net\Bazzline\Component\Locator
 */
class FactoryInterfaceGeneratorTest extends LocatorTestCase
{
    public function testSetters()
    {
        $classGeneratorFactory = $this->getClassGeneratorFactory();
        $documentationGeneratorFactory = $this->getDocumentationGeneratorFactory();
        $fileGeneratorFactory = $this->getFileGeneratorFactory();
        $generator = $this->getFactoryInterfaceGenerator();
        $methodGeneratorFactory = $this->getMethodGeneratorFactory();

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
            $generator->setFileGeneratorFactory($fileGeneratorFactory)
        );
        $this->assertEquals(
            $generator,
            $generator->setMethodGeneratorFactory($methodGeneratorFactory)
        );
    }

    public function testGenerate()
    {
        $className = 'MyClass';
        $classGeneratorFactory = $this->getClassGeneratorFactory();
        $configuration = $this->getMockOfConfiguration();
        $documentationGeneratorFactory = $this->getDocumentationGeneratorFactory();
        $fileGeneratorFactory = $this->getFileGeneratorFactory();
        $generator = $this->getFactoryInterfaceGenerator();
        $methodGeneratorFactory = $this->getMethodGeneratorFactory();
        $namespace = 'My\Namespace';
        $root = vfsStream::setup('root', 0777);
        $strategy = $this->getDeleteStrategy();

        $generator->setConfiguration($configuration);
        $generator->setClassGeneratorFactory($classGeneratorFactory);
        $generator->setDocumentationGeneratorFactory($documentationGeneratorFactory);
        $generator->setFileGeneratorFactory($fileGeneratorFactory);
        $generator->setFileExistsStrategy($strategy);
        $generator->setMethodGeneratorFactory($methodGeneratorFactory);

        $configuration->shouldReceive('getClassName')
            ->once()
            ->andReturn($className);
        $configuration->shouldReceive('getFilePath')
            ->twice()
            ->andReturn($root->url());
        $configuration->shouldReceive('getNamespace')
            ->once()
            ->andReturn($namespace);
        $configuration->shouldReceive('hasNamespace')
            ->once()
            ->andReturn(true);

        $expectedFileContent = '<?php' . PHP_EOL .
            '/**' . PHP_EOL .
            ' * @author Net\Bazzline\Component\Locator' . PHP_EOL .
            ' * @since ' . date('Y-m-d') . PHP_EOL .
            ' */' . PHP_EOL .
            '' . PHP_EOL .
            'namespace ' . $namespace . ';' . PHP_EOL .
            '' . PHP_EOL .
            '/**' . PHP_EOL .
            ' * Class FactoryInterface' . PHP_EOL .
            ' *' . PHP_EOL .
            ' * @package ' . $namespace . PHP_EOL .
            ' */' . PHP_EOL .
            'interface FactoryInterface' . PHP_EOL .
            '{' . PHP_EOL .
            '    /**' . PHP_EOL .
            '     * @param ' . $className . ' $locator' . PHP_EOL .
            '     * @return $this' . PHP_EOL .
            '     */' . PHP_EOL .
            '    public function setLocator(' . $className . ' $locator);' . PHP_EOL .
            '' . PHP_EOL .
            '    /**' . PHP_EOL .
            '     * @return null|object' . PHP_EOL .
            '     */' . PHP_EOL .
            '    public function create();' . PHP_EOL .
            '}';

        $generator->generate();
        /** @var \org\bovigo\vfs\vfsStreamFile $expectedFile */
        $expectedFile = $root->getChild('FactoryInterface.php');

        $this->assertNotNull($expectedFile);
        $this->assertEquals(
            $expectedFileContent,
            $expectedFile->getContent()
        );
    }
} 