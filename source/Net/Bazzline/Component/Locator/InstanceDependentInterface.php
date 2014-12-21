<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-12-21 
 */

namespace Net\Bazzline\Component\Locator;

use Net\Bazzline\Component\Locator\Configuration\Instance;

/**
 * Interface InstanceDependentInterface
 * @package Net\Bazzline\Component\Locator
 */
interface InstanceDependentInterface
{
    /**
     * @param Instance $instance
     * @return $this
     */
    public function setInstance(Instance $instance);
}