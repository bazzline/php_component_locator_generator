<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-09 
 */

namespace Test\Net\Bazzline\Component\Locator\Configuration;

use Test\Net\Bazzline\Component\Locator\LocatorTestCase;

/**
 * Class InstanceTest
 * @package Test\Net\Bazzline\Component\Locator\Configuration
 */
class InstanceTest extends LocatorTestCase
{
    public function testWithInvalidAlias()
    {
        $instance = $this->getInstance();
        $alias = '';

        $this->assertFalse($instance->hasAlias());
        $this->assertNull($instance->getAlias());
        $this->assertEquals(
            $instance,
            $instance->setAlias($alias)
        );
        $this->assertFalse($instance->hasAlias());
        $this->assertNull($instance->getAlias());
    }

    public function testWithValidAlias()
    {
        $instance = $this->getInstance();
        $alias = 'foo';

        $this->assertFalse($instance->hasAlias());
        $this->assertNull($instance->getAlias());
        $this->assertEquals(
            $instance,
            $instance->setAlias($alias)
        );
        $this->assertTrue($instance->hasAlias());
        $this->assertEquals(
            $alias,
            $instance->getAlias()
        );
    }

    public function testClassName()
    {
        $instance = $this->getInstance();
        $className = 'foo';

        $this->assertNull($instance->getClassName());
        $this->assertEquals(
            $instance,
            $instance->setClassName($className)
        );
        $this->assertEquals(
            $className,
            $instance->getClassName()
        );
    }

    public function testIsFactory()
    {
        $instance = $this->getInstance();

        $this->assertFalse($instance->isFactory());
        $this->assertEquals(
            $instance,
            $instance->setIsFactory(true)
        );
        $this->assertTrue($instance->isFactory());
        $this->assertEquals(
            $instance,
            $instance->setIsFactory(false)
        );
        $this->assertFalse($instance->isFactory());
    }

    public function testIsShared()
    {
        $instance = $this->getInstance();

        $this->assertTrue($instance->isShared());
        $this->assertEquals(
            $instance,
            $instance->setIsShared(true)
        );
        $this->assertTrue($instance->isShared());
        $this->assertEquals(
            $instance,
            $instance->setIsShared(false)
        );
        $this->assertFalse($instance->isShared());
    }

    public function testWithInvalidReturnValue()
    {
        $instance = $this->getInstance();
        $returnValue = '';

        $this->assertFalse($instance->hasReturnValue());
        $this->assertNull($instance->getReturnValue());
        $this->assertEquals(
            $instance,
            $instance->setReturnValue($returnValue)
        );
        $this->assertFalse($instance->hasReturnValue());
        $this->assertNull($instance->getReturnValue());
    }

    public function testWithValidReturnValue()
    {
        $instance = $this->getInstance();
        $returnValue = 'foo';

        $this->assertFalse($instance->hasReturnValue());
        $this->assertNull($instance->getReturnValue());
        $this->assertEquals(
            $instance,
            $instance->setReturnValue($returnValue)
        );
        $this->assertTrue($instance->hasReturnValue());
        $this->assertEquals($returnValue, $instance->getReturnValue());
    }
}