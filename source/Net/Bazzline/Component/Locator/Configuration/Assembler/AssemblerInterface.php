<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-05-26 
 */

namespace Net\Bazzline\Component\Locator\Configuration\Assembler;

use Net\Bazzline\Component\Locator\Configuration;

/**
 * Interface AssemblerInterface
 * @package Net\Bazzline\Component\Locator\Configuration\Assembler
 */
interface AssemblerInterface
{
    /**
     * @return Configuration
     * @throws RuntimeException
     */
    public function getConfiguration();

    /**
     * @param Configuration $configuration
     * @return $this
     */
    public function setConfiguration(Configuration $configuration);

    /**
     * @param mixed $data
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function assemble($data);
} 