<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-14 
 */

namespace Net\Bazzline\Component\Locator;

use Net\Bazzline\Component\Locator\FileExistsStrategy\FileExistsStrategyInterface;

/**
 * Class AbstractGenerator
 * @package Net\Bazzline\Component\Locator
 */
abstract class AbstractGenerator implements GeneratorInterface
{
    /**
     * @var \Net\Bazzline\Component\Locator\Configuration
     */
    protected $configuration;

    /**
     * @var FileExistsStrategyInterface
     */
    protected $fileExistsStrategy;

    /**
     * @param \Net\Bazzline\Component\Locator\Configuration $configuration
     * @return $this
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @param FileExistsStrategyInterface $strategy
     * @return $this
     */
    public function setFileExistsStrategy(FileExistsStrategyInterface $strategy)
    {
        $this->fileExistsStrategy = $strategy;

        return $this;
    }

    /**
     * @param string $filePath
     * @param string $fileName
     * @throws RuntimeException
     */
    protected function moveOldFileIfExists($filePath, $fileName)
    {
        $fullQualifiedFilePath = $filePath . DIRECTORY_SEPARATOR . $fileName;

        if (file_exists($fullQualifiedFilePath)) {
            if ($this->fileExistsStrategy instanceof FileExistsStrategyInterface) {
                $this->fileExistsStrategy
                    ->setFileName($fileName)
                    ->setFilePath($filePath)
                    ->execute();
            } else {
                throw new RuntimeException(
                    'file "' . $fullQualifiedFilePath . '" already exists'
                );
            }
        }
    }

    /**
     * @param string $fullQualifiedFileName
     * @param string $content
     * @throws RuntimeException
     */
    protected function dumpToFile($fullQualifiedFileName, $content)
    {
        if (file_put_contents($fullQualifiedFileName, $content) === false) {
            throw new RuntimeException(
                'can not create "' . $fullQualifiedFileName . '" or write content'
            );
        }
    }
}