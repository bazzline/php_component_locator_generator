<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-12 
 */

namespace Net\Bazzline\Component\Locator\MethodBodyBuilder;

use Net\Bazzline\Component\CodeGenerator\BlockGenerator;
use Net\Bazzline\Component\CodeGenerator\DocumentationGenerator;
use Net\Bazzline\Component\Locator\Configuration\Instance;

/**
 * Interface MethodBodyBuilderInterface
 * @package Net\Bazzline\Component\Locator\MethodBodyBuilder
 */
interface MethodBodyBuilderInterface
{
    /**
     * @param Instance $instance
     * @return $this
     */
    public function setInstance(Instance $instance);

    /**
     * @param BlockGenerator $body
     * @return BlockGenerator
     * @throws RuntimeException
     */
    public function build(BlockGenerator $body);

    /**
     * @param DocumentationGenerator $documentation
     * @return DocumentationGenerator
     */
    public function extend(DocumentationGenerator $documentation);
} 