<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-14 
 */

namespace Net\Bazzline\Component\Locator;

/**
 * Interface GeneratorInterface
 * @package Net\Bazzline\Component\Locator
 */
interface GeneratorInterface
{
    /**
     * @throws RuntimeException
     */
    public function generate();
} 