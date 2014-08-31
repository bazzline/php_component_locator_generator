<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-05-26 
 */

namespace Net\Bazzline\Component\Locator\Configuration\Assembler;

/**
 * Class FromArrayAssembler
 * @package Net\Bazzline\Component\Locator\Configuration\Assembler
 */
class FromArrayAssembler extends AbstractAssembler
{
    /**
     * @param mixed $data
     * @throws RuntimeException
     */
    protected function map($data)
    {
        $configuration = $this->getConfiguration();

        //set strings
        $configuration
            ->setClassName($data['class_name'])
            ->setFilePath($data['file_path']);

        if (isset($data['namespace'])) {
            $configuration->setNamespace($data['namespace']);
        }

        if (isset($data['method_prefix'])) {
            $configuration->setMethodPrefix($data['method_prefix']);
        }

        if (isset($data['extends'])) {
            $configuration->setExtends($data['extends']);
        }

        if (isset($data['instances'])) {
            foreach ($data['instances'] as $key => $instance) {
                if (!isset($instance['class_name'])) {
                    throw new RuntimeException(
                        'instance entry with key "' . $key . '" needs to have a key "class_name"'
                    );
                }

                $alias = (isset($instance['alias'])) ? $instance['alias'] : null;
                $class = $instance['class_name'];
                $isFactory = (isset($instance['is_factory'])) ? $instance['is_factory'] : false;
                $isShared = (isset($instance['is_shared'])) ? $instance['is_shared'] : true;
                $methodBodyBuilder = (isset($instance['method_body_builder'])) ? $instance['method_body_builder'] : null;
                $returnValue = (isset($instance['return_value'])) ? $instance['return_value'] : $class;

                $configuration->addInstance($class, $isFactory, $isShared, $returnValue, $alias, $methodBodyBuilder);
            }
        }

        if (isset($data['implements'])) {
            foreach ($data['implements'] as $interfaceName) {
                $configuration->addImplements($interfaceName);
            }
        }

        if (isset($data['uses'])) {
            foreach ($data['uses'] as $key => $uses) {
                if (!isset($uses['class_name'])) {
                    throw new RuntimeException(
                        'use entry with key "' . $key . '" needs to have a key "class_name"'
                    );
                }

                $alias = (isset($uses['alias'])) ? $uses['alias'] : '';
                $class = $uses['class_name'];

                $configuration->addUses($class, $alias);
            }
        }

        $this->setConfiguration($configuration);
    }

    /**
     * @param mixed $data
     * @throws InvalidArgumentException
     */
    protected function validateData($data)
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException(
                'data must be an array'
            );
        }

        if (empty($data)) {
            throw new InvalidArgumentException(
                'data array must contain content'
            );
        }

        $mandatoryKeysToExpectedValueTyp = array(
            'class_name'        => 'string',
            'file_path'         => 'string'
        );

        $this->validateDataWithMandatoryKeysAndExpectedValueType(
            $data,
            $mandatoryKeysToExpectedValueTyp
        );

        $optionalKeysToExpectedValueTyp = array(
            'extends'           => 'string',
            'instances'         => 'array',
            'implements'        => 'array',
            'namespace'         => 'string',
            'uses'              => 'array'
        );

        $this->validateDataWithOptionalKeysAndExpectedValueTypeOrSetExpectedValueAsDefault(
            $data,
            $optionalKeysToExpectedValueTyp
        );
    }
}