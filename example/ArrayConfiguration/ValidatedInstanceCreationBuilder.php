<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-24 
 */

use Net\Bazzline\Component\CodeGenerator\BlockGenerator;
use Net\Bazzline\Component\CodeGenerator\DocumentationGenerator;
use Net\Bazzline\Component\Locator\Configuration\Instance;
use Net\Bazzline\Component\Locator\MethodBodyBuilder\MethodBodyBuilderInterface;

/**
 * Class ValidatedInstanceCreationBuilder
 */
class ValidatedInstanceCreationBuilder implements MethodBodyBuilderInterface
{
    /**
     * @var Instance
     */
    private $instance;

    /**
     * @param \Net\Bazzline\Component\Locator\Configuration\Instance $instance
     * @return $this
     */
    public function setInstance(Instance $instance)
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * @param \Net\Bazzline\Component\CodeGenerator\BlockGenerator $body
     * @return \Net\Bazzline\Component\CodeGenerator\BlockGenerator
     * @throws \Net\Bazzline\Component\Locator\MethodBodyBuilder\RuntimeException
     */
    public function build(BlockGenerator $body)
    {
        $body
            ->add('//validate if instance class is available')
            ->add('if (class_exists(\'' . $this->instance->getClassName() . '\')) {')
            ->startIndention()
                ->add('//create new instance')
                ->add('$instance = new ' . $this->instance->getClassName() . '();')
            ->stopIndention()
            ->add('} else {')
            ->startIndention()
                ->add('throw new \InvalidArgumentException(\'provided class "' . $this->instance->getClassName() . '" does not exists\');')
            ->stopIndention()
            ->add('}')
            ->add('')
            ->add('return $instance;');

        return $body;
    }

    /**
     * @param DocumentationGenerator $documentation
     * @return DocumentationGenerator
     */
    public function extend(DocumentationGenerator $documentation)
    {
        $documentation->addThrows('\InvalidArgumentException');

        return $documentation;
    }
}