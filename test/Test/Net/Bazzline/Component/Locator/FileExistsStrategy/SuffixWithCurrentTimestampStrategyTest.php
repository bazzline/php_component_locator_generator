<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-09 
 */

namespace Test\Net\Bazzline\Component\Locator\FileExistsStrategy;

use org\bovigo\vfs\vfsStream;
use Test\Net\Bazzline\Component\Locator\LocatorTestCase;

/**
 * Class SuffixWithCurrentTimestampStrategyTest
 * @package Test\Net\Bazzline\Component\Locator\FileExistsStrategy
 */
class SuffixWithCurrentTimestampStrategyTest extends LocatorTestCase
{
    public function testGetCurrentTimestampWithoutSettingIt()
    {
        $strategy = $this->getSuffixWithCurrentTimestampStrategy();

        $this->assertGreaterThanOrEqual(time(), $strategy->getCurrentTimeStamp());
    }

    public function testSetAndGetCurrentTimestamp()
    {
        $strategy = $this->getSuffixWithCurrentTimestampStrategy();
        $timestamp = __LINE__;

        $strategy->setCurrentTimeStamp($timestamp);

        $this->assertEquals($timestamp, $strategy->getCurrentTimeStamp());
    }

    public function testExecuteWithRenameableFile()
    {
        $strategy = $this->getSuffixWithCurrentTimestampStrategy();
        $timestamp = __LINE__;
        $root = vfsStream::setup('root', 0700);
        $file = vfsStream::newFile('file', 0700);

        $root->addChild($file);

        $strategy->setCurrentTimeStamp($timestamp);
        $strategy->setFileName('file');
        $strategy->setFilePath($root->url());

        $this->assertEquals($root->url() . DIRECTORY_SEPARATOR . 'file', $file->url());
        $strategy->execute();
        $this->assertEquals($root->url() . DIRECTORY_SEPARATOR . 'file.' . $timestamp, $file->url());
    }
}