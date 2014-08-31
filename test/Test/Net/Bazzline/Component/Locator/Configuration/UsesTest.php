<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-09 
 */

namespace Test\Net\Bazzline\Component\Locator\Configuration;

use Test\Net\Bazzline\Component\Locator\LocatorTestCase;

/**
 * Class UsesTest
 * @package Test\Net\Bazzline\Component\Locator\Configuration
 */
class UsesTest  extends LocatorTestCase
{
    public function testAlias()
    {
        $uses = $this->getUses();
        $alias = 'foo';

        $this->assertNull($uses->getAlias());
        $this->assertEquals(
            $uses,
            $uses->setAlias($alias)
        );
        $this->assertEquals(
            $alias,
            $uses->getAlias()
        );
    }

    public function testClassName()
    {
        $uses = $this->getUses();
        $className = 'foo';

        $this->assertNull($uses->getClassName());
        $this->assertEquals(
            $uses,
            $uses->setClassName($className)
        );
        $this->assertEquals(
            $className,
            $uses->getClassName()
        );
    }
} 