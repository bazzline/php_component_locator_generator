<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-05-26 
 */

namespace Net\Bazzline\Component\Locator\Configuration\Assembler;

use Net\Bazzline\Component\Locator\Configuration\Configuration;

/**
 * Interface AssemblerInterface
 * @package Net\Bazzline\Component\Locator\Configuration\Assembler
 */
interface AssemblerInterface
{
    /**
     * @param mixed $data
     * @param Configuration $configuration
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @return Configuration
     */
    public function assemble($data, Configuration $configuration);
} 