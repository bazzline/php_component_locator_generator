<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-05-26 
 */

namespace Net\Bazzline\Component\Locator\Configuration\Assembler;

use Net\Bazzline\Component\Locator\Configuration;
use Net\Bazzline\Component\Locator\Configuration\Validator\ReadableFilePath;
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
        //begin of variable definitions
        $columnClassMethodBodyBuilder =
            (isset($data['column_class_method_body_builder']))
                ? $data['column_class_method_body_builder']
                : null;
        $configuration = $this->getConfiguration();
        $locatorNamespace =
            (isset($data['namespace']))
                ? $data['namespace']
                : '';
        $methodNameWithoutNamespace =
            (isset($data['method_name_without_namespace']))
                ? $data['method_name_without_namespace']
                : false;
        $pathToSchemaXml = realpath($data['path_to_schema_xml']);
        $queryClassMethodBodyBuilder =
            (isset($data['query_class_method_body_builder']))
                ? $data['query_class_method_body_builder']
                : null;
        //end of variable definitions

        //@todo inject
        $validator = new ReadableFilePath();
        $validator->validate($pathToSchemaXml);

        $configuration = $this->mapStringPropertiesToConfiguration(
            $data,
            $configuration
        );
        $configuration = $this->mapSchemaXmlPropertiesToConfiguration(
            $pathToSchemaXml,
            $configuration,
            $columnClassMethodBodyBuilder,
            $locatorNamespace,
            $queryClassMethodBodyBuilder,
            $methodNameWithoutNamespace
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
            'extends'                       => 'string',
            'implements'                    => 'array',
            'method_name_without_namespace' => 'boolean',
            'namespace'                     => 'string',
            'path_to_schema_xml'            => 'string',
            'uses'                          => 'array'
        );

        $this->validateDataWithOptionalKeysAndExpectedValueTypeOrSetExpectedValueAsDefault(
            $data,
            $optionalKeysToExpectedValueTyp
        );
    }

    /**
     * @param string $pathToSchemaXml
     * @param Configuration $configuration
     * @param string $columnClassMethodBodyBuilder
     * @param string $locatorNamespace
     * @param string $queryClassMethodBodyBuilder
     * @param boolean $methodNameWithoutNamespace
     * @return Configuration
     */
    private function mapSchemaXmlPropertiesToConfiguration(
        $pathToSchemaXml,
        Configuration $configuration,
        $columnClassMethodBodyBuilder,
        $locatorNamespace,
        $queryClassMethodBodyBuilder,
        $methodNameWithoutNamespace
    )
    {
        //begin of variable definitions
        $hasRootNamespace = false;
        //@todo inject XMLReader
        $reader = new XMLReader();
        $reader->open($pathToSchemaXml);
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
                        $queryClassMethodBodyBuilder,
                        $methodNameWithoutNamespace
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
     * @param XMLReader $reader
     * @param boolean $hasRootNamespace
     * @param string $rootNamespace
     * @param string $locatorNamespace
     * @param Configuration $configuration
     * @param string $columnClassMethodBodyBuilder
     * @param string $queryClassMethodBodyBuilder
     * @param boolean $methodNameWithoutNamespace
     * @return Configuration
     */
    private function addTableToConfiguration(
        XMLReader $reader,
        $hasRootNamespace,
        $rootNamespace,
        $locatorNamespace,
        Configuration $configuration,
        $columnClassMethodBodyBuilder,
        $queryClassMethodBodyBuilder,
        $methodNameWithoutNamespace
    )
    {
        //begin of variable definitions
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
        $fullQualifiedClassName = $this->createFullQualifiedClassName(
            $hasPhpName,
            $phpName,
            $tableName,
            $hasTableNamespace,
            $tableNamespace
        );
        $classNameAlias = ($methodNameWithoutNamespace)
            ? $this->createClassNameAlias($hasPhpName, $phpName, $tableName)
            : null;
        $queryClassNameAlias =
            (!is_null($classNameAlias))
                ? $classNameAlias . 'Query'
                : null;
        $fullQualifiedQueryClassName = $fullQualifiedClassName . 'Query';
        //end of class name building

        //begin of configuration adaptation
        $configuration->addInstance(
            $fullQualifiedClassName,
            false,
            false,
            $fullQualifiedClassName,
            $classNameAlias,
            $columnClassMethodBodyBuilder
        );
        $configuration->addInstance(
            $fullQualifiedQueryClassName,
            false,
            false,
            $fullQualifiedClassName,
            $queryClassNameAlias,
            $queryClassMethodBodyBuilder
        );

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
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    private function startsWith($haystack, $needle)
    {
        return (strncmp($haystack, $needle, strlen($needle)) === 0);
    }

    /**
     * @param boolean $hasPhpName
     * @param string $phpName
     * @param string $tableName
     * @param boolean $hasTableNamespace
     * @param string $tableNamespace
     * @return mixed|string
     */
    private function createFullQualifiedClassName($hasPhpName, $phpName, $tableName, $hasTableNamespace, $tableNamespace)
    {
        $fullQualifiedClassName = '';

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

        return $fullQualifiedClassName;
    }

    /**
     * @param boolean $hasPhpName
     * @param string $phpName
     * @param string $tableName
     * @return string
     */
    private function createClassNameAlias($hasPhpName, $phpName, $tableName)
    {
        return ($hasPhpName) ? $phpName : $tableName;
    }
}
