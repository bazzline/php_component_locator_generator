<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-06-17 
 */

namespace Net\Bazzline\Component\Locator;

use Net\Bazzline\Component\CodeGenerator\ClassGenerator;
use Net\Bazzline\Component\CodeGenerator\FileGenerator;
use Net\Bazzline\Component\CodeGenerator\Factory\BlockGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\ClassGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\DocumentationGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\FileGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\MethodGeneratorFactory;
use Net\Bazzline\Component\CodeGenerator\Factory\PropertyGeneratorFactory;
use Net\Bazzline\Component\Locator\Configuration\Instance;

/**
 * Class LocatorGenerator
 * @package Net\Bazzline\Component\Locator
 */
class LocatorGenerator extends AbstractGenerator
{
    /**
     * @var BlockGeneratorFactory
     */
    protected $blockGeneratorFactory;

    /**
     * @var ClassGeneratorFactory
     */
    private $classGeneratorFactory;

    /**
     * @var DocumentationGeneratorFactory
     */
    private $documentationGeneratorFactory;

    /**
     * @var FileGeneratorFactory
     */
    private $fileGeneratorFactory;

    /**
     * @var MethodGeneratorFactory
     */
    protected $methodGeneratorFactory;

    /**
     * @var PropertyGeneratorFactory
     */
    protected $propertyGeneratorFactory;

    /**
     * @param \Net\Bazzline\Component\CodeGenerator\Factory\BlockGeneratorFactory $factory
     * @return $this
     */
    public function setBlockGeneratorFactory(BlockGeneratorFactory $factory)
    {
        $this->blockGeneratorFactory = $factory;

        return $this;
    }

    /**
     * @param \Net\Bazzline\Component\CodeGenerator\Factory\ClassGeneratorFactory $factory
     * @return $this
     */
    public function setClassGeneratorFactory(ClassGeneratorFactory $factory)
    {
        $this->classGeneratorFactory = $factory;

        return $this;
    }

    /**
     * @param \Net\Bazzline\Component\CodeGenerator\Factory\DocumentationGeneratorFactory $factory
     * @return $this
     */
    public function setDocumentationGeneratorFactory(DocumentationGeneratorFactory $factory)
    {
        $this->documentationGeneratorFactory = $factory;

        return $this;
    }

    /**
     * @param \Net\Bazzline\Component\CodeGenerator\Factory\FileGeneratorFactory $factory
     * @return $this
     */
    public function setFileGeneratorFactory(FileGeneratorFactory $factory)
    {
        $this->fileGeneratorFactory = $factory;

        return $this;
    }

    /**
     * @param \Net\Bazzline\Component\CodeGenerator\Factory\MethodGeneratorFactory $factory
     * @return $this
     */
    public function setMethodGeneratorFactory(MethodGeneratorFactory $factory)
    {
        $this->methodGeneratorFactory = $factory;

        return $this;
    }

    /**
     * @param \Net\Bazzline\Component\CodeGenerator\Factory\PropertyGeneratorFactory $factory
     * @return $this
     */
    public function setPropertyGeneratorFactory(PropertyGeneratorFactory $factory)
    {
        $this->propertyGeneratorFactory = $factory;

        return $this;
    }

    /**
     * @throws RuntimeException
     */
    public function generate()
    {
        $this->moveOldFileIfExists(
            $this->configuration->getFilePath(),
            $this->configuration->getFileName()
        );

        $fileGenerator = $this->createFile($this->fileGeneratorFactory->create());
        $classGenerator = $this->createClass(
            $this->classGeneratorFactory->create(),
            $this->configuration,
            $this->documentationGeneratorFactory
        );

        if ($this->configuration->hasInstances()) {
            $classGenerator = $this->addInstanceFetching(
                $this->blockGeneratorFactory,
                $classGenerator,
                $this->configuration,
                $this->documentationGeneratorFactory,
                $this->methodGeneratorFactory
            );

            if ($this->configuration->hasFactoryInstances()) {
                $classGenerator = $this->addFactoryInstancePooling(
                    $this->blockGeneratorFactory,
                    $classGenerator,
                    $this->documentationGeneratorFactory,
                    $this->methodGeneratorFactory,
                    $this->propertyGeneratorFactory
                );
            }

            if ($this->configuration->hasSharedInstances()) {
                $classGenerator = $this->addSharedInstancePooling(
                    $this->blockGeneratorFactory,
                    $classGenerator,
                    $this->documentationGeneratorFactory,
                    $this->methodGeneratorFactory,
                    $this->propertyGeneratorFactory
                );
            }
        }

        $fileGenerator->addClass($classGenerator);
        $fileContent = $fileGenerator->generate();

        $fullQualifiedPathName = $this->configuration->getFilePath() .
            DIRECTORY_SEPARATOR . $this->configuration->getFileName();
        $this->dumpToFile($fullQualifiedPathName, $fileContent);
    }

