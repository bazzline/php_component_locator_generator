<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-17 
 */

namespace Net\Bazzline\Component\Locator;

use Net\Bazzline\Component\CodeGenerator\ClassGenerator;
use Net\Bazzline\Component\CodeGenerator\Factory\ClassGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\DocumentationGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\FileGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\MethodGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\FileGenerator;

/**
 * Class FactoryInterfaceGenerator
 * @package Net\Bazzline\Component\Locator
 */
class FactoryInterfaceGenerator extends AbstractGenerator
{
    /**
     * @var ClassGeneratorFactory
     */
    private $classGeneratorFactory;

    /**
     * @var DocumentationGeneratorFactory
     */
    private $documentationGeneratorFactory;

    /**
     * @var FileGeneratorFactory
     */
    private $fileGeneratorFactory;

    /**
     * @var MethodGeneratorFactory
     */
    private $methodGeneratorFactory;

    /**
     * @param \Net\Bazzline\Component\CodeGenerator\Factory\ClassGeneratorFactory $classGeneratorFactory
     * @return $this
     */
    public function setClassGeneratorFactory(ClassGeneratorFactory $classGeneratorFactory)
    {
        $this->classGeneratorFactory = $classGeneratorFactory;

        return $this;
    }

    /**
     * @param \Net\Bazzline\Component\CodeGenerator\Factory\DocumentationGeneratorFactory $documentationGeneratorFactory
     * @return $this
     */
    public function setDocumentationGeneratorFactory(DocumentationGeneratorFactory $documentationGeneratorFactory)
    {
        $this->documentationGeneratorFactory = $documentationGeneratorFactory;

        return $this;
    }

    /**
     * @param \Net\Bazzline\Component\CodeGenerator\Factory\FileGeneratorFactory $fileGeneratorFactory
     * @return $this
     */
    public function setFileGeneratorFactory(FileGeneratorFactory $fileGeneratorFactory)
    {
        $this->fileGeneratorFactory = $fileGeneratorFactory;

        return $this;
    }

    /**
     * @param \Net\Bazzline\Component\CodeGenerator\Factory\MethodGeneratorFactory $methodGeneratorFactory
     * @return $this
     */
    public function setMethodGeneratorFactory(MethodGeneratorFactory $methodGeneratorFactory)
    {
        $this->methodGeneratorFactory = $methodGeneratorFactory;

        return $this;
    }

    /**
     * @throws RuntimeException
     */
    public function generate()
    {
        $fileName = 'FactoryInterface.php';
        $this->moveOldFileIfExists($this->configuration->getFilePath(), $fileName);

        $fileGenerator = $this->createFile($this->fileGeneratorFactory->create());
        $classGenerator = $this->createClass(
            $this->classGeneratorFactory->create(),
            $this->configuration,
            $this->documentationGeneratorFactory,
            $this->methodGeneratorFactory
        );

        $fileGenerator->addClass($classGenerator);
        $fileContent = $fileGenerator->generate();

        $fullQualifiedPathName = $this->configuration->getFilePath() .
            DIRECTORY_SEPARATOR . $fileName;
        $this->dumpToFile($fullQualifiedPathName, $fileContent);
    }

    /**
     * @param FileGenerator $fileGenerator
     * @return FileGenerator
     */
    private function createFile(FileGenerator $fileGenerator)
    {
        $fileGenerator->addFileContent(
            array(
                '/**',
                ' * @author Net\Bazzline\Component\Locator',
                ' * @since ' . date('Y-m-d'),
                ' */'
            )
        );

        return $fileGenerator;
    }

    /**
     * @param ClassGenerator $classGenerator
     * @param Configuration $configuration
     * @param DocumentationGeneratorFactory $documentationGeneratorFactory
     * @param MethodGeneratorFactory $methodGeneratorFactory
     * @return ClassGenerator
     */
    private function createClass(ClassGenerator $classGenerator, Configuration $configuration, DocumentationGeneratorFactory $documentationGeneratorFactory, MethodGeneratorFactory $methodGeneratorFactory)
    {
        $classGenerator->markAsInterface();
        $classGenerator->setDocumentation($documentationGeneratorFactory->create());
        $classGenerator->setName('FactoryInterface');

        if ($configuration->hasNamespace()) {
            $classGenerator->setNamespace($configuration->getNamespace());
        }

        $setLocator = $methodGeneratorFactory->create();
        $setLocator->setDocumentation($documentationGeneratorFactory->create());
        $setLocator->setName('setLocator');
        $setLocator->addParameter('locator', null, $configuration->getClassName());
        $setLocator->markAsPublic();
        $setLocator->markAsHasNoBody();
        $setLocator->getDocumentation()->setReturn(array('$this'));

        $create = $methodGeneratorFactory->create();
        $create->setDocumentation($documentationGeneratorFactory->create());
        $create->setName('create');
        $create->markAsPublic();
        $create->markAsHasNoBody();
        $create->getDocumentation()->setReturn(array('null', 'object'));

        $classGenerator->addMethod($setLocator);
        $classGenerator->addMethod($create);

        return $classGenerator;
    }
}