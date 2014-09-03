<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-09-03
 */

namespace Net\Bazzline\Component\Locator;

use Exception;

/**
 * Class Command
 * @package Net\Bazzline\Component\Locator
 */
class Command
{
    /**
     * @var array
     */
    private $arguments;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param array $arguments
     * @throws Exception
     */
    public function setArguments(array $arguments)
    {
        if (count($arguments) !== 2) {
            throw new Exception(
                'called with invalid number of arguments'
            );
        }

        $this->arguments = $arguments;
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        $isNotCalledFromCommandLineInterface = (PHP_SAPI !== 'cli');

        if ($isNotCalledFromCommandLineInterface) {
            throw new Exception(
                'This script can only be called from the command line'
            );
        }

        $cwd = getcwd();
        $pathToConfigurationFile = realpath($cwd . DIRECTORY_SEPARATOR . $this->arguments[1]);

        //----begin of validation
        if (!is_file($pathToConfigurationFile)) {
            throw new Exception(
                'provided path "' . $pathToConfigurationFile . '" is not a file'
            );
        }

        if (!is_readable($pathToConfigurationFile)) {
            throw new Exception(
                'file "' . $pathToConfigurationFile . '" is not readable'
            );
        }

        $data = require_once $pathToConfigurationFile;

        if (!isset($data['assembler'])) {
            throw new Exception(
                'data array must contain content for key "assembler"'
            );
        }

        if (!class_exists($data['assembler'])) {
            throw new Exception(
                'provided assembler "' . $data['assembler'] . '" does not exist'
            );
        }

        if (!isset($data['file_exists_strategy'])) {
            throw new Exception(
                'data array must contain content for key "file_exists_strategy"'
            );
        }

        if (!class_exists($data['file_exists_strategy'])) {
            throw new Exception(
                'provided file exists strategy "' . $data['file_exists_strategy'] . '" does not exist'
            );
        }

        if ($data['bootstrap_file']) {
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
        //----end of validation

        /**
         * @var \Net\Bazzline\Component\Locator\Configuration\Assembler\AssemblerInterface $assembler
         * @var \Net\Bazzline\Component\Locator\FileExistsStrategy\FileExistsStrategyInterface $fileExistsStrategy
         */
        $assembler = new $data['assembler']();
        $configurationFactory = new ConfigurationFactory();
        $fileExistsStrategy = new $data['file_exists_strategy']();
        $generatorFactory = new GeneratorFactory();

        $this->configuration = $configurationFactory->create();
        $generator = $generatorFactory->create();

        $assembler->setConfiguration($this->configuration);
        $assembler->assemble($data);

        $generator->setConfiguration($assembler->getConfiguration());
        $generator->setFileExistsStrategy($fileExistsStrategy);
        $generator->generate();
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
} 