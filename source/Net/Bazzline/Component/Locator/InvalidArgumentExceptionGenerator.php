<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-17 
 */

namespace Net\Bazzline\Component\Locator;

use Net\Bazzline\Component\CodeGenerator\ClassGenerator;
use Net\Bazzline\Component\CodeGenerator\Factory\ClassGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\DocumentationGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\FileGenerator;
use Net\Bazzline\Component\CodeGenerator\Factory\FileGeneratorFactory;

/**
 * Class InvalidArgumentExceptionGenerator
 * @package Net\Bazzline\Component\Locator
 */
class InvalidArgumentExceptionGenerator extends AbstractGenerator
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
     * @throws RuntimeException
     */
    public function generate()
    {
        $fileName = 'InvalidArgumentException.php';
        $this->moveOldFileIfExists($this->configuration->getFilePath(), $fileName);

        $fileGenerator = $this->createFile($this->fileGeneratorFactory->create());
        $classGenerator = $this->createClass(
            $this->classGeneratorFactory->create(),
            $this->configuration,
            $this->documentationGeneratorFactory
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
     * @return ClassGenerator
     */
    private function createClass(ClassGenerator $classGenerator, Configuration $configuration, DocumentationGeneratorFactory $documentationGeneratorFactory)
    {
        $classGenerator->setDocumentation($documentationGeneratorFactory->create());
        $classGenerator->setName('InvalidArgumentException');

        if ($configuration->hasNamespace()) {
            $classGenerator->setNamespace($configuration->getNamespace());
        }

        $classGenerator->addUse('InvalidArgumentException', 'ParentClass');
        $classGenerator->setExtends('ParentClass');

        return $classGenerator;
    }
}