<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-05-26
 */

namespace Net\Bazzline\Component\Locator\Configuration\Assembler;

use Net\Bazzline\Component\Locator\Configuration;

/**
 * Class AbstractAssembler
 * @package Net\Bazzline\Component\Locator\Configuration\Assembler
 */
abstract class AbstractAssembler implements AssemblerInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @return Configuration
     * @throws RuntimeException
     */
    final public function getConfiguration()
    {
        if (is_null($this->configuration)) {
            throw new RuntimeException(
                'configuration is mandatory'
            );
        }

        return $this->configuration;
    }

    /**
     * @param Configuration $configuration
     * @return $this
     */
    final public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @param mixed $data
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    final public function assemble($data)
    {
        $this->assertMandatoryProperties();
        $this->validateData($data);
        $this->map($data);
    }

    /**
     * @param mixed $data
     * @throws RuntimeException
     */
    abstract protected function map($data);

    /**
     * @param mixed $data
     * @throws InvalidArgumentException
     */
    abstract protected function validateData($data);

    /**
     * @param array $data
     * @param array $keysToExpectedValueType
     * @throws InvalidArgumentException
     */
    final protected function validateDataWithMandatoryKeysAndExpectedValueType(array $data, array $keysToExpectedValueType)
    {
        foreach ($keysToExpectedValueType as $key => $expectedType) {
            if (!isset($data[$key])) {
                throw new InvalidArgumentException(
                    'data array must contain content for key "' . $key . '"'
                );
            }
            $exceptionMessage = 'value of key "' . $key . '" must be of type "' . $expectedType . '"';

            switch ($expectedType) {
                case 'array':
                    if (!is_array($data[$key])) {
                        throw new InvalidArgumentException(
                            $exceptionMessage
                        );
                    }
                    break;
                case 'string':
                    if (!is_string($data[$key])) {
                        throw new InvalidArgumentException(
                            $exceptionMessage
                        );
                    }
                    break;
            }
        }
    }

    /**
     * @param array $data
     * @param array $keysToExpectedValueType
     * @throws InvalidArgumentException
     */
    final protected function validateDataWithOptionalKeysAndExpectedValueTypeOrSetExpectedValueAsDefault(array $data, array $keysToExpectedValueType)
    {
        foreach ($keysToExpectedValueType as $key => $expectedType) {
            if (isset($data[$key])) {
                $exceptionMessage = 'value of key "' . $key . '" must be of type "' . $expectedType . '" when set';

                switch ($expectedType) {
                    case 'array':
                        if (!is_array($data[$key])) {
                            throw new InvalidArgumentException(
                                $exceptionMessage
                            );
                        }
                        break;
                    case 'string':
                        if (!is_string($data[$key])) {
                            throw new InvalidArgumentException(
                                $exceptionMessage
                            );
                        }
                        break;
                }
            }
        }
    }

    /**
     * @throws RuntimeException
     */
    private function assertMandatoryProperties()
    {
        $propertyNames = array(
            'configuration',
        );

        foreach ($propertyNames as $propertyName) {
            if (is_null($this->configuration)) {
                throw new RuntimeException(
                    $propertyName . ' is mandatory'
                );
            }
        }
    }
}