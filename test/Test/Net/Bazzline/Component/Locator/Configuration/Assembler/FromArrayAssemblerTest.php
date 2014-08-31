<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-09 
 */

namespace Test\Net\Bazzline\Component\Locator\Configuration\Assembler;

use Test\Net\Bazzline\Component\Locator\LocatorTestCase;
use Net\Bazzline\Component\Locator\Configuration;

/**
 * Class FromArrayAssemblerTest
 * @package Test\Net\Bazzline\Component\Locator\Configuration\Assembler
 */
class FromArrayAssemblerTest extends LocatorTestCase
{
    //begin of test
    /**
     * @expectedException \Net\Bazzline\Component\Locator\Configuration\Assembler\RuntimeException
     * @expectedExceptionMessage configuration is mandatory
     */
    public function testAssembleMissingProperties()
    {
        $assembler = $this->getFromArrayAssembler();

        $assembler->assemble(null);
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\Configuration\Assembler\InvalidArgumentException
     * @expectedExceptionMessage data must be an array
     */
    public function testAssembleWithNoArrayAsData()
    {
        $assembler = $this->getFromArrayAssembler();
        $configuration = $this->getMockOfConfiguration();

        $assembler->setConfiguration($configuration)
            ->assemble(null);
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\Configuration\Assembler\InvalidArgumentException
     * @expectedExceptionMessage data array must contain content
     */
    public function testAssembleWithEmptyDataArray()
    {
        $assembler = $this->getFromArrayAssembler();
        $configuration = $this->getMockOfConfiguration();

        $assembler->setConfiguration($configuration)
            ->assemble(array());
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\Configuration\Assembler\InvalidArgumentException
     * @expectedExceptionMessage data array must contain content for key "class_name"
     */
    public function testAssembleWithMissingMandatoryDataKeyClassName()
    {
        $assembler = $this->getFromArrayAssembler();
        $configuration = $this->getMockOfConfiguration();

        $assembler->setConfiguration($configuration)
            ->assemble(array('key' => null));
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\Configuration\Assembler\InvalidArgumentException
     * @expectedExceptionMessage value of key "class_name" must be of type "string"
     */
    public function testAssembleWithWrongMandatoryDataKeyClassNameValueType()
    {
        $assembler = $this->getFromArrayAssembler();
        $configuration = $this->getMockOfConfiguration();

        $assembler->setConfiguration($configuration)
            ->assemble(array('class_name' => 1));
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\Configuration\Assembler\InvalidArgumentException
     * @expectedExceptionMessage data array must contain content for key "file_path"
     */
    public function testAssembleWithMissingMandatoryDataKeyFilePath()
    {
        $assembler = $this->getFromArrayAssembler();
        $configuration = $this->getMockOfConfiguration();

        $assembler->setConfiguration($configuration)
            ->assemble(array('class_name' => 'class name'));
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\Configuration\Assembler\InvalidArgumentException
     * @expectedExceptionMessage value of key "extends" must be of type "string" when set
     */
    public function testAssembleWithWrongOptionalDataKeyClassNameValueType()
    {
        $assembler = $this->getFromArrayAssembler();
        $configuration = $this->getMockOfConfiguration();

        $assembler->setConfiguration($configuration)
            ->assemble(
            array(
                'class_name'    => 'class name',
                'file_path'     => '/file/path',
                'extends'       => array('your argument is invalid')
            )
        );
    }

    public function testAssembleWithValidMandatoryData()
    {
        $assembler = $this->getFromArrayAssembler();
        $configuration = $this->getMockOfConfiguration();

        $configuration->shouldReceive('setClassName')
            ->with('my_class')
            ->andReturn($configuration)
            ->once();
        $configuration->shouldReceive('setFilePath')
            ->with('/my/file/path')
            ->once();

        $data = array(
            'class_name'    => 'my_class',
            'file_path'     => '/my/file/path'
        );

        $assembler->setConfiguration($configuration)
            ->assemble($data);
    }

    public function testAssembleWithValidAllData()
    {
        $className = 'TestName';
        $extends = 'Bar';
        $filePath = '/test/name';
        $implements = array(
                'BarInterface',
                'FooInterface',
                'TestInterface'
        );
        $instances = array(
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
                )
        );
        $methodPrefix = 'test';
        $namespace = 'Test\Namespace';
        $uses = array(
            array('class_name' => 'My\Foo', 'alias' => 'Foo')
        );

        $configuration = $this->getConfiguration();
        $data = array(
            'class_name' => $className,
            'file_path' => $filePath,
            'namespace' => $namespace,
            'method_prefix' => $methodPrefix,
            'extends' => $extends,
            'instances' => $instances,
            'implements' => $implements,
            'uses' => $uses
        );

        $assembler = $this->getFromArrayAssembler();
        $assembler->setConfiguration($configuration);
        $assembler->assemble($data);

        $expectedUseCollection = array();
        foreach ($uses as $use) {
            $item = $this->getUses();
            $item->setAlias($use['alias']);
            $item->setClassName($use['class_name']);
            $expectedUseCollection[] = $item;
        }

        $this->assertEquals($className, $configuration->getClassName());
        $this->assertEquals($extends, $configuration->getExtends());
        $this->assertEquals($className . '.php', $configuration->getFileName());
        $this->assertEquals($implements, $configuration->getImplements());
        $this->assertSameSize($instances, $configuration->getInstances());
        $this->assertEquals($methodPrefix, $configuration->getMethodPrefix());
        $this->assertEquals($namespace, $configuration->getNamespace());
        $this->assertEquals($expectedUseCollection, $configuration->getUseCollection());
    }
    //end of test
}