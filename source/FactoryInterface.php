<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-02 
 */

namespace Net\Bazzline\Component\Locator;

/**
 * Interface FactoryInterface
 * @package Net\Bazzline\Component\Locator
 */
interface FactoryInterface
{
    /**
     * @param LocatorInterface $locator
     * @return $this
     */
    public function setLocator(LocatorInterface $locator);

    /**
     * @return mixed
     */
    public function create();
} 