    /**
     * @param FileGenerator $fileGenerator
     * @return FileGenerator
     */
    private function createFile(FileGenerator $fileGenerator)
    {
        $fileGenerator->addFileContent(
            array(
                '/**',
                ' * @author Net\Bazzline\Component\Locator',
                ' * @since ' . date('Y-m-d'),
                ' */'
            )
        );

        return $fileGenerator;
    }

    /**
     * @param ClassGenerator $classGenerator
     * @param Configuration $configuration
     * @param DocumentationGeneratorFactory $documentationGeneratorFactory
     * @return ClassGenerator
     */
    private function createClass(ClassGenerator $classGenerator, Configuration $configuration, DocumentationGeneratorFactory $documentationGeneratorFactory)
    {
        $documentationGenerator = $documentationGeneratorFactory->create();
        $documentationGenerator->setClass($configuration->getClassName());
        if ($configuration->hasNamespace()) {
            $documentationGenerator->setPackage($configuration->getNamespace());
        }

        $classGenerator->setDocumentation($documentationGenerator);
        $classGenerator->setName($configuration->getClassName());

        if ($configuration->hasNamespace()) {
            $classGenerator->setNamespace($configuration->getNamespace());
        }

        if ($configuration->hasExtends()) {
            $classGenerator->setExtends($configuration->getExtends());
        }

        if ($configuration->hasUses()) {
            foreach ($configuration->getUseCollection() as $use) {
                $classGenerator->addUse($use->getClassName(), $use->getAlias());
            }
        }

        if ($configuration->hasImplements()) {
            foreach ($configuration->getImplements() as $interfaceName) {
                $classGenerator->addImplements($interfaceName);
            }
        }

        return $classGenerator;
    }

    /**
     * @param BlockGeneratorFactory $blockGeneratorFactory
     * @param ClassGenerator $classGenerator
     * @param DocumentationGeneratorFactory $documentationGeneratorFactory
     * @param MethodGeneratorFactory $methodGeneratorFactory
     * @param PropertyGeneratorFactory $propertyGeneratorFactory
     * @return ClassGenerator
     */
    private function addFactoryInstancePooling(BlockGeneratorFactory $blockGeneratorFactory, ClassGenerator $classGenerator, DocumentationGeneratorFactory $documentationGeneratorFactory, MethodGeneratorFactory $methodGeneratorFactory, PropertyGeneratorFactory $propertyGeneratorFactory)
    {
        //----begin of property
        $factoryInstancePool = $propertyGeneratorFactory->create();

        $factoryInstancePool->setDocumentation($documentationGeneratorFactory->create());
        $factoryInstancePool->setName('factoryInstancePool');
        $factoryInstancePool->markAsPrivate();
        $factoryInstancePool->setValue('array()');
        //----end of property

        //----begin of methods
        //----begin of fetch from factory instance pool
        $fetchFromFactoryInstancePoolBody = $blockGeneratorFactory->create();
        $fetchFromFactoryInstancePool = $methodGeneratorFactory->create();

        $fetchFromFactoryInstancePool->setDocumentation($documentationGeneratorFactory->create());
        $fetchFromFactoryInstancePool->setName('fetchFromFactoryInstancePool');
        $fetchFromFactoryInstancePool->addParameter('className', null, 'string');
        $fetchFromFactoryInstancePool->markAsProtected();
        $fetchFromFactoryInstancePool->markAsFinal();

        $fetchFromFactoryInstancePoolBody
            ->add('if ($this->isNotInFactoryInstancePool($className)) {')
            ->startIndention()
                ->add('if (!class_exists($className)) {')
                ->startIndention()
                    ->add('throw new InvalidArgumentException(')
                    ->startIndention()
                        ->add('\'factory class "\' . $className . \'" does not exist\'')
                    ->stopIndention()
                    ->add(');')
                ->stopIndention()
                ->add('}')
                ->add('')
                ->add('/** @var FactoryInterface $factory */')
                ->add('$factory = new $className();')
                ->add('$factory->setLocator($this);')
                ->add('$this->addToFactoryInstancePool($className, $factory);')
            ->stopIndention()
            ->add('}')
            ->add('')
            ->add('return $this->getFromFactoryInstancePool($className);');

        $fetchFromFactoryInstancePool->setBody($fetchFromFactoryInstancePoolBody, array('FactoryInterface'));
        $fetchFromFactoryInstancePool->getDocumentation()->addThrows('InvalidArgumentException');
        //----end of fetch from factory instance pool

        //----begin of fetch from factory instance pool
        $addToFactoryInstancePoolBody = $blockGeneratorFactory->create();
        $addToFactoryInstancePool = $methodGeneratorFactory->create();

        $addToFactoryInstancePool->setDocumentation($documentationGeneratorFactory->create());
        $addToFactoryInstancePool->setName('addToFactoryInstancePool');
        $addToFactoryInstancePool->addParameter('className', null, 'string');
        $addToFactoryInstancePool->addParameter('factory', null, 'FactoryInterface');
        $addToFactoryInstancePool->markAsPrivate();

        $addToFactoryInstancePoolBody
            ->add('$this->factoryInstancePool[$className] = $factory;')
            ->add('')
            ->add('return $this;');

        $addToFactoryInstancePool->setBody($addToFactoryInstancePoolBody, array('$this'));
        //----end of fetch from factory instance pool

        //----begin of get from factory instance pool
        $getFromFactoryInstancePoolBody = $blockGeneratorFactory->create();
        $getFromFactoryInstancePool = $methodGeneratorFactory->create();

        $getFromFactoryInstancePool->setDocumentation($documentationGeneratorFactory->create());
        $getFromFactoryInstancePool->setName('getFromFactoryInstancePool');
        $getFromFactoryInstancePool->addParameter('className', null, 'string');
        $getFromFactoryInstancePool->markAsPrivate();

        $getFromFactoryInstancePoolBody
            ->add('return $this->factoryInstancePool[$className];');

        $getFromFactoryInstancePool->setBody($getFromFactoryInstancePoolBody, array('null', 'FactoryInterface'));
        //----end of get from factory instance pool

        //----begin of is not in factory instance pool
        $isNotInFactoryInstancePoolBody = $blockGeneratorFactory->create();
        $isNotInFactoryInstancePool = $methodGeneratorFactory->create();

        $isNotInFactoryInstancePool->setDocumentation($documentationGeneratorFactory->create());
        $isNotInFactoryInstancePool->setName('isNotInFactoryInstancePool');
        $isNotInFactoryInstancePool->addParameter('className', null, 'string');
        $isNotInFactoryInstancePool->markAsPrivate();

        $isNotInFactoryInstancePoolBody
            ->add('return (!isset($this->factoryInstancePool[$className]));');

        $isNotInFactoryInstancePool->setBody($isNotInFactoryInstancePoolBody, array('boolean'));
        //----end of is not in factory instance pool
        //----begin of methods

        //----begin of adding to class
        $classGenerator->addProperty($factoryInstancePool);

        //----protected
        $classGenerator->addMethod($fetchFromFactoryInstancePool);
        //----private
        $classGenerator->addMethod($addToFactoryInstancePool);
        $classGenerator->addMethod($getFromFactoryInstancePool);
        $classGenerator->addMethod($isNotInFactoryInstancePool);
        //----end of adding to class

        return $classGenerator;
    }

