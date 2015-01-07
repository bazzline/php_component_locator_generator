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

    /** @var LocatorInterfaceGenerator */
    private $locatorInterfaceGenerator;

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
     * @param \Net\Bazzline\Component\Locator\LocatorInterfaceGenerator $locatorInterfaceGenerator
     * @return $this
     */
    public function setLocatorInterfaceGenerator(LocatorInterfaceGenerator $locatorInterfaceGenerator)
    {
        $this->locatorInterfaceGenerator = $locatorInterfaceGenerator;

        return $this;
    }

    /**
     * @throws RuntimeException
     */
    public function generate()
    {
        //start of dependencies
        $configuration                      = $this->configuration;
        $fileExistsStrategy                 = $this->fileExistsStrategy;
        $invalidArgumentExceptionGenerator  = $this->invalidArgumentExceptionGenerator;
        $locatorGenerator                   = $this->locatorGenerator;
        $locatorInterfaceGenerator          = $this->locatorInterfaceGenerator;
        //end of dependencies

        //start of validation
        if (!is_dir($configuration->getFilePath())) {
            throw new RuntimeException(
                'provided path "' . $configuration->getFilePath() . '" is not a directory'
            );
        }

        if (!is_writable($configuration->getFilePath())) {
            throw new RuntimeException(
                'provided directory "' . $configuration->getFilePath() . '" is not writable'
            );
        }
        //start of validation

        //@todo why not add a "shouldBeGenerated" so that the generator can
        // decide on its own if it should be generated
        //than we could simple write a "foreach generators as generator ..."
        $locatorGenerator->setConfiguration($configuration);
        $locatorGenerator->setFileExistsStrategy($fileExistsStrategy);
        $locatorGenerator->generate();

        if (($configuration->hasFactoryInstances())
            || ($configuration->hasSharedInstances())) {
            $invalidArgumentExceptionGenerator->setConfiguration($configuration);
            $invalidArgumentExceptionGenerator->setFileExistsStrategy($fileExistsStrategy);
            $invalidArgumentExceptionGenerator->generate();
        }

echo var_export($configuration->createLocatorGeneratorInterface(), true) . PHP_EOL;
        if ($configuration->createLocatorGeneratorInterface()) {
            $locatorInterfaceGenerator->setConfiguration($this->configuration);
            $locatorInterfaceGenerator->setFileExistsStrategy($this->fileExistsStrategy);
            $locatorInterfaceGenerator->generate();
        }
    }
}
