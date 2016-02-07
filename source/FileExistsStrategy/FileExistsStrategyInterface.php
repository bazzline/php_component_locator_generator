<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-05-26 
 */

namespace Net\Bazzline\Component\Locator\FileExistsStrategy;

/**
 * Class FileExistsStrategyInterface
 * @package Net\Bazzline\Component\Locator\FileExistsStrategy
 */
interface FileExistsStrategyInterface
{
    /**
     * @throws RuntimeException
     */
    public function execute();

    /**
     * @param string $name
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setFileName($name);

    /**
     * @param string $path
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setFilePath($path);
}