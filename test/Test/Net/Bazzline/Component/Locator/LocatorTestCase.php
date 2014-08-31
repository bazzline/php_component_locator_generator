<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-09 
 */

namespace Test\Net\Bazzline\Component\Locator;

use Mockery;
use Net\Bazzline\Component\CodeGenerator\BlockGenerator;
use Net\Bazzline\Component\CodeGenerator\Factory\BlockGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\ClassGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\DocumentationGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\FileGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\MethodGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\PropertyGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Indention;
use Net\Bazzline\Component\CodeGenerator\LineGenerator;
use Net\Bazzline\Component\Locator\Configuration;
use Net\Bazzline\Component\Locator\FactoryInterfaceGenerator;
use Net\Bazzline\Component\Locator\FileExistsStrategy\DeleteStrategy;
use Net\Bazzline\Component\Locator\FileExistsStrategy\SuffixWithCurrentTimestampStrategy;
use Net\Bazzline\Component\Locator\Generator;
use Net\Bazzline\Component\Locator\GeneratorFactory;
use Net\Bazzline\Component\Locator\InvalidArgumentExceptionGenerator;
use Net\Bazzline\Component\Locator\LocatorGenerator;
use Net\Bazzline\Component\Locator\MethodBodyBuilder\FetchFromFactoryInstancePoolBuilder;
use Net\Bazzline\Component\Locator\MethodBodyBuilder\FetchFromSharedInstancePoolBuilder;
use Net\Bazzline\Component\Locator\MethodBodyBuilder\FetchFromSharedInstancePoolOrCreateByFactoryBuilder;
use Net\Bazzline\Component\Locator\MethodBodyBuilder\NewInstanceBuilder;
use Net\Bazzline\Component\Locator\MethodBodyBuilder\PropelQueryCreateBuilder;
use PHPUnit_Framework_TestCase;

/**
 * Class LocatorTestCase
 * @package Test\Net\Bazzline\Component
 */
