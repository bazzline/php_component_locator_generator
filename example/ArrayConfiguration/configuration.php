<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-04-27 
 */

return array(
    'assembler' => '\Net\Bazzline\Component\Locator\Configuration\Assembler\FromArrayAssembler',
    'bootstrap_file' => __DIR__ . '/boostrap.php',
    'class_name' => 'FromArrayConfigurationFileLocator',    //determines file name as well as php class name
    //add class name here, depending on entries in use section, full qualified or not
    'extends' => 'BaseLocator',
    'file_exists_strategy' => '\Net\Bazzline\Component\Locator\FileExistsStrategy\SuffixWithCurrentTimestampStrategy',
    //file path where files will be generated
    'file_path' => __DIR__ . '/../../data',
    //format: array(['alias' => <string>], 'name' => <string>, ['is_factory' => <boolean>], ['is_shared' => <boolean>], ['method_body_builder'] => <string>)
    'instances' => array(
        array(
            'alias'         => 'UniqueInvokableInstance',
            'class_name'    => '\Application\Model\ExampleUniqueInvokableInstance',
            'is_shared'     => false
        ),
        array(
            'alias'         => 'UniqueFactorizedInstance',
            'class_name'    => '\Application\Factory\ExampleUniqueFactorizedInstanceFactory',
            'is_factory'    => true,
            'is_shared'     => false,
            'return_value'  => '\Application\Model\ExampleUniqueFactorizedInstance'
        ),
        array(
            'alias'         => 'SharedInvokableInstance',
            'class_name'    => '\Application\Model\ExampleSharedInvokableInstance'
        ),
        array(
            'alias'         => 'SharedFactorizedInstance',
            'class_name'    => '\Application\Factory\ExampleSharedFactorizedInstanceFactory',
            'is_factory'    => true,
            'return_value'  => '\Application\Model\ExampleSharedFactorizedInstance'
        ),
        array(
            'alias'                 => 'ValidatedInvokableInstance',
            'class_name'            => '\Application\Model\ExampleValidatedInvokableInstance',
            'method_body_builder'   => '\ValidatedInstanceCreationBuilder'
        )
    ),
    //add interface names here, depending on entries in use section, full qualified or not
    'implements' => array(
        '\My\Full\QualifiedInterface',
        'MyInterface'
    ),
    //prefix for the instance fetching
    'method_prefix' => 'get',
    'namespace' => 'Application\Service',
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