<?php

namespace Dwo\FlaggingBundle\Tests\Functional;

use Dwo\Flagging\Context\Context;
use Dwo\Flagging\FeatureDeciderInterface;
use Dwo\Flagging\Model\FeatureManagerInterface;
use Dwo\FlaggingBundle\Tests\DependencyInjection;
use Dwo\FlaggingBundle\Tests\Fixtures\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class AbstractContainerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected static $container;

    public static function createContainer()
    {
        self::$container = Container::createContainerFromFixtures(
            array(
                'config_functional.yml',
                'services.yml',
            )
        );
    }
}