<?php

namespace Dwo\FlaggingBundle\Tests\DependencyInjection;

use Dwo\FlaggingBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    protected static $config;

    /**
     * @beforeClass
     */
    public static function init()
    {
        $yml = Yaml::parse(file_get_contents(__DIR__.'/../Fixtures/config.yml'));

        $processor = new Processor();
        self::$config = $processor->processConfiguration(new Configuration(), array($yml['dwo_flagging']));
    }

    public static function testEmptyConfig()
    {
        $yml = Yaml::parse(file_get_contents(__DIR__.'/../Fixtures/config_empty.yml'));

        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), array($yml['dwo_flagging']));

        self::assertArrayHasKey('features', $config);
        self::assertArrayHasKey('manager', $config);

        self::assertCount(0, $config['features']);
        self::assertCount(2, $config['manager']);
        self::assertEquals('dwo_flagging.manager.feature.config', $config['manager']['feature']);
        self::assertEquals('dwo_flagging.manager.voter.config', $config['manager']['voter']);
    }

    public function test()
    {
        self::assertArrayHasKey('features', self::$config);
        self::assertArrayHasKey('manager', self::$config);
    }

    public function testFeatureKeys()
    {
        $features = self::$config['features'];
        self::assertArrayHasKey('foo', $features);
        self::assertArrayHasKey('filters', $features['foo']);
        self::assertArrayHasKey('values', $features['foo']);
        self::assertCount(0, $features['foo']['filters']);
        self::assertCount(0, $features['foo']['values']);
    }

    public function testFilters()
    {
        $filters = self::$config['features']['foo_filters']['filters'];
        self::isType('array', $filters);
        self::assertCount(2, $filters);

        $filter = current($filters);
        self::assertArrayHasKey('countries', $filter);
        self::isType('array', $filter['countries']);
        self::assertCount(1, $filter['countries']);
        self::assertEquals('DE', current($filter['countries']));
    }

    public function testBreaker()
    {
        $feature = self::$config['features']['foo_breaker'];

        self::assertArrayHasKey('breaker', $feature);

        $breaker = $feature['breaker'];
        self::isType('array', $breaker);
        self::assertCount(1, $breaker);

        $filter = current($breaker);
        self::assertArrayHasKey('emails', $filter);
        self::isType('array', $filter['emails']);
        self::assertCount(1, $filter['emails']);
        self::assertEquals('user@domain.com', current($filter['emails']));
    }

    public function testValues()
    {
        $getValue = function ($name) {
            return current(self::$config['features'][$name]['values'])['value'];
        };

        $value = $getValue('foo_value');
        self::isType('scalar', $value);
        self::isFalse(empty($value));

        $value_array = $getValue('foo_value_array');
        self::isType('array', $value_array);
        self::isFalse(empty($value_array));

        $value_obj = $getValue('foo_value_obj');
        self::isType('array', $value_obj);
        self::isFalse(empty($value_obj));
        self::assertArrayHasKey('foo', $value_obj);
        self::assertArrayHasKey('lorem', $value_obj);
        self::assertCount(2, $value_obj['lorem']);
    }
}