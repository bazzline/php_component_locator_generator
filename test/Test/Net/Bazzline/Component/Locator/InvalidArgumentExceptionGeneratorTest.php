<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-08-07 
 */

namespace Test\Net\Bazzline\Component\Locator;

use org\bovigo\vfs\vfsStream;

/**
 * Class InvalidArgumentExceptionGeneratorTest
 * @package Test\Net\Bazzline\Component\Locator
 */
class InvalidArgumentExceptionGeneratorTest extends LocatorTestCase
{
    public function testSetters()
    {
        $classGeneratorFactory = $this->getClassGeneratorFactory();
        $documentationGeneratorFactory = $this->getDocumentationGeneratorFactory();
        $fileGeneratorFactory = $this->getFileGeneratorFactory();
        $generator = $this->getInvalidArgumentExceptionGenerator();

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
    }

    public function testGenerate()
    {
        $classGeneratorFactory = $this->getClassGeneratorFactory();
        $configuration = $this->getMockOfConfiguration();
        $documentationGeneratorFactory = $this->getDocumentationGeneratorFactory();
        $fileGeneratorFactory = $this->getFileGeneratorFactory();
        $generator = $this->getInvalidArgumentExceptionGenerator();
        $namespace = 'My\Namespace';
        $root = vfsStream::setup('root', 0777);
        $strategy = $this->getDeleteStrategy();

        $generator->setConfiguration($configuration);
        $generator->setClassGeneratorFactory($classGeneratorFactory);
        $generator->setDocumentationGeneratorFactory($documentationGeneratorFactory);
        $generator->setFileGeneratorFactory($fileGeneratorFactory);
        $generator->setFileExistsStrategy($strategy);

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
            'use InvalidArgumentException as ParentClass;' . PHP_EOL .
            '' . PHP_EOL .
            '/**' . PHP_EOL .
            ' * Class InvalidArgumentException' . PHP_EOL .
            ' *' . PHP_EOL .
            ' * @package ' . $namespace . PHP_EOL .
            ' */' . PHP_EOL .
            'class InvalidArgumentException extends ParentClass' . PHP_EOL .
            '{' . PHP_EOL .
            '}';

        $generator->generate();
        /** @var \org\bovigo\vfs\vfsStreamFile $expectedFile */
        $expectedFile = $root->getChild('InvalidArgumentException.php');

        $this->assertNotNull($expectedFile);
        $this->assertEquals(
            $expectedFileContent,
            $expectedFile->getContent()
        );
    }
}