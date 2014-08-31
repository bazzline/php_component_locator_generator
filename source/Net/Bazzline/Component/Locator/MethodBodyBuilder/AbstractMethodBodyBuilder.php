<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-12 
 */

namespace Net\Bazzline\Component\Locator\MethodBodyBuilder;

use Net\Bazzline\Component\CodeGenerator\DocumentationGenerator;
use Net\Bazzline\Component\Locator\Configuration\Instance;

/**
 * Class AbstractMethodBodyBuilder
 * @package Net\Bazzline\Component\Locator\MethodBodyBuilder
 */
abstract class AbstractMethodBodyBuilder implements MethodBodyBuilderInterface
{
    /**
     * @var Instance
     */
    protected $instance;

    public function __clone()
    {
        $this->instance = null;
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
     * @param DocumentationGenerator $documentation
     * @return DocumentationGenerator
     */
    public function extend(DocumentationGenerator $documentation)
    {
        return $documentation;
    }

    /**
     * @param array $propertyNames
     * @throws RuntimeException
     */
    protected function assertMandatoryProperties(array $propertyNames = array('instance'))
    {
        foreach ($propertyNames as $propertyName) {
            if (!isset($this->$propertyName)
                || (is_null($this->$propertyName))) {
                throw new RuntimeException(
                    'property "' . $propertyName . '" is mandatory'
                );
            }
        }
    }
}