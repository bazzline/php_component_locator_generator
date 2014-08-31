<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-09 
 */

namespace Test\Net\Bazzline\Component\Locator\Configuration\Assembler;

use Test\Net\Bazzline\Component\Locator\LocatorTestCase;

/**
 * Class AbstractAssemblerTest
 * @package Test\Net\Bazzline\Component\Locator\Configuration\Assembler
 */
class AbstractAssemblerTest extends LocatorTestCase
{
    /**
     * @expectedException \Net\Bazzline\Component\Locator\Configuration\Assembler\RuntimeException
     * @expectedExceptionMessage configuration is mandatory
     */
    public function testWithGetConfigurationWithNoConfigurationSet()
    {
        $assembler = $this->getMockOfAbstractAssembler();
        $assembler->getConfiguration();
    }

    public function testSetAndSetConfiguration()
    {
        $assembler = $this->getMockOfAbstractAssembler();
        $configuration = $this->getConfiguration();

        $this->assertEquals($assembler, $assembler->setConfiguration($configuration));
        $this->assertEquals($configuration, $assembler->getConfiguration());
    }
} 