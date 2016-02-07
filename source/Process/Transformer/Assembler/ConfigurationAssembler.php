<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-05-31 
 */

namespace Net\Bazzline\Component\Locator\Process\Transformer\Assembler;

use Net\Bazzline\Component\Locator\Configuration\Configuration;
use Net\Bazzline\Component\ProcessPipe\ExecutableException;
use Net\Bazzline\Component\ProcessPipe\ExecutableInterface;

class ConfigurationAssembler implements ExecutableInterface
{
    /** @var Configuration */
    private $configuration;

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param array $input
     * @return array
     * @throws ExecutableException
     */
    public function execute($input = null)
    {
        if (!is_array($input)) {
            throw new ExecutableException(
                'input must be an array'
            );
        }

        /** @var \Net\Bazzline\Component\Locator\Configuration\Assembler\AssemblerInterface $assembler */
        $assembler              = new $input['assembler'];
        $configuration          = $this->configuration;
        $input['configuration'] = $assembler->assemble($input, $configuration);

        return $input;
    }
}