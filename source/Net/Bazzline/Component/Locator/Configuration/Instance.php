<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-07 
 */

namespace Net\Bazzline\Component\Locator\Configuration;

use Net\Bazzline\Component\Locator\MethodBodyBuilder\MethodBodyBuilderInterface;

/**
 * Class Instance
 * @package Net\Bazzline\Component\Locator\Configuration
 */
class Instance
{
    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $className;

    /**
     * @var boolean
     */
    private $isFactory = false;

    /**
     * @var boolean
     */
    private $isShared = true;

    /**
     * @var MethodBodyBuilderInterface
     */
    private $methodBodyBuilder;

    /**
     * @var string
     */
    private $returnValue;

    /**
     * @param string $alias
     * @return $this
     */
    public function setAlias($alias)
    {
        $alias = trim((string) $alias);

        if (strlen($alias) > 0) {
            $this->alias = (string) $alias;
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return bool
     */
    public function hasAlias()
    {
        return (is_string($this->alias));
    }

    /**
     * @param string $className
     * @return $this
     */
    public function setClassName($className)
    {
        $this->className = (string) $className;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return null|boolean
     */
    public function isFactory()
    {
        return $this->isFactory;
    }

    /**
     * @param boolean $isFactory
     * @return $this
     */
    public function setIsFactory($isFactory)
    {
        $this->isFactory = (boolean) $isFactory;

        return $this;
    }

    /**
     * @return null|boolean
     */
    public function isShared()
    {
        return $this->isShared;
    }

    /**
     * @param boolean $isShared
     * @return $this
     */
    public function setIsShared($isShared)
    {
        $this->isShared = (boolean) $isShared;

        return $this;
    }

    /**
     * @param \Net\Bazzline\Component\Locator\MethodBodyBuilder\MethodBodyBuilderInterface $methodBodyBuilder
     */
    public function setMethodBodyBuilder(MethodBodyBuilderInterface $methodBodyBuilder)
    {
        $this->methodBodyBuilder = $methodBodyBuilder;
        $this->methodBodyBuilder->setInstance($this);
    }

    /**
     * @return \Net\Bazzline\Component\Locator\MethodBodyBuilder\MethodBodyBuilderInterface
     */
    public function getMethodBodyBuilder()
    {
        return $this->methodBodyBuilder;
    }

    /**
     * @return string
     */
    public function getReturnValue()
    {
        return $this->returnValue;
    }

    /**
     * @return bool
     */
    public function hasReturnValue()
    {
        return (!is_null($this->returnValue));
    }

    /**
     * @param string $returnValue
     * @return $this
     */
    public function setReturnValue($returnValue)
    {
        if ((is_string($returnValue))
            && (strlen($returnValue) > 0)) {
            $this->returnValue = $returnValue;
        }

        return $this;
    }
}