    /**
     * @param BlockGeneratorFactory $blockGeneratorFactory
     * @param ClassGenerator $classGenerator
     * @param DocumentationGeneratorFactory $documentationGeneratorFactory
     * @param MethodGeneratorFactory $methodGeneratorFactory
     * @param PropertyGeneratorFactory $propertyGeneratorFactory
     * @return ClassGenerator
     */
    private function addSharedInstancePooling(BlockGeneratorFactory $blockGeneratorFactory, ClassGenerator $classGenerator, DocumentationGeneratorFactory $documentationGeneratorFactory, MethodGeneratorFactory $methodGeneratorFactory, PropertyGeneratorFactory $propertyGeneratorFactory)
    {
        //----begin of property
        $sharedInstancePool = $propertyGeneratorFactory->create();

        $sharedInstancePool->setDocumentation($documentationGeneratorFactory->create());
        $sharedInstancePool->setName('sharedInstancePool');
        $sharedInstancePool->markAsPrivate();
        $sharedInstancePool->setValue('array()');
        //----end of property

        //----begin of methods
        //----begin of fetch from shared instance pool
        //----end of fetch from shared instance pool
        $fetchFromSharedInstancePoolBody = $blockGeneratorFactory->create();
        $fetchFromSharedInstancePool = $methodGeneratorFactory->create();

        $fetchFromSharedInstancePool->setDocumentation($documentationGeneratorFactory->create());
        $fetchFromSharedInstancePool->setName('fetchFromSharedInstancePool');
        $fetchFromSharedInstancePool->addParameter('className', null, 'string');
        $fetchFromSharedInstancePool->markAsProtected();
        $fetchFromSharedInstancePool->markAsFinal();

        $fetchFromSharedInstancePoolBody
            ->add('if ($this->isNotInFactoryInstancePool($className)) {')
            ->startIndention()
                ->add('if (!class_exists($className)) {')
                ->startIndention()
                    ->add('throw new InvalidArgumentException(')
                    ->startIndention()
                        ->add('\'class "\' . $className . \'" does not exist\'')
                    ->stopIndention()
                    ->add(');')
                ->stopIndention()
                ->add('}')
                ->add('')
                ->add('$instance = new $className();')
                ->add('$this->addToFactoryInstancePool($className, $instance);')
            ->stopIndention()
            ->add('}')
            ->add('')
            ->add('return $this->getFromSharedInstancePool($className);');

        $fetchFromSharedInstancePool->setBody($fetchFromSharedInstancePoolBody, array('object'));
        $fetchFromSharedInstancePool->getDocumentation()->addThrows('InvalidArgumentException');
        //----begin of add to shared instance pool
        $addToSharedInstancePoolBody = $blockGeneratorFactory->create();
        $addToSharedInstancePool = $methodGeneratorFactory->create();

        $addToSharedInstancePool->setDocumentation($documentationGeneratorFactory->create());
        $addToSharedInstancePool->setName('addToSharedInstancePool');
        $addToSharedInstancePool->addParameter('className', null, 'string');
        $addToSharedInstancePool->addParameter('instance', null, 'object');
        $addToSharedInstancePool->markAsPrivate();

        $addToSharedInstancePoolBody
            ->add('$this->sharedInstancePool[$className] = $instance;')
            ->add('')
            ->add('return $this;');

        $addToSharedInstancePool->setBody($addToSharedInstancePoolBody, array('$this'));
        //----end of add to shared instance pool
        //----begin of get from shared instance pool
        $getFromSharedInstancePoolBody = $blockGeneratorFactory->create();
        $getFromSharedInstancePool = $methodGeneratorFactory->create();

        $getFromSharedInstancePool->setDocumentation($documentationGeneratorFactory->create());
        $getFromSharedInstancePool->setName('getFromSharedInstancePool');
        $getFromSharedInstancePool->addParameter('className', null, 'string');
        $getFromSharedInstancePool->markAsPrivate();

        $getFromSharedInstancePoolBody
            ->add('return $this->sharedInstancePool[$className];');

        $getFromSharedInstancePool->setBody($getFromSharedInstancePoolBody, array('null', 'object'));
        //----end of get from shared instance pool
        //----begin of is not in shared instance pool
        $isNotInSharedInstancePoolBody = $blockGeneratorFactory->create();
        $isNotInSharedInstancePool = $methodGeneratorFactory->create();

        $isNotInSharedInstancePool->setDocumentation($documentationGeneratorFactory->create());
        $isNotInSharedInstancePool->setName('isNotInSharedInstancePool');
        $isNotInSharedInstancePool->addParameter('className', null, 'string');
        $isNotInSharedInstancePool->markAsPrivate();

        $isNotInSharedInstancePoolBody
            ->add('return (!isset($this->sharedInstancePool[$className]));');

        $isNotInSharedInstancePool->setBody($isNotInSharedInstancePoolBody, array('boolean'));
        //----begin of is not in shared instance pool
        //----begin of methods

        //----begin of adding to class
        $classGenerator->addProperty($sharedInstancePool);

        //----protected
        $classGenerator->addMethod($fetchFromSharedInstancePool);
        //----private
        $classGenerator->addMethod($addToSharedInstancePool);
        $classGenerator->addMethod($getFromSharedInstancePool);
        $classGenerator->addMethod($isNotInSharedInstancePool);
        //----end of adding to class

        return $classGenerator;
    }

