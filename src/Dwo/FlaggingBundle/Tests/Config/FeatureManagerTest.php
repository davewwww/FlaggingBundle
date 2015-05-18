<?php

namespace Dwo\FlaggingBundle\Tests\Config;

use Dwo\Flagging\Model\Feature;
use Dwo\Flagging\Model\FilterCollectionInterface;
use Dwo\Flagging\Model\FilterInterface;
use Dwo\FlaggingBundle\Config\FeatureManager;

class FeatureManagerTest extends \PHPUnit_Framework_TestCase
{
    protected static $config;

    /**
     * @beforeClass
     */
    public static function init()
    {
        self::$config = array(
            'foo' => array(
                'filters' => array(
                    array('emails' => ['user@domain.com'])
                ),
            )
        );
    }

    public function testFindFeatureByName()
    {
        $manager = new FeatureManager(self::$config);
        $feature = $manager->findFeatureByName('foo');

        self::assertInstanceOf('Dwo\Flagging\Model\FeatureInterface', $feature);
        self::assertEquals('foo', $feature->getName());
    }

    public function testFindFeatureByNameError()
    {
        $manager = new FeatureManager(self::$config);
        $feature = $manager->findFeatureByName('bar');

        self::assertNull($feature);
    }

    public function testFindAll()
    {
        $manager = new FeatureManager(self::$config);
        $features = $manager->findAllFeatures();
        self::assertCount(1, $features);

        self::assertArrayHasKey('foo', $features);
        self::assertInstanceOf('Dwo\Flagging\Model\FeatureInterface', $features['foo']);

        $feature = $features['foo'];
        self::assertEquals('foo', $feature->getName());
    }

    public function testSaveFeature()
    {
        $feature = new Feature('bar');

        $manager = new FeatureManager(self::$config);
        $manager->saveFeature($feature);

        self::assertEquals($feature, $manager->findFeatureByName('bar'));
    }
}
