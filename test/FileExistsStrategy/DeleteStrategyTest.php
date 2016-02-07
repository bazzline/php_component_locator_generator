<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-09 
 */

namespace Test\Net\Bazzline\Component\Locator\FileExistsStrategy;

use org\bovigo\vfs\vfsStream;
use Test\Net\Bazzline\Component\Locator\LocatorTestCase;

/**
 * Class DeleteStrategyTest
 * @package Test\Net\Bazzline\Component\Locator\FileExistsStrategy
 */
class DeleteStrategyTest extends LocatorTestCase
{
    public function testExecuteWithDeletableFile()
    {
        $strategy   = $this->getDeleteStrategy();
        $root       = vfsStream::setup('root', 0700);
        $directory  = vfsStream::newDirectory('directory');
        $file       = vfsStream::newFile('file', 0700);

        $directory->addChild($file);
        $root->addChild($directory);

        $strategy->setFilePath($directory->url());
        $strategy->setFileName('file');

        $strategy->execute();
    }
}