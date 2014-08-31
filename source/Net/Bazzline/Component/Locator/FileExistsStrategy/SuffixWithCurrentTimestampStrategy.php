<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-05-26 
 */

namespace Net\Bazzline\Component\Locator\FileExistsStrategy;

/**
 * Class SuffixWithCurrentTimestampStrategy
 * @package Net\Bazzline\Component\Locator\FileExistsStrategy
 */
class SuffixWithCurrentTimestampStrategy extends AbstractStrategy
{
    public function __construct()
    {
        $this->currentTimeStamp = time();
    }

    /**
     * @var int
     */
    private $currentTimeStamp;

    /**
     * @param int $currentTimeStamp
     */
    public function setCurrentTimeStamp($currentTimeStamp)
    {
        $this->currentTimeStamp = (int) $currentTimeStamp;
    }

    /**
     * @return int
     * @throws RuntimeException
     */
    public function getCurrentTimeStamp()
    {
        if (is_null($this->currentTimeStamp)) {
            throw new RuntimeException(
                'current timestamp is mandatory'
            );
        }

        return $this->currentTimeStamp;
    }

    /**
     * @throws RuntimeException
     */
    public function execute()
    {
        $name = $this->getFileName();
        $path = $this->getFilePath();

        $newName = $path . DIRECTORY_SEPARATOR . $name . '.' . $this->getCurrentTimeStamp();
        $oldName = $path . DIRECTORY_SEPARATOR . $name;

        $fileCouldNotBeMoved = ((rename($oldName, $newName)) === false);

        if ($fileCouldNotBeMoved) {
            throw new RuntimeException(
                'could not move file from "' . $oldName . '" to "' . $newName . '"'
            );
        }
    }
}