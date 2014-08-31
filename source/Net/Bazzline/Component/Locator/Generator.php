<?php
/**
 * @author sleibelt
 * @since 2014-04-24
 */

namespace Net\Bazzline\Component\Locator;

/**
 * Class Generator
 *
 * @package Net\Bazzline\Component\Locator
 */
class Generator extends AbstractGenerator
{
    /** @var FactoryInterfaceGenerator */
    private $factoryInterfaceGenerator;

    /** @var InvalidArgumentExceptionGenerator */
    private $invalidArgumentExceptionGenerator;

    /** @var LocatorGenerator */
    private $locatorGenerator;

    /**
     * @param \Net\Bazzline\Component\Locator\FactoryInterfaceGenerator $factoryInterfaceGenerator
     * @return $this
     */
    public function setFactoryInterfaceGenerator(FactoryInterfaceGenerator $factoryInterfaceGenerator)
    {
        $this->factoryInterfaceGenerator = $factoryInterfaceGenerator;

        return $this;
    }

    /**
     * @param \Net\Bazzline\Component\Locator\InvalidArgumentExceptionGenerator $invalidArgumentExceptionGenerator
     * @return $this
     */
    public function setInvalidArgumentExceptionGenerator(InvalidArgumentExceptionGenerator $invalidArgumentExceptionGenerator)
    {
        $this->invalidArgumentExceptionGenerator = $invalidArgumentExceptionGenerator;

        return $this;
    }

    /**
     * @param \Net\Bazzline\Component\Locator\LocatorGenerator $locatorGenerator
     * @return $this
     */
    public function setLocatorGenerator(LocatorGenerator $locatorGenerator)
    {
        $this->locatorGenerator = $locatorGenerator;

        return $this;
    }



    /**
     * @throws RuntimeException
     */
    public function generate()
    {
        if (!is_dir($this->configuration->getFilePath())) {
            throw new RuntimeException(
                'provided path "' . $this->configuration->getFilePath() . '" is not a directory'
            );
        }

        if (!is_writable($this->configuration->getFilePath())) {
            throw new RuntimeException(
                'provided directory "' . $this->configuration->getFilePath() . '" is not writable'
            );
        }

        $this->locatorGenerator->setConfiguration($this->configuration);
        $this->locatorGenerator->setFileExistsStrategy($this->fileExistsStrategy);
        $this->locatorGenerator->generate();

        if ($this->configuration->hasFactoryInstances()) {
            $this->factoryInterfaceGenerator->setConfiguration($this->configuration);
            $this->factoryInterfaceGenerator->setFileExistsStrategy($this->fileExistsStrategy);
            $this->factoryInterfaceGenerator->generate();
        }

        if (($this->configuration->hasFactoryInstances())
            || ($this->configuration->hasSharedInstances())) {
            $this->invalidArgumentExceptionGenerator->setConfiguration($this->configuration);
            $this->invalidArgumentExceptionGenerator->setFileExistsStrategy($this->fileExistsStrategy);
            $this->invalidArgumentExceptionGenerator->generate();
        }
    }
}