class LocatorTestCase extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        Mockery::close();
    }

    //----begin of configuration namespace
    /**
     * @return Mockery\MockInterface|\Net\Bazzline\Component\Locator\Configuration\Instance
     */
    public function getMockOfInstance()
    {
        return Mockery::mock('Net\Bazzline\Component\Locator\Configuration\Instance');
    }

    /**
     * @return Configuration\Instance
     */
    public function getInstance()
    {
        return new Configuration\Instance();
    }

    /**
     * @return Configuration\Uses
     */
    public function getUses()
    {
        return new Configuration\Uses();
    }
    //----end of configuration namespace
    //----begin of configuration assembler namespace
    /**
     * @return Mockery\MockInterface|\Net\Bazzline\Component\Locator\Configuration\Assembler\AbstractAssembler
     */
    public function getMockOfAbstractAssembler()
    {
        return Mockery::mock('Net\Bazzline\Component\Locator\Configuration\Assembler\AbstractAssembler');
    }

    /**
     * @return Configuration\Assembler\FromArrayAssembler
     */
    public function getFromArrayAssembler()
    {
        return new Configuration\Assembler\FromArrayAssembler();
    }

    /**
     * @return Configuration\Assembler\FromPropelSchemaXmlAssembler
     */
    public function getFromPropelSchemaXmlAssembler()
    {
        return new Configuration\Assembler\FromPropelSchemaXmlAssembler();
    }
    //----end of configuration assembler namespace

    //----begin of file exists strategy namespace
    /**
     * @return Mockery\MockInterface|\Net\Bazzline\Component\Locator\FileExistsStrategy\FileExistsStrategyInterface
     */
    public function getMockOfFileExistsStrategyInterface()
    {
        return Mockery::mock('Net\Bazzline\Component\Locator\FileExistsStrategy\FileExistsStrategyInterface');
    }

    /**
     * @return Mockery\MockInterface|\Net\Bazzline\Component\Locator\FileExistsStrategy\AbstractStrategy
     */
    public function getMockOfAbstractStrategy()
    {
        return Mockery::mock('Net\Bazzline\Component\Locator\FileExistsStrategy\AbstractStrategy[execute]');
    }

    /**
     * @return DeleteStrategy
     */
    public function getDeleteStrategy()
    {
        return new DeleteStrategy();
    }

    /**
     * @return SuffixWithCurrentTimestampStrategy
     */
    public function getSuffixWithCurrentTimestampStrategy()
    {
        return new SuffixWithCurrentTimestampStrategy();
    }
    //----end of file exists strategy namespace

    //----begin of locator namespace
    /**
     * @return Configuration
     */
    protected function getConfiguration()
    {
        $configuration = new Configuration();

        $configuration->setFetchFromFactoryInstancePoolBuilder($this->getFetchFromFactoryInstancePoolBuilder());
        $configuration->setFetchFromSharedInstancePoolBuilder($this->getFetchFromSharedInstancePoolBuilder());
        $configuration->setFetchFromSharedInstancePoolOrCreateByFactoryBuilder($this->getFetchFromSharedInstancePoolOrCreateByFactoryBuilder());
        $configuration->setInstance($this->getInstance());
        $configuration->setNewInstanceBuilder($this->getNewInstanceBuilder());
        $configuration->setUses($this->getUses());

        return $configuration;
    }

    /**
     * @return Configuration|\Mockery\MockInterface
     */
    protected function getMockOfConfiguration()
    {
        return Mockery::mock('Net\Bazzline\Component\Locator\Configuration');
    }

    /**
     * @return Mockery\MockInterface|\Net\Bazzline\Component\Locator\AbstractGenerator
     */
    protected function getMockOfAbstractGenerator()
    {
        return Mockery::mock('Net\Bazzline\Component\Locator\AbstractGenerator[generate]');
    }

    /**
     * @return FactoryInterfaceGenerator
     */
    protected function getFactoryInterfaceGenerator()
    {
        return new FactoryInterfaceGenerator();
    }

    /**
     * @return FactoryInterfaceGenerator|\Mockery\MockInterface
     */
    protected function getMockOfFactoryInterfaceGenerator()
    {
        return Mockery::mock('Net\Bazzline\Component\Locator\FactoryInterfaceGenerator');
    }

    /**
     * @return InvalidArgumentExceptionGenerator
     */
    protected function getInvalidArgumentExceptionGenerator()
    {
        return new InvalidArgumentExceptionGenerator();
    }

    /**
     * @return InvalidArgumentExceptionGenerator|\Mockery\MockInterface
     */
    protected function getMockOfInvalidArgumentExceptionGenerator()
    {
        return Mockery::mock('Net\Bazzline\Component\Locator\InvalidArgumentExceptionGenerator');
    }

    /**
     * @return Generator
     */
    protected function getGenerator()
    {
        return new Generator();
    }

    /**
     * @return GeneratorFactory
     */
    protected function getGeneratorFactory()
    {
        return new GeneratorFactory();
    }

    /**
     * @return LocatorGenerator
     */
    protected function getLocatorGenerator()
    {
        return new LocatorGenerator();
    }

    /**
     * @return LocatorGenerator|\Mockery\MockInterface
     */
    protected function getMockOfLocatorGenerator()
    {
        return Mockery::mock('Net\Bazzline\Component\Locator\LocatorGenerator');
    }
    //----end of locator namespace

    //----begin of helper
    //----end of helper

    //----begin of generator
    /**
     * @return \Net\Bazzline\Component\CodeGenerator\BlockGenerator
     */
    protected function getBlockGenerator()
    {
        return new BlockGenerator($this->getLineGenerator(), $this->getIndention());
    }

    /**
     * @return Mockery\MockInterface|\Net\Bazzline\Component\CodeGenerator\DocumentationGenerator
     */
    protected function getMockOfDocumentationGenerator()
    {
        return Mockery::mock('Net\Bazzline\Component\CodeGenerator\DocumentationGenerator');
    }

    /**
     * @return LineGenerator
     */
    protected function getLineGenerator()
    {
        return new LineGenerator($this->getIndention());
    }

    /**
     * @return Indention
     */
    protected function getIndention()
    {
        return new Indention();
    }
    //----end of generator
    //----begin of generator factory
    /**
     * @return BlockGeneratorFactory
     */
    protected function getBlockGeneratorFactory()
    {
        return new BlockGeneratorFactory();
    }

    /**
     * @return ClassGeneratorFactory
     */
    protected function getClassGeneratorFactory()
    {
        return new ClassGeneratorFactory();
    }

    /**
     * @return DocumentationGeneratorFactory
     */
    protected function getDocumentationGeneratorFactory()
    {
        return new DocumentationGeneratorFactory();
    }

    /**
     * @return FileGeneratorFactory
     */
    protected function getFileGeneratorFactory()
    {
        return new FileGeneratorFactory();
    }

    /**
     * @return MethodGeneratorFactory
     */
    protected function getMethodGeneratorFactory()
    {
        return new MethodGeneratorFactory();
    }

    /**
     * @return PropertyGeneratorFactory
     */
    protected function getPropertyGeneratorFactory()
    {
        return new PropertyGeneratorFactory();
    }
    //----end of generator factory

    //----begin of MethodBodyBuilder
    /**
     * @return Mockery\MockInterface|\Net\Bazzline\Component\Locator\MethodBodyBuilder\AbstractMethodBodyBuilder
     */
    protected function getAbstractMethodBodyBuilder()
    {
        return Mockery::mock('Net\Bazzline\Component\Locator\MethodBodyBuilder\AbstractMethodBodyBuilder[build]');
    }
    /**
     * @return FetchFromFactoryInstancePoolBuilder
     */
    protected function getFetchFromFactoryInstancePoolBuilder()
    {
        return new FetchFromFactoryInstancePoolBuilder();
    }

    /**
     * @return FetchFromSharedInstancePoolBuilder
     */
    protected function getFetchFromSharedInstancePoolBuilder()
    {
        return new FetchFromSharedInstancePoolBuilder();
    }

    /**
     * @return FetchFromSharedInstancePoolOrCreateByFactoryBuilder
     */
    protected function getFetchFromSharedInstancePoolOrCreateByFactoryBuilder()
    {
        return new FetchFromSharedInstancePoolOrCreateByFactoryBuilder();
    }

    /**
     * @return NewInstanceBuilder
     */
    protected function getNewInstanceBuilder()
    {
        return new NewInstanceBuilder();
    }

    /**
     * @return PropelQueryCreateBuilder
     */
    protected function getPropelQueryCreateBuilder()
    {
        return new PropelQueryCreateBuilder();
    }
    //----end of MethodBodyBuilder
}