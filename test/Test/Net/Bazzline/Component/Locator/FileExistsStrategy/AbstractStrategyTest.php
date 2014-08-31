<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-09 
 */

namespace Test\Net\Bazzline\Component\Locator\FileExistsStrategy;

use org\bovigo\vfs\vfsStream;
use Test\Net\Bazzline\Component\Locator\LocatorTestCase;

/**
 * Class AbstractStrategyTest
 * @package Test\Net\Bazzline\Component\Locator\FileExistsStrategy
 */
class AbstractStrategyTest extends LocatorTestCase
{
    /**
     * @expectedException \Net\Bazzline\Component\Locator\FileExistsStrategy\InvalidArgumentException
     * @expectedExceptionMessage invalid filename given
     */
    public function testInvalidFileName()
    {
        $strategy = $this->getMockOfAbstractStrategy();

        $strategy->setFileName(null);
    }

    public function testValidFileName()
    {
        $strategy = $this->getMockOfAbstractStrategy();

        $strategy->setFileName('1');
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\FileExistsStrategy\InvalidArgumentException
     * @expectedExceptionMessage provided path "vfs://root/file" has to be a directory
     */
    public function testFilePathIsNotADirectory()
    {
        $strategy = $this->getMockOfAbstractStrategy();
        $root = vfsStream::setup('root', 0755);
        $file = vfsStream::newFile('file');
        $root->addChild($file);

        $strategy->setFilePath($file->url());
    }

    /**
     * @expectedException \Net\Bazzline\Component\Locator\FileExistsStrategy\InvalidArgumentException
     * @expectedExceptionMessage provided path "vfs://root" has to be writable
     */
    public function testFilePathIsNotAWritableDirectory()
    {
        $strategy = $this->getMockOfAbstractStrategy();
        $root = vfsStream::setup('root', 0444);

        $strategy->setFilePath($root->url());
    }

    public function testValidFilePath()
    {
        $strategy = $this->getMockOfAbstractStrategy();
        $root = vfsStream::setup('root', 0700);

        $strategy->setFilePath($root->url());
    }
}