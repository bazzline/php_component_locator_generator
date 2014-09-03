# Locator Generator

This component easy up locator generation based on configuration files.
Out of the box, it creates locator from an array configuration file and from propel1 schema.xml files.

It is using the [php code generator component](https://github.com/bazzline/php_component_code_generator) as a robust base for code generation.

The build status of the current master branch is tracked by Travis CI:
[![Build Status](https://travis-ci.org/bazzline/php_component_locator_generator.png?branch=master)](http://travis-ci.org/bazzline/php_component_locator_generator)

The scrutinizer status are:
[![code quality](https://scrutinizer-ci.com/g/bazzline/php_component_locator_generator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bazzline/php_component_locator_generator/) | [![code coverage](https://scrutinizer-ci.com/g/bazzline/php_component_locator_generator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bazzline/php_component_locator_generator/) | [![build status](https://scrutinizer-ci.com/g/bazzline/php_component_locator_generator/badges/build.png?b=master)](https://scrutinizer-ci.com/g/bazzline/php_component_locator_generator/)

The versioneye status is:
[![dependencies](https://www.versioneye.com/user/projects/53e48c23e0a229172f000146/badge.svg?style=flat)](https://www.versioneye.com/user/projects/53e48c23e0a229172f000146)

Downloads:
[![Downloads this Month](https://img.shields.io/packagist/dm/net_bazzline/php_component_locator_generator.svg)](https://packagist.org/packages/net_bazzline/php_component_locator_generator)

It is also available at [openhub.net](http://www.openhub.net/p/718779).

# Why

* don't like "serviceLocator->get('foo')" (inexplicit API) calls
* like the configurable approach of some service locators out there
* inspired by a [php usergroup](http://artodeto.bazzline.net/archives/525-Social-Human-Architecture-for-Beginners-and-the-Flip-Side-of-Dependency-Injection-PHPUGHH.html) presentation called "[the flipside of dependency injection](http://thephp.cc/dates/2014/phpughh/the-flip-side-of-dependency-injection)" i'Ve seen "i'm not alone"
* generated code is easy debug- and understandable (no magic inside)

# How

* a task specific configuration assembler creates a unified configuration object
* unified configuration object is injected into the locator generator
* the locator generator creates needed files
* a file exists strategy can take care how to deal with existing files


# Install

## Manuel

    mkdir -p vendor/net_bazzline/php_component_locator_generator
    cd vendor/net_bazzline/php_component_locator_generator
    git clone https://github.com/bazzline/php_component_locator_generator

## With [Packagist](https://packagist.org/packages/net_bazzline/php_component_locator_generator)

    composer require net_bazzline/php_component_locator_generator:dev-master

# Example

## Array Configuration File

Take a Look to [configuration file](https://github.com/bazzline/php_component_locator_generator/blob/master/example/ArrayConfiguration/configuration.php).

### How To Create

```shell
cd <component root directory>
php bin/generateLocator.php example/ArrayConfiguration/configuration.php
ls data/
vim data/FromArrayConfigurationFileLocator.php
```

### Generated Code

```php
<?php
/**
 * @author Net\Bazzline\Component\Locator
 * @since 2014-06-07
 */

namespace Application\Service;

use My\OtherInterface as MyInterface;
use Application\Locator\BaseLocator as BaseLocator;

/**
 * Class FromArrayConfigurationFileLocator
 *
 * @package Application\Service
 */
class FromArrayConfigurationFileLocator extends BaseLocator implements \My\Full\QualifiedInterface, MyInterface
{
    /**
     * @var $factoryInstancePool
     */
    private $factoryInstancePool = array();

    /**
     * @var $sharedInstancePool
     */
    private $sharedInstancePool = array();

    /**
     * @return \Application\Model\ExampleUniqueInvokableInstance
     */
    public function getExampleUniqueInvokableInstance()
    {
        return new \Application\Model\ExampleUniqueInvokableInstance();
    }

    /**
     * @return \Application\Factory\ExampleUniqueFactorizedInstanceFactory
     */
    public function getExampleUniqueFactorizedInstance()
    {
        return $this->fetchFromFactoryInstancePool('\Application\Factory\ExampleUniqueFactorizedInstanceFactory')->create();
    }

    /**
     * @return \Application\Model\ExampleSharedInvokableInstance
     */
    public function getExampleSharedInvokableInstance()
    {
        return $this->fetchFromSharedInstancePool('\Application\Model\ExampleSharedInvokableInstance');
    }

    /**
     * @return \Application\Factory\ExampleSharedFactorizedInstanceFactory
     */
    public function getExampleSharedFactorizedInstance()
    {
        $className = '\Application\Factory\ExampleSharedFactorizedInstanceFactory';

        if ($this->isNotInSharedInstancePool($className)) {
            $factoryClassName = '\Application\Factory\ExampleSharedFactorizedInstanceFactory';
            $factory = $this->fetchFromFactoryInstancePool($factoryClassName);

            $this->addToSharedInstancePool($className, $factory->create());
        }

        return $this->fetchFromSharedInstancePool($className);
    }
    //... code for internal methods
}
```

The Locator is taking care of the instance pooling.

# Behaviour

* creates a FactoryInterface file
* creates a InvalidArgumentException if a namespace is given

# Terms

* Assembler
    * implements the [AssemblerInterface](https://github.com/bazzline/php_component_locator_generator/blob/master/source/Net/Bazzline/Component/Locator/Configuration/Assembler/AssemblerInterface.php)
    * implements the way the [Configuration](https://github.com/bazzline/php_component_locator_generator/blob/master/source/Net/Bazzline/Component/Locator/Configuration.php) is filled with data
* MethodBodyBuilder
    * implements the [MethodBodyBuilderInterface](https://github.com/bazzline/php_component_locator_generator/blob/master/source/Net/Bazzline/Component/Locator/MethodBodyBuilder/MethodBodyBuilderInterface.php)
    * provides a way to extend a instance creation method body
    * provides a way to extend the method documentation
* FileExistsStrategy
    * implements the [FileExistsStrategyInterface](https://github.com/bazzline/php_component_locator_generator/blob/master/source/Net/Bazzline/Component/Locator/FileExistsStrategy/FileExistsStrategyInterface.php)
    * provides a way to deal with the fact a generated file exists already

# Benefits

* on way of calling the locator generator "php bin/generateLocator.php <path to configuration file>"
* assembler, method builder and file exists strategy are configuration based runtime variables
* highly configurable
    * each configuration file needs to be a simple php array
    * mandatory array keys are
        * assembler
        * file_exists_strategy
    * optional array key is
        * boostrap_file
    * rest of configuration is based on the given assembler
* shipped with two [assembler](https://github.com/bazzline/php_component_locator_generator/blob/master/source/Net/Bazzline/Component/Locator/Configuration/Assembler) implementations
    * FromArrayAssembler
        * mandatory array keys
            * class_name <string>
            * file_path <string>
        * optional array keys
            * extends <array> (can be empty)
            * implements <array> (can be empty)
            * instances <array> (can be empty)
                * alias <string>
                * is_factory <boolean>
                * is_shared <boolean>
                * method_body_builder <string>
            * method_prefix <string>
            * namespace <string> (can be empty)
            * uses <array> (can be empty)
                * alias <string>
    * FromPropelSchemaXmlAssembler
        * mandatory array keys
            * class_name <string>
            * file_path <string>
        * optional array keys
            * extends <array> (can be empty)
            * implements <array> (can be empty)
            * method_prefix
            * namespace <string> (can be empty)
            * path_to_schema_xml <string>
            * uses <array> (can be empty)
    * implement the [AssemblerInterface](https://github.com/bazzline/php_component_locator_generator/blob/master/source/Net/Bazzline/Component/Locator/Configuration/Assembler/AssemblerInterface.php) to write your own assembler
* shipped with two file exists strategies
    * DeleteStrategy
    * SuffixWithCurrentTimestampStrategy
    * implement the [FileExistsStrategyInterface](https://github.com/bazzline/php_component_locator_generator/blob/master/source/Net/Bazzline/Component/Locator/FileExistsStrategy/FileExistsStrategyInterface.php) to write your own strategy
* shipped with five [method body builder](https://github.com/bazzline/php_component_locator_generator/blob/master/source/Net/Bazzline/Component/Locator/MethodBodyBuilder) implementations
    * FetchFromFactoryInstancePoolBuilder used internally by the generated locator
    * FetchFromSharedInstancePoolBuilder used internally by the generated locator
    * FetchFromSharedInstancePoolOrCreateByFactoryBuilder used internally by the generated locator
    * NewInstanceBuilder used internally by the generated locator
    * PropelQueryCreateBuilder as an example to use your own method body builder
    * [ValidatedInstanceCreationBuilder](https://github.com/bazzline/php_component_locator_generator/blob/master/example/ArrayConfiguration/ValidatedInstanceCreationBuilder.php) as an additional example how to use the power of the method body builder support to generate own instance creation code
    * implement the [MethodBodyBuilderInterface](https://github.com/bazzline/php_component_locator_generator/blob/master/source/Net/Bazzline/Component/Locator/MethodBodyBuilder/MethodBodyBuilderInterface.php) to write your own method body builder
* uses separate [component](https://github.com/stevleibelt/php_component_code_generator) for php code generation

# API

Thanks to [apigen](https://github.com/apigen/apigen), the api is available in the [document](https://github.com/bazzline/php_component_locator_generator/blob/master/document/index.html) section.

# Future Improvements

* create zf2 module to easy up usage
* add "verify" method to configuration that throws an error if not all mandatory parameters are set
* implement validation of used interface- or class names by adding "autoloader class path"
* implement a flag to create a LocatorInterface out of the written Locator
* implement usage of symfony/console for "generatorLocator.php" file

# History

* [1.0.2](https://github.com/bazzline/php_component_locator_generator/tree/1.0.2) - not yet released
* [1.0.1](https://github.com/bazzline/php_component_locator_generator/tree/1.0.1) - released at 03.09.2014
    * added api
    * fixed broken links
    * adapted composer.json project name
    * moved command logic into simple Command class
    * added check in "generateLocator.php" to validate if installed as composer component or not
* [1.0.0](https://github.com/bazzline/php_component_locator_generator/tree/1.0.0) - released at 31.08.2014
    * initial project start
    * unit tests
    * examples
    * codebase itself
    * api description
