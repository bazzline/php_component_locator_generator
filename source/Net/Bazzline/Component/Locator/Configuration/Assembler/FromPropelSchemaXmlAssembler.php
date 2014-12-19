<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-05-26 
 */

namespace Net\Bazzline\Component\Locator\Configuration\Assembler;

use Net\Bazzline\Component\Locator\Configuration;
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

        $this->validatePathToSchemaXml($pathToSchemaXml);

        $configuration = $this->mapStringPropertiesToConfiguration(
            $data,
            $configuration
        );
        $configuration = $this->mapSchemaXmlPropertiesToConfiguration(
            $data,
            $pathToSchemaXml,
            $configuration
        );
        $configuration = $this->mapArrayPropertiesToConfiguration(
            $data,
            $configuration
        );

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

    /**
     * @param XMLReader $reader
     * @param boolean $hasRootNamespace
     * @param string $rootNamespace
     * @param string $locatorNamespace
     * @param Configuration $configuration
     * @param string $columnClassMethodBodyBuilder
     * @param string $queryClassMethodBodyBuilder
     * @return Configuration
     */
    private function addTableToConfiguration(
        XMLReader $reader,
        $hasRootNamespace,
        $rootNamespace,
        $locatorNamespace,
        Configuration $configuration,
        $columnClassMethodBodyBuilder,
        $queryClassMethodBodyBuilder
    )
    {
        //begin of variable definitions
        $fullQualifiedClassName = '';
        $namespace              = $reader->getAttribute('namespace');
        $phpName                = $reader->getAttribute('phpName');
        $tableName              = $reader->getAttribute('name');
        $tableNamespace         = '';
        $hasPhpName             = (strlen($phpName) > 0);
        $hasNamespace           = (strlen($namespace) > 0);
        //end of variable definitions

        //begin of class name building
        if ($hasRootNamespace) {
            $tableNamespace .= '\\' . $rootNamespace . '\\';
        }

        if ($hasNamespace) {
            $tableNamespace .= $namespace . '\\';
        }

        $hasDifferentNamespaceThanLocator = ($locatorNamespace !== $tableNamespace);
        $hasTableNamespace  = (strlen($tableNamespace) > 0);

        if ($hasPhpName) {
            $fullQualifiedClassName = $phpName;
        } else {
            $tableNameAsArray = explode('_', $tableName);
            array_walk($tableNameAsArray, function (&$value) {
                $value = ucfirst($value);
            });
            $fullQualifiedClassName .= implode('', $tableNameAsArray);
        }

        if ($hasTableNamespace) {
            $fullQualifiedClassName = $tableNamespace . '\\' . $fullQualifiedClassName;
        }

        $fullQualifiedClassName = str_replace('\\\\', '\\', $fullQualifiedClassName);
        $fullQualifiedQueryClassName = $fullQualifiedClassName . 'Query';
        //end of class name building

        //begin of configuration adaptation
        $configuration->addInstance($fullQualifiedClassName, false, false, $fullQualifiedClassName, null, $columnClassMethodBodyBuilder);
        $configuration->addInstance($fullQualifiedQueryClassName, false, false, $fullQualifiedClassName, null, $queryClassMethodBodyBuilder);

        if ($hasDifferentNamespaceThanLocator) {
            //we have to remove the first "\" if available
            $useClassName = ($this->startsWith($fullQualifiedClassName, '\\'))
                ? substr($fullQualifiedClassName, 1)
                : $fullQualifiedClassName;
            $useQueryClassName = ($this->startsWith($fullQualifiedQueryClassName, '\\'))
                ? substr($fullQualifiedQueryClassName, 1)
                : $fullQualifiedQueryClassName;

            $configuration->addUses($useClassName);
            $configuration->addUses($useQueryClassName);
        }
        //end of configuration adaptation

        return $configuration;
    }

    /**
     * @param string $pathToSchemaXml
     * @throws RuntimeException
     */
    private function validatePathToSchemaXml($pathToSchemaXml)
    {
        if (!is_file($pathToSchemaXml)) {
            throw new RuntimeException('provided schema xml path "' . $pathToSchemaXml . '" is not a file');
        }

        if (!is_readable($pathToSchemaXml)) {
            throw new RuntimeException('file "' . $pathToSchemaXml . '" is not readable');
        }
    }

    /**
     * @param array $data
     * @param Configuration $configuration
     * @return Configuration
     */
    private function mapStringPropertiesToConfiguration(array $data, Configuration $configuration)
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
     * @param string $pathToSchemaXml
     * @param Configuration $configuration
     * @return Configuration
     */
    private function mapSchemaXmlPropertiesToConfiguration(array $data, $pathToSchemaXml, Configuration $configuration)
    {
        //begin of variable definitions
        //@todo inject XMLReader
        $reader = new XMLReader();
        $reader->open($pathToSchemaXml);
        $columnClassMethodBodyBuilder =
            (isset($data['column_class_method_body_builder']))
                ? $data['column_class_method_body_builder']
                : null;
        $hasRootNamespace = false;
        $locatorNamespace = (isset($data['namespace'])) ? $data['namespace'] : '';
        $queryClassMethodBodyBuilder =
            (isset($data['query_class_method_body_builder']))
                ? $data['query_class_method_body_builder']
                : null;
        $rootNamespace = '';
        //end of variable definitions

        //begin of xml parsing
        while ($reader->read()) {
            if ($reader->nodeType === XMLREADER::ELEMENT) {
                $nodeIsADatabase = ($reader->name === 'database');
                $nodeIsATable = ($reader->name === 'table');

                if ($nodeIsADatabase) {
                    $rootNamespace = $reader->getAttribute('namespace');
                    if(strlen($rootNamespace) > 0) {
                        $hasRootNamespace = true;
                    }
                }

                if ($nodeIsATable) {
                    $configuration = $this->addTableToConfiguration(
                        $reader,
                        $hasRootNamespace,
                        $rootNamespace,
                        $locatorNamespace,
                        $configuration,
                        $columnClassMethodBodyBuilder,
                        $queryClassMethodBodyBuilder
                    );
                }
            }
        }
        $reader->close();
        //end of xml parsing

        return $configuration;
    }

    /**
     * @param array $data
     * @param Configuration $configuration
     * @return Configuration
     * @throws RuntimeException
     */
    private function mapArrayPropertiesToConfiguration(array $data, Configuration $configuration)
    {
        if (isset($data['implements'])) {
            foreach($data['implements'] as $interfaceName) {
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

        return $configuration;
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    private function startsWith($haystack, $needle)
    {
        return (strncmp($haystack, $needle, strlen($needle)) === 0);
    }
}