    /**
     * @param BlockGeneratorFactory $blockGeneratorFactory
     * @param ClassGenerator $classGenerator
     * @param Configuration $configuration
     * @param DocumentationGeneratorFactory $documentationGeneratorFactory
     * @param MethodGeneratorFactory $methodGeneratorFactory
     * @return ClassGenerator
     */
    private function addInstanceFetching(BlockGeneratorFactory $blockGeneratorFactory, ClassGenerator $classGenerator, Configuration $configuration, DocumentationGeneratorFactory $documentationGeneratorFactory, MethodGeneratorFactory $methodGeneratorFactory)
    {
        foreach ($configuration->getInstances() as $instance) {
            $body = $blockGeneratorFactory->create();
            $documentation = $documentationGeneratorFactory->create();
            $method = $methodGeneratorFactory->create();
            $methodBuilder = $instance->getMethodBodyBuilder();
            $returnValue = ($instance->hasReturnValue()) ? $instance->getReturnValue() : $instance->getClassName();

            $body = $methodBuilder->build($body);
            $documentation = $methodBuilder->extend($documentation);
            $method->setBody($body, array($returnValue));

            if ($instance->hasAlias()) {
                $methodName = $instance->getAlias();
            } else {
                $methodName = (str_replace('\\', '' , $instance->getClassName()));
            }
            $methodName = $configuration->getMethodPrefix() . ucfirst($methodName);

            $method->setDocumentation($documentation);
            $method->setName($methodName);
            $method->markAsPublic();

            $classGenerator->addMethod($method);
        }

        return $classGenerator;
    }
}