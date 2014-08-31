<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-08-30 
 */

namespace Test\Net\Bazzline\Component\Locator;

/**
 * Class AbstractGeneratorTest
 * @package Test\Net\Bazzline\Component\Locator
 */
class AbstractGeneratorTest extends LocatorTestCase
{
    public function testSetters()
    {
        $generator = $this->getMockOfAbstractGenerator();
        $configuration = $this->getMockOfConfiguration();
        $strategy = $this->getMockOfAbstractStrategy();

        $this->assertEquals(
            $generator,
            $generator->setConfiguration($configuration)
        );
        $this->assertEquals(
            $generator,
            $generator->setFileExistsStrategy($strategy)
        );
    }
} 