<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-09 
 */

namespace Test\Net\Bazzline\Component\Locator\Configuration\Assembler;

use Test\Net\Bazzline\Component\Locator\LocatorTestCase;

/**
 * Class FromPropelSchemaXmlAssemblerTest
 * @package Test\Net\Bazzline\Component\Locator\Configuration\Assembler
 */
class FromPropelSchemaXmlAssemblerTest extends LocatorTestCase
{
    //begin of test
    /**
     * @expectedException \Net\Bazzline\Component\Locator\Configuration\Assembler\RuntimeException
     * @expectedExceptionMessage configuration is mandatory
     */
    public function testAssembleMissingProperties()
    {
        $assembler = $this->getFromPropelSchemaXmlAssembler();

        $assembler->assemble(null);
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\Configuration\Assembler\InvalidArgumentException
     * @expectedExceptionMessage data must be an array
     */
    public function testAssembleWithNoArrayAsData()
    {
        $assembler = $this->getFromPropelSchemaXmlAssembler();
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
        $assembler = $this->getFromPropelSchemaXmlAssembler();
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
        $assembler = $this->getFromPropelSchemaXmlAssembler();
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
        $assembler = $this->getFromPropelSchemaXmlAssembler();
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
        $assembler = $this->getFromPropelSchemaXmlAssembler();
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
        $assembler = $this->getFromPropelSchemaXmlAssembler();
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
        $assembler = $this->getFromPropelSchemaXmlAssembler();
        $configuration = $this->getMockOfConfiguration();

        $configuration->shouldReceive('setClassName')
            ->with('my_class')
            ->andReturn($configuration)
            ->once();
        $configuration->shouldReceive('setFilePath')
            ->with('/my/file/path')
            ->once();
        $configuration->shouldReceive('addInstance')
            ->atLeast(1);
        $configuration->shouldReceive('addUses')
            ->atLeast(1);

        $data = array(
            'class_name'            => 'my_class',
            'file_path'             => '/my/file/path',
            'path_to_schema_xml'    => __DIR__ . DIRECTORY_SEPARATOR . 'schema.xml' //can not be replaced by vfsStream because of realpath usage
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
        );
        $instances = array(
            'table_one_class',
            'table_one_query_class',
            'table_two_class',
            'table_two_query_class'
        );
        $methodPrefix = 'test';
        $namespace = 'Test\Namespace';
        $uses = array(
            array('class_name' => 'My\Tables\One\MyTableOne', 'alias' => ''),
            array('class_name' => 'My\Tables\One\MyTableOneQuery', 'alias' => ''),
            array('class_name' => 'My\Tables\Two\MyTableTwo', 'alias' => ''),
            array('class_name' => 'My\Tables\Two\MyTableTwoQuery', 'alias' => '')
        );

        $configuration = $this->getConfiguration();
        $data = array(
            'class_name'            => $className,
            'extends'               => $extends,
            'file_path'             => $filePath,
            'method_prefix'         => $methodPrefix,
            'namespace'             => $namespace,
            'path_to_schema_xml'    => __DIR__ . DIRECTORY_SEPARATOR . 'schema.xml' //can not be replaced by vfsStream because of realpath usage
        );

        $assembler = $this->getFromPropelSchemaXmlAssembler();
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