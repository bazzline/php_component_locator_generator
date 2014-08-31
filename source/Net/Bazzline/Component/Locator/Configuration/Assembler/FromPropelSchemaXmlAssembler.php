<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-05-26 
 */

namespace Net\Bazzline\Component\Locator\Configuration\Assembler;

use XMLReader;

/**
 * Class FromPropelSchemaXmlAssembler
 * @package Net\Bazzline\Component\Locator\Configuration\Assembler
 */
class FromPropelSchemaXmlAssembler extends AbstractAssembler
{
    /**
     * @param mixed $data
     * @throws RuntimeException
     */
    protected function map($data)
    {
        $configuration = $this->getConfiguration();
        $pathToSchemaXml = realpath($data['path_to_schema_xml']);

        if (!is_file($pathToSchemaXml)) {
            throw new RuntimeException(
                'provided schema xml path "' . $pathToSchemaXml . '" is not a file'
            );
        }

        if (!is_readable($pathToSchemaXml)) {
            throw new RuntimeException(
                'file "' . $pathToSchemaXml . '" is not readable'
            );
        }

        //set strings
        $configuration
            ->setClassName($data['class_name'])
            ->setFilePath($data['file_path']);

        if (isset($data['method_prefix'])) {
            $configuration->setMethodPrefix($data['method_prefix']);
        }

        if (isset($data['namespace'])) {
            $configuration->setNamespace($data['namespace']);
        }

        if (isset($data['extends'])) {
            $configuration->setExtends($data['extends']);
        }

        $reader = new XMLReader();
        $reader->open($pathToSchemaXml);

        $columnClassMethodBodyBuilder = (isset($data['column_class_method_body_builder']))
            ? $data['column_class_method_body_builder'] : null;
        $hasRootNamespace = false;
        $locatorNamespace = (isset($data['namespace'])) ? $data['namespace'] : '';
        $queryClassMethodBodyBuilder = (isset($data['query_class_method_body_builder']))
            ? $data['query_class_method_body_builder'] : null;
        $rootNamespace = '';

        while ($reader->read()) {
            if ($reader->nodeType === XMLREADER::ELEMENT) {
                if ($reader->name === 'database') {
                    $rootNamespace = $reader->getAttribute('namespace');
                    if (strlen($rootNamespace) > 0) {
                        $hasRootNamespace = true;
                    }
                }
   
                if ($reader->name === 'table') {
                    $className = '';
                    $namespace = $reader->getAttribute('namespace');
                    $phpName = $reader->getAttribute('phpName');
                    $tableName = $reader->getAttribute('name');
                    $tableNamespace = '';

                    if ($hasRootNamespace) {
                        $tableNamespace .= $rootNamespace . '\\';
                    }

                    if (strlen($namespace) > 0) {
                        $tableNamespace .= $namespace . '\\';
                    }

                    if (strlen($phpName) > 0) {
                        $className = $phpName;
                    } else {
                        $tableNameAsArray = explode('_', $tableName);
                        array_walk($tableNameAsArray, function (&$value) { $value = ucfirst($value); });
                        $className .= implode('', $tableNameAsArray);
                    }

                    if (strlen($tableNamespace) > 0) {
                        $className = $tableNamespace . '\\' . $className;
                    }
                    $className = str_replace('\\\\', '\\', $className);

                    $queryClassName = $className . 'Query';

                    $configuration->addInstance($className, false, false, $className, null, $columnClassMethodBodyBuilder);
                    $configuration->addInstance($queryClassName, false, false, $className, null, $queryClassMethodBodyBuilder);

                    if ($locatorNamespace !== $tableNamespace) {
                        $configuration->addUses($className);
                        $configuration->addUses($queryClassName);
                    }
                }
            }
        }
        $reader->close();

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
                $className = str_replace('\\\\', '\\', $uses['class_name']);

                $configuration->addUses($className, $alias);
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
            'class_name'            => 'string',
            'file_path'             => 'string'
        );

        $this->validateDataWithMandatoryKeysAndExpectedValueType(
            $data,
            $mandatoryKeysToExpectedValueTyp
        );

        $optionalKeysToExpectedValueTyp = array(
            'extends'               => 'string',
            'implements'            => 'array',
            'namespace'             => 'string',
            'path_to_schema_xml'    => 'string',
            'uses'                  => 'array'
        );

        $this->validateDataWithOptionalKeysAndExpectedValueTypeOrSetExpectedValueAsDefault(
            $data,
            $optionalKeysToExpectedValueTyp
        );
    }
}
