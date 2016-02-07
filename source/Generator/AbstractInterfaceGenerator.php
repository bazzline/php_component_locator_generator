<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-12-23 
 */

namespace Net\Bazzline\Component\Locator\Generator;

use Net\Bazzline\Component\CodeGenerator\InterfaceGenerator;
use Net\Bazzline\Component\CodeGenerator\Factory\InterfaceGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\DocumentationGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\FileGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\MethodGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\FileGenerator;
use Net\Bazzline\Component\Locator\Configuration\Configuration;
use Net\Bazzline\Component\Locator\Generator\AbstractGenerator;
use Net\Bazzline\Component\Locator\RuntimeException;

/**
 * Class AbstractInterfaceGenerator
 * @package Net\Bazzline\Component\Locator
 */
abstract class AbstractInterfaceGenerator extends AbstractGenerator
{
    /** @var InterfaceGeneratorFactory */
    protected $interfaceGeneratorFactory;

    /** @var DocumentationGeneratorFactory */
    protected $documentationGeneratorFactory;

    /** @var FileGeneratorFactory */
    protected $fileGeneratorFactory;

    /** @var MethodGeneratorFactory */
    protected $methodGeneratorFactory;

    /**
     * @param \Net\Bazzline\Component\CodeGenerator\Factory\InterfaceGeneratorFactory $interfaceGeneratorFactory
     * @return $this
     */
    public function setInterfaceGeneratorFactory(InterfaceGeneratorFactory $interfaceGeneratorFactory)
    {
        $this->interfaceGeneratorFactory = $interfaceGeneratorFactory;

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
        $this->generateInterface(
            $this->getInterfaceName(),
            $this->configuration->getFileNameExtension()
        );
    }

    /**
     * @param string $name
     * @param InterfaceGenerator $interfaceGenerator
     * @param Configuration $configuration
     * @param DocumentationGeneratorFactory $documentationGeneratorFactory
     * @param MethodGeneratorFactory $methodGeneratorFactory
     * @return InterfaceGenerator
     */
    abstract protected function createInterface($name, InterfaceGenerator $interfaceGenerator, Configuration $configuration, DocumentationGeneratorFactory $documentationGeneratorFactory, MethodGeneratorFactory $methodGeneratorFactory);

    /**
     * @return string
     */
    abstract protected function getInterfaceName();

    /**
     * @param string $name
     * @param string $extension
     * @throws RuntimeException
     */
    protected function generateInterface($name, $extension)
    {
        $fileName = $name . $extension;
        $this->moveOldFileIfExists($this->configuration->getFilePath(), $fileName);

        $fileGenerator = $this->createFile($this->fileGeneratorFactory->create());
        $interfaceGenerator = $this->createInterface(
            $name,
            $this->interfaceGeneratorFactory->create(),
            $this->configuration,
            $this->documentationGeneratorFactory,
            $this->methodGeneratorFactory
        );

        $fileGenerator->addInterface($interfaceGenerator);
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
                ' * @author ' . $this->getAuthorString(),
                ' * @since ' . date('Y-m-d'),
                ' */'
            )
        );

        return $fileGenerator;
    }
}