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
     * @param mixed $data
     * @param Configuration $configuration
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @return Configuration
     */
    final public function assemble($data, Configuration $configuration)
    {
        $this->validateData($data);
        $configuration = $this->map($data, $configuration);

        return $configuration;
    }

    /**
     * @param mixed $data
     * @param Configuration $configuration
     * @return Configuration
     * @throws RuntimeException
     */
    abstract protected function map($data, Configuration $configuration);

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

            $this->validateExpectedDataKeyType($data, $key, $expectedType);
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
                $this->validateExpectedDataKeyType($data, $key, $expectedType);
            }
        }
    }

    /**
     * @param array $data
     * @param Configuration $configuration
     * @return Configuration
     */
    protected function mapBooleanProperties(array $data, Configuration $configuration)
    {
        if (isset($data['create_interface'])) {
            $configuration->setCreateLocatorGeneratorInterface($data['create_interface']);
        }

        return $configuration;
    }

    /**
     * @param array $data
     * @param Configuration $configuration
     * @return Configuration
     */
    protected function mapStringProperties(array $data, Configuration $configuration)
    {
        $configuration->setClassName($data['class_name'])->setFilePath($data['file_path']);

        if (isset($data['method_prefix'])) {
            $configuration->setMethodPrefix($data['method_prefix']);
        }

        if (isset($data['namespace'])) {
            $configuration->setNamespace($data['namespace']);
        }

        if (isset($data['extends'])) {
            $configuration->setExtends($data['extends']);
        }

        return $configuration;
    }

    /**
     * @param array $data
     * @param string $key
     * @param string $expectedType
     */
    private function validateExpectedDataKeyType(array $data, $key, $expectedType)
    {
        $exceptionMessage = 'value of key "' . $key . '" must be of type "' . $expectedType . '" when set';

        switch ($expectedType) {
            case 'array':
                if (!is_array($data[$key])) {
                    throw new InvalidArgumentException($exceptionMessage);
                }
                break;
            case 'string':
                if (!is_string($data[$key])) {
                    throw new InvalidArgumentException($exceptionMessage);
                }
                break;
            case 'bolean':
                if (!is_bool($data[$key])) {
                    throw new InvalidArgumentException($exceptionMessage);
                }
                break;
        }
    }
}