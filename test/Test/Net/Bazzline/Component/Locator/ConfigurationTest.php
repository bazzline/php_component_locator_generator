<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-09 
 */

namespace Test\Net\Bazzline\Component\Locator;

/**
 * Class ConfigurationTest
 * @package Test\Net\Bazzline\Component\Locator
 */
class ConfigurationTest extends LocatorTestCase
{
    public function testGetClassName()
    {
        $configuration = $this->getConfiguration();

        $this->assertNull($configuration->getClassName());
    }

    public function testSetClassName()
    {
        $configuration = $this->getConfiguration();
        $className = 'foo';

        $this->assertEquals(
            $configuration,
            $configuration->setClassName($className)
        );
        $this->assertEquals($className, $configuration->getClassName());
    }

    public function testGetFileName()
    {
        $configuration = $this->getConfiguration();

        $this->assertNull($configuration->getFilePath());
    }

    public function testGetFilePath()
    {
        $configuration = $this->getConfiguration();

        $this->assertNull($configuration->getFilePath());
    }

    public function testSetFilePath()
    {
        $configuration = $this->getConfiguration();
        $filePath = 'foo';

        $this->assertEquals(
            $configuration,
            $configuration->setFilePath($filePath)
        );
        $this->assertEquals(
            $filePath,
            $configuration->getFilePath()
        );
    }

    public function testGetNamespace()
    {
        $configuration = $this->getConfiguration();
        $namespace = 'foo';

        $this->assertEquals(
            $configuration,
            $configuration->setNamespace($namespace)
        );
        $this->assertEquals(
            $namespace,
            $configuration->getNamespace()
        );
    }

    public function testHasNamespace()
    {
        $configuration = $this->getConfiguration();
        $invalidNamespace = '';
        $validNamespace = 'foo';

        $this->assertFalse($configuration->hasNamespace());

        $configuration->setNamespace($invalidNamespace);
        $this->assertFalse($configuration->hasNamespace());

        $configuration->setNamespace($validNamespace);
        $this->assertTrue($configuration->hasNamespace());
    }

    public function testSetNamespace()
    {
        $configuration = $this->getConfiguration();
        $namespace = 'foo';

        $this->assertEquals(
            $configuration,
            $configuration->setNamespace($namespace)
        );
    }

    public function testSetMethodPrefix()
    {
        $configuration = $this->getConfiguration();
        $methodPrefix = 'foo';

        $this->assertEquals(
            $configuration,
            $configuration->setMethodPrefix($methodPrefix)
        );
    }

    public function testGetMethodPrefix()
    {
        $configuration = $this->getConfiguration();
        $methodPrefix = 'foo';

        $this->assertNull($configuration->getMethodPrefix());

        $configuration->setMethodPrefix($methodPrefix);
        $this->assertEquals(
            $methodPrefix,
            $configuration->getMethodPrefix()
        );
    }

    public function testAddInstance()
    {
        $configuration = $this->getConfiguration();
        $alias = 'bar';
        $className = 'foo';
        $isFactory = true;
        $isShared = true;
        $returnValue = 'string';

        $this->assertEquals(
            $configuration,
            $configuration->addInstance(
                $className, $isFactory, $isShared, $returnValue, $alias
            )
        );
    }

    public function testGetInstances()
    {
        $configuration = $this->getConfiguration();
        $alias = 'bar';
        $className = 'foo';
        $isFactory = true;
        $isShared = true;
        $returnValue = 'string';

        $this->assertEmpty($configuration->getInstances());

        $this->assertEquals(
            $configuration,
            $configuration->addInstance(
                $className, $isFactory, $isShared, $returnValue, $alias
            )
        );

        $this->assertNotEmpty($configuration->getInstances());
        $this->assertCount(1, $configuration->getInstances());
    }

    public function testHasInstances()
    {
        $configuration = $this->getConfiguration();

        $alias = 'bar';
        $className = 'foo';
        $isFactory = true;
        $isShared = true;
        $returnValue = 'string';

        $this->assertFalse($configuration->hasInstances());

        $this->assertEquals(
            $configuration,
            $configuration->addInstance(
                $className, $isFactory, $isShared, $returnValue, $alias
            )
        );

        $this->assertTrue($configuration->hasInstances());
    }

    public function testHasFactoryInstances()
    {
        $configuration = $this->getConfiguration();

        $alias = 'bar';
        $className = 'foo';
        $isFactory = true;
        $isShared = false;
        $returnValue = 'string';

        $this->assertFalse($configuration->hasFactoryInstances());

        $configuration->addInstance(
            $className, $isFactory, $isShared, $returnValue, $alias
        );

        $this->assertTrue($configuration->hasFactoryInstances());
    }

    public function testHasSharedInstances()
    {
        $configuration = $this->getConfiguration();

        $alias = 'bar';
        $className = 'foo';
        $isFactory = false;
        $isShared = true;
        $returnValue = 'string';

        $this->assertFalse($configuration->hasSharedInstances());

        $configuration->addInstance(
            $className, $isFactory, $isShared, $returnValue, $alias
        );

        $this->assertTrue($configuration->hasSharedInstances());
    }

    public function testAddImplements()
    {
        $configuration = $this->getConfiguration();
        $interfaceName = 'foo';

        $this->assertEquals(
            $configuration,
            $configuration->addImplements($interfaceName)
        );
    }

    public function testGetImplements()
    {
        $configuration = $this->getConfiguration();
        $interfaceName = 'foo';

        $this->assertEmpty($configuration->getImplements());

        $configuration->addImplements($interfaceName);
        $this->assertNotEmpty($configuration->getImplements());
        $this->assertCount(1, $configuration->getImplements());
    }

    public function testHasImplements()
    {
        $configuration = $this->getConfiguration();
        $interfaceName = 'foo';

        $this->assertFalse($configuration->hasImplements());

        $configuration->addImplements($interfaceName);
        $this->assertTrue($configuration->hasImplements());
    }

    public function testSetExtends()
    {
        $configuration = $this->getConfiguration();
        $parentClassName = 'foo';

        $this->assertEquals(
            $configuration,
            $configuration->setExtends($parentClassName)
        );
    }

    public function testHasExtends()
    {
        $configuration = $this->getConfiguration();
        $parentClassName = 'foo';

        $this->assertFalse($configuration->hasExtends());

        $configuration->setExtends($parentClassName);
        $this->assertTrue($configuration->hasExtends());
    }

    public function testGetExtends()
    {
        $configuration = $this->getConfiguration();
        $parentClassName = 'foo';

        $this->assertNull($configuration->getExtends());

        $configuration->setExtends($parentClassName);
        $this->assertEquals(
            $parentClassName,
            $configuration->getExtends()
        );
    }

    public function testAddUses()
    {
        $configuration = $this->getConfiguration();
        $className = 'foo';

        $this->assertEquals(
            $configuration,
            $configuration->addUses($className)
        );
    }

    public function testHasUses()
    {
        $configuration = $this->getConfiguration();
        $className = 'foo';

        $this->assertFalse($configuration->hasUses());

        $configuration->addUses($className);
        $this->assertTrue($configuration->hasUses());
    }

    public function testGetUses()
    {
        $configuration = $this->getConfiguration();
        $className = 'foo';

        $this->assertEmpty($configuration->getUseCollection());

        $configuration->addUses($className);
        $this->assertNotEmpty($configuration->getUseCollection());
        $this->assertCount(1, $configuration->getUseCollection());
    }
}