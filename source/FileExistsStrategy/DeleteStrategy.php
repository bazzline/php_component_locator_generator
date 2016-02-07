<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-07
 */

namespace Net\Bazzline\Component\Locator\FileExistsStrategy;

/**
 * Class DeleteStrategy
 * @package Net\Bazzline\Component\Locator\FileExistsStrategy
 */
class DeleteStrategy extends AbstractStrategy
{
    /**
     * @throws RuntimeException
     */
    public function execute()
    {
        $fullQualifiedFilePath = $this->getFilePath() . DIRECTORY_SEPARATOR .
            $this->getFileName();
        $fileCouldNotBeRemoved = (unlink($fullQualifiedFilePath) !== true);

        if ($fileCouldNotBeRemoved) {
            throw new RuntimeException(
                'could not delete file "' . $fullQualifiedFilePath . '"'
            );
        }
    }
}