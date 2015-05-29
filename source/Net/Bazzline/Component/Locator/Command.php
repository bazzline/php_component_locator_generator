<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-09-03
 */

namespace Net\Bazzline\Component\Locator;

use Exception;
use Net\Bazzline\Component\Locator\Configuration\Validator\ArrayKeyIsValidClassName;
use Net\Bazzline\Component\Locator\Configuration\Validator\ReadableFilePath;

/**
 * Class Command
 * @package Net\Bazzline\Component\Locator
 */
class Command
{
    /** @var array */
    private $arguments;

    /** @var Configuration */
    private $configuration;

    /**
     * @param array $arguments
     * @throws Exception
     */
    public function setArguments(array $arguments)
    {
        $this->validateArguments($arguments);
        $this->arguments = $arguments;
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        $this->validateEnvironment();
        //bo: exclude in own class, ConfigurationFromFileProvider
        $pathToConfigurationFile = $this->buildPathToConfigurationFromArguments();
        $data = $this->buildDataFromPathToConfigurationFile($pathToConfigurationFile);
        //eo: exclude in own class
        //bo: process pipe
        $this->generate($data);
        //eo: process pipe
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @throws Exception
     */
    private function validateEnvironment()
    {
        $isNotCalledFromCommandLineInterface = (PHP_SAPI !== 'cli');

        if ($isNotCalledFromCommandLineInterface) {
            throw new Exception(
                'This script can only be called from the command line'
            );
        }
    }

    /**
     * @param array $arguments
     * @throws Exception
     */
    private function validateArguments(array $arguments)
    {
        if (count($arguments) !== 2) {
            throw new Exception(
                'called with invalid number of arguments' . PHP_EOL . '   ' . basename(__FILE__) . ' <path to configuration file>'
            );
        }
    }

    /**
     * @return string
     */
    private function buildPathToConfigurationFromArguments()
    {
        $cwd            = getcwd();
        $path           = $this->arguments[1];
        $isRelativePath = ($path[0] !== '/');

        if ($isRelativePath) {
            $pathToConfigurationFile = realpath($cwd . DIRECTORY_SEPARATOR . $path);
        } else {
            $pathToConfigurationFile = realpath($path);
        }

        return $pathToConfigurationFile;
    }

    /**
     * @param string $pathToConfigurationFile
     * @return array
     * @throws Exception
     */
    private function buildDataFromPathToConfigurationFile($pathToConfigurationFile)
    {
        //@todo inject
        $pathValidator  = new ReadableFilePath();
        $classValidator = new ArrayKeyIsValidClassName();

        $pathValidator->validate($pathToConfigurationFile);

        $data = require_once $pathToConfigurationFile;

        $classValidator->validate($data, 'assembler');
        $classValidator->validate($data, 'file_exists_strategy');
        $this->validateBootstrapFile($data);

        return $data;
    }

    /**
     * @param $data
     * @throws RuntimeException
     */
    private function generate($data)
    {
        /**
         * @var \Net\Bazzline\Component\Locator\Configuration\Assembler\AssemblerInterface $assembler
         * @var \Net\Bazzline\Component\Locator\FileExistsStrategy\FileExistsStrategyInterface $fileExistsStrategy
         */
        $assembler              = new $data['assembler']();
        $configurationFactory   = new ConfigurationFactory();
        $fileExistsStrategy     = new $data['file_exists_strategy']();
        $generatorFactory       = new GeneratorFactory();

        $this->configuration = $configurationFactory->create();
        $generator = $generatorFactory->create();

        $assembler->setConfiguration($this->configuration);
        $assembler->assemble($data);

        $generator->setConfiguration($assembler->getConfiguration());
        $generator->setFileExistsStrategy($fileExistsStrategy);
        $generator->generate();
    }

    /**
     * @param array $data
     * @throws Exception
     */
    private function validateBootstrapFile(array $data)
    {
        if (isset($data['bootstrap_file'])) {
            if (!file_exists($data['bootstrap_file'])) {
                throw new Exception(
                    'provided bootstrap file "' . $data['bootstrap_file'] . '" does not exist'
                );
            }

            if (!is_readable($data['bootstrap_file'])) {
                throw new Exception(
                    'provided bootstrap file "' . $data['bootstrap_file'] . '" is not readable'
                );
            }

            require_once $data['bootstrap_file'];
        }
    }
} 