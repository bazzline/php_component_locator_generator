<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-04-27 
 */

return array(
    'assembler' => '\Net\Bazzline\Component\Locator\Configuration\Assembler\FromPropelSchemaXmlAssembler',
    'class_name' => 'FromPropelSchemaXmlLocator',    //determines file name as well as php class name
    //add class names here, depending on entries in use section, full qualified or not
    'extends' => 'BaseLocator',
    'file_exists_strategy' => '\Net\Bazzline\Component\Locator\FileExistsStrategy\SuffixWithCurrentTimestampStrategy',
    //file path where files will be generated
    'file_path' => __DIR__ . '/../../data',
    //add interface names here, depending on entries in use section, full qualified or not
    'implements' => array(
        '\My\Full\QualifiedInterface',
        'MyInterface'
    ),
    //method builder that is used for normal propel class instantiation
    'column_class_method_body_builder' => '\Net\Bazzline\Component\Locator\MethodBodyBuilder\NewInstanceBuilder',
    //method builder that is used for query propel class instantiation
    'query_class_method_body_builder' => '\Net\Bazzline\Component\Locator\MethodBodyBuilder\PropelQueryCreateBuilder',
    //prefix for the instance fetching
    'method_prefix' => 'create',
    'namespace' => 'Application\Service',
    'path_to_schema_xml' => __DIR__ . '/schema.xml',
    //add use statements here
    //format: array(['alias' => <string>], 'class_name' => <string>)
    'uses' => array(
        array(
            'alias'         => 'MyInterface',
            'class_name'    => 'My\OtherInterface'
        ),
        array(
            'alias'         => 'BaseLocator',
            'class_name'    => 'Application\Locator\BaseLocator'
        )
    )
);