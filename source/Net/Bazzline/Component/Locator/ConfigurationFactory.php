<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-22 
 */

namespace Net\Bazzline\Component\Locator;

use Net\Bazzline\Component\Locator\Configuration\Instance;
use Net\Bazzline\Component\Locator\Configuration\Uses;
use Net\Bazzline\Component\Locator\MethodBodyBuilder\FetchFromFactoryInstancePoolBuilder;
use Net\Bazzline\Component\Locator\MethodBodyBuilder\FetchFromSharedInstancePoolBuilder;
use Net\Bazzline\Component\Locator\MethodBodyBuilder\FetchFromSharedInstancePoolOrCreateByFactoryBuilder;
use Net\Bazzline\Component\Locator\MethodBodyBuilder\NewInstanceBuilder;

/**
 * Class ConfigurationFactory
 * @package Net\Bazzline\Component\Locator
 */
class ConfigurationFactory
{
    /**
     * @return Configuration
     */
    public function create()
    {
        $configuration = new Configuration();

        $configuration->setFetchFromFactoryInstancePoolBuilder(new FetchFromFactoryInstancePoolBuilder());
        $configuration->setFetchFromSharedInstancePoolBuilder(new FetchFromSharedInstancePoolBuilder());
        $configuration->setFetchFromSharedInstancePoolOrCreateByFactoryBuilder(new FetchFromSharedInstancePoolOrCreateByFactoryBuilder());
        $configuration->setNewInstanceBuilder(new NewInstanceBuilder());

        $configuration->setInstance(new Instance());
        $configuration->setUses(new Uses());

        return $configuration;
    }
} 