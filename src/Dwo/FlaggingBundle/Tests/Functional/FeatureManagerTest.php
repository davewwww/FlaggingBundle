<?php

namespace Dwo\FlaggingBundle\Tests\Functional;

use Dwo\Flagging\Model\FeatureManagerInterface;
use Dwo\FlaggingBundle\Tests\DependencyInjection;

class FeatureManagerTest extends AbstractContainerTestCase
{
    /**
     * @var FeatureManagerInterface
     */
    protected static $manager;

    /**
     * @beforeClass
     */
    public static function init()
    {
        self::createContainer();
        self::$manager = self::$container->get('dwo_flagging.manager.feature');
    }

    public function testFindFeature()
    {
        $feature = self::$manager->findFeatureByName('feature_foo');
        self::assertInstanceOf('Dwo\Flagging\Model\FeatureInterface', $feature);
    }

    public function testFindAllFeatures()
    {
        $features = self::$manager->findAllFeatures();

        self::assertGreaterThan(1, count($features));
        self::assertArrayHasKey('feature_foo', $features);
        self::assertInstanceOf('Dwo\Flagging\Model\FeatureInterface', $features['feature_foo']);
    }
}