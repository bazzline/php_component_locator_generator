<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-05-26 
 */

namespace Net\Bazzline\Component\Locator;

use Net\Bazzline\Component\Locator\Configuration\Instance;
use Net\Bazzline\Component\Locator\Configuration\Uses;
use Net\Bazzline\Component\Locator\MethodBodyBuilder\FetchFromFactoryInstancePoolBuilder;
use Net\Bazzline\Component\Locator\MethodBodyBuilder\FetchFromSharedInstancePoolBuilder;
use Net\Bazzline\Component\Locator\MethodBodyBuilder\FetchFromSharedInstancePoolOrCreateByFactoryBuilder;
use Net\Bazzline\Component\Locator\MethodBodyBuilder\MethodBodyBuilderInterface;
use Net\Bazzline\Component\Locator\MethodBodyBuilder\NewInstanceBuilder;

/**
 * Interface Configuration
 * @package Net\Bazzline\Component\Locator\Configuration\Assembler
 */
class Configuration
{
    /**
     * @var string
     */
    private $extends;

    /**
     * @var array
     */
    private $instances = array();

    /**
     * @var array
     */
    private $implements = array();

    /**
     * @var string
     */
    private $className;

    /**
     * @var FetchFromFactoryInstancePoolBuilder
     */
    private $fetchFromFactoryInstancePoolBuilder;

    /**
     * @var FetchFromSharedInstancePoolBuilder
     */
    private $fetchFromSharedInstancePoolBuilder;

    /**
     * @var FetchFromSharedInstancePoolOrCreateByFactoryBuilder
     */
    private $fetchFromSharedInstancePoolOrCreateByFactoryBuilder;

    /**
     * @var bool
     */
    private $hasFactoryInstances = false;

    /**
     * @var bool
     */
    private $hasSharedInstances = false;

    /**
     * @var Instance
     */
    private $instance;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var NewInstanceBuilder
     */
    private $newInstanceBuilder;

    /**
     * @var string
     */
    private $methodPrefix;

    /**
     * @var array
     */
    private $methodBodyBuilderInstancePool = array();

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var array
     */
    private $useCollection = array();

    /**
     * @var Uses
     */
    private $uses;

    /**
     * @return null|string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setClassName($name)
    {
        $this->className = (string) $name;
        $this->fileName = $this->className . '.php';

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return null|string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setFilePath($path)
    {
        $this->filePath = (string) $path;

        return $this;
    }

    /**
     * @param Instance $instance
     * @return $this
     */
    public function setInstance(Instance $instance)
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * @param Uses $uses
     * @return $this
     */
    public function setUses(Uses $uses)
    {
        $this->uses = $uses;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return bool
     */
    public function hasNamespace()
    {
        return (is_string($this->namespace));
    }

    /**
     * @param string $namespace
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $namespace = trim((string) $namespace);

        if (strlen($namespace) > 0) {
            $this->namespace = $namespace;
        }

        return $this;
    }

    /**
     * @param string $methodPrefix
     * @return $this
     */
    public function setMethodPrefix($methodPrefix)
    {
        $this->methodPrefix = (string) $methodPrefix;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMethodPrefix()
    {
        return $this->methodPrefix;
    }

    /**
     * @param string $className
     * @param bool $isFactory
     * @param bool $isShared
     * @param string $returnValue
     * @param string $alias
     * @param null|string $methodBodyBuilderClassName
     * @return $this
     * @throws RuntimeException
     */
    public function addInstance($className, $isFactory, $isShared, $returnValue, $alias, $methodBodyBuilderClassName = null)
    {
        $instance = $this->getNewInstance();

        if ($isFactory) {
            $this->hasFactoryInstances = true;
        }

        if ($isShared) {
            $this->hasSharedInstances = true;
        }

        $instance->setAlias($alias);
        $instance->setClassName($className);
        $instance->setIsFactory($isFactory);
        $instance->setIsShared($isShared);
        $instance->setReturnValue($returnValue);

        if (!is_null($methodBodyBuilderClassName)) {
            $methodBodyBuilder = $this->fetchFromMethodBodyBuilderInstancePool($methodBodyBuilderClassName);
            $methodBodyBuilder->setInstance($instance);
            $instance->setMethodBodyBuilder($methodBodyBuilder);
        } else {
            $instance = $this->tryToDetermineMethodBodyBuilder($instance);
        }

        $this->instances[] = $instance;

        return $this;
    }

    /**
     * @return array|Instance[]
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     * @return boolean
     */
    public function hasInstances()
    {
        return (!empty($this->instances));
    }

    /**
     * @return bool
     */
    public function hasFactoryInstances()
    {
        return $this->hasFactoryInstances;
    }

    /**
     * @return bool
     */
    public function hasSharedInstances()
    {
        return $this->hasSharedInstances;
    }

    /**
     * @param string $interfaceName
     * @return $this
     */
    public function addImplements($interfaceName)
    {
        $this->implements[] = $interfaceName;

        return $this;
    }

    /**
     * @return array
     */
    public function getImplements()
    {
        return $this->implements;
    }

    /**
     * @return boolean
     */
    public function hasImplements()
    {
        return (!empty($this->implements));
    }

    /**
     * @param string $className
     * @return $this
     */
    public function setExtends($className)
    {
        $this->extends = (string) $className;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasExtends()
    {
        return (is_string($this->extends));
    }

    /**
     * @return null|string
     */
    public function getExtends()
    {
        return $this->extends;
    }

    /**
     * @param string $className
     * @param string $alias
     * @return $this
     */
    public function addUses($className, $alias = '')
    {
        $uses = $this->getNewUses();

        $uses->setAlias($alias);
        $uses->setClassName($className);

        $this->useCollection[] = $uses;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasUses()
    {
        return (!empty($this->useCollection));
    }

    /**
     * @return array|Uses[]
     */
    public function getUseCollection()
    {
        return $this->useCollection;
    }

    /**
     * @param FetchFromFactoryInstancePoolBuilder $fetchFromFactoryInstancePoolBuilder
     * @return $this
     */
    public function setFetchFromFactoryInstancePoolBuilder(FetchFromFactoryInstancePoolBuilder $fetchFromFactoryInstancePoolBuilder)
    {
        $this->fetchFromFactoryInstancePoolBuilder = $fetchFromFactoryInstancePoolBuilder;

        return $this;
    }

    /**
     * @param FetchFromSharedInstancePoolBuilder $fetchFromSharedInstancePoolBuilder
     * @return $this
     */
    public function setFetchFromSharedInstancePoolBuilder(FetchFromSharedInstancePoolBuilder $fetchFromSharedInstancePoolBuilder)
    {
        $this->fetchFromSharedInstancePoolBuilder = $fetchFromSharedInstancePoolBuilder;

        return $this;
    }

    /**
     * @param FetchFromSharedInstancePoolOrCreateByFactoryBuilder $fetchFromSharedInstancePoolOrCreateByFactoryBuilder
     * @return $this
     */
    public function setFetchFromSharedInstancePoolOrCreateByFactoryBuilder(FetchFromSharedInstancePoolOrCreateByFactoryBuilder $fetchFromSharedInstancePoolOrCreateByFactoryBuilder)
    {
        $this->fetchFromSharedInstancePoolOrCreateByFactoryBuilder = $fetchFromSharedInstancePoolOrCreateByFactoryBuilder;

        return $this;
    }

    /**
     * @param NewInstanceBuilder $newInstanceBuilder
     * @return $this
     */
    public function setNewInstanceBuilder(NewInstanceBuilder $newInstanceBuilder)
    {
        $this->newInstanceBuilder = $newInstanceBuilder;

        return $this;
    }

    /**
     * @return Uses
     */
    private function getNewUses()
    {
        return clone $this->uses;
    }

    /**
     * @return Instance
     */
    private function getNewInstance()
    {
        return clone $this->instance;
    }

    /**
     * @param Instance $instance
     * @return Instance
     * @throws RuntimeException
     */
    private function tryToDetermineMethodBodyBuilder(Instance $instance)
    {
        $isUniqueInvokableInstance = ((!$instance->isFactory()) && (!$instance->isShared()));
        $isUniqueInvokableFactorizedInstance = (($instance->isFactory()) && (!$instance->isShared()));
        $isSharedInvokableInstance = ((!$instance->isFactory()) && ($instance->isShared()));
        $isSharedInvokableFactorizedInstance = (($instance->isFactory()) && ($instance->isShared()));

        if ($isUniqueInvokableInstance) {
            $instance->setMethodBodyBuilder($this->newInstanceBuilder);
        } else if ($isUniqueInvokableFactorizedInstance) {
            $instance->setMethodBodyBuilder($this->fetchFromFactoryInstancePoolBuilder);
        } else if ($isSharedInvokableInstance) {
            $instance->setMethodBodyBuilder($this->fetchFromSharedInstancePoolBuilder);
        } else if ($isSharedInvokableFactorizedInstance) {
            $instance->setMethodBodyBuilder($this->fetchFromSharedInstancePoolOrCreateByFactoryBuilder);
        } else {
            throw new RuntimeException(
                'could not determine method body builder for instance.' . PHP_EOL .
                'please set a method_body_builder for following instance: ' . PHP_EOL .
                var_export($instance, true)
            );
        }

        return $instance;
    }

    /**
     * @param $methodBodyBuilderClassName
     * @return \Net\Bazzline\Component\Locator\MethodBodyBuilder\MethodBodyBuilderInterface
     * @throws RuntimeException
     */
    private function fetchFromMethodBodyBuilderInstancePool($methodBodyBuilderClassName)
    {
        $key = sha1($methodBodyBuilderClassName);

        if (!isset($this->methodBodyBuilderInstancePool[$key])) {
            if (class_exists($methodBodyBuilderClassName)) {
                $methodBodyBuilder = new $methodBodyBuilderClassName();
                if (!$methodBodyBuilder instanceof MethodBodyBuilderInterface) {
                    throw new RuntimeException(
                        'provided method body builder class name "' . $methodBodyBuilderClassName .
                        '" does not implements MethodBodyBuilderInterface'
                    );
                }
                $this->methodBodyBuilderInstancePool[$key] = $methodBodyBuilder;
            } else {
                throw new RuntimeException(
                    'provided method_body_builder_class_name "' . $methodBodyBuilderClassName . '" does not exists'
                );
            }
        }

        return $this->methodBodyBuilderInstancePool[$key];
    }
}