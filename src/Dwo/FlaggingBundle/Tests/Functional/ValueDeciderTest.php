<?php

namespace Dwo\FlaggingBundle\Tests\Functional;

use Dwo\Flagging\Context\Context;
use Dwo\Flagging\FeatureDeciderInterface;
use Dwo\FlaggingBundle\Tests\DependencyInjection;

class ValueDeciderTest extends AbstractContainerTestCase
{
    /**
     * @var FeatureDeciderInterface
     */
    protected static $decider;

    /**
     * @beforeClass
     */
    public static function init()
    {
        self::createContainer();
        self::$decider = self::$container->get('dwo_flagging.value_decider');
    }

    /**
     * @dataProvider provider
     */
    public function testDecide($result, $featureName, $name)
    {
        $context = new Context(array('name' => $name));
        self::assertEquals($result, self::$decider->decide($featureName, $context), 'error in '.$featureName);
    }

    public function provider()
    {
        return array(
            array(null, 'foo', ''),

            array(5, 'feature_value', ''),
            array(5, 'feature_value', 'foo'),

            array(5, 'feature_values', ''),
            array(1, 'feature_values', 'foo'),

            array(4, 'feature_filters_and_values', 'foo'),
            array(5, 'feature_filters_and_values', 'bar'),
            array(null, 'feature_filters_and_values', 'foobar'),
        );
    }
}