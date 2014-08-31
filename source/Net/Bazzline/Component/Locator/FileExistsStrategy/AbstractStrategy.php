<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-05-26 
 */

namespace Net\Bazzline\Component\Locator\FileExistsStrategy;

/**
 * Class AbstractStrategy
 * @package Net\Bazzline\Component\Locator\FileExistsStrategy
 */
abstract class AbstractStrategy implements FileExistsStrategyInterface
{
    /**
     * @var null|string
     */
    private $fileName;

    /**
     * @var null|string
     */
    private $filePath;

    /**
     * @param string $name
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setFileName($name)
    {
        $name = (string) $name;

        if ($name === '') {
            throw new InvalidArgumentException(
                'invalid filename given'
            );
        }
        $this->fileName = $name;

        return $this;
    }

    /**
     * @param string $path
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setFilePath($path)
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException(
                'provided path "' . $path . '" has to be a directory'
            );
        }
        if (!is_writable($path)) {
            throw new InvalidArgumentException(
                'provided path "' . $path . '" has to be writable'
            );
        }
        $this->filePath = $path;

        return $this;
    }

    /**
     * @return null|string
     * @throws RuntimeException
     */
    protected function getFileName()
    {
        if (is_null($this->fileName)) {
            throw new RuntimeException(
                'file name is mandatory'
            );
        }

        return $this->fileName;
    }

    /**
     * @return null|string
     * @throws RuntimeException
     */
    protected function getFilePath()
    {
        if (is_null($this->filePath)) {
            throw new RuntimeException(
                'file path is mandatory'
            );
        }

        return $this->filePath;
    }

    /**
     * @return $this
     */
    protected function resetFileName()
    {
        $this->fileName = null;

        return $this;
    }

    /**
     * @return $this
     */
    protected function resetFilePath()
    {
        $this->filePath = null;

        return $this;
    }
}