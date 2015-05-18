<?php

namespace Dwo\FlaggingBundle\Tests\Functional;

use Dwo\Flagging\Context\Context;
use Dwo\Flagging\FeatureDeciderInterface;

class FeatureDeciderTest extends AbstractContainerTestCase
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
        self::$decider = self::$container->get('dwo_flagging.feature_decider');
    }

    /**
     * @dataProvider provider
     */
    public function testDecide($result, $featureName, $name)
    {
        self::assertEquals($result, self::$decider->decide($featureName, new Context(array('name' => $name))));
    }

    public function provider()
    {
        return array(
            array(true, 'feature', ''),
            array(true, 'feature', 'foo'),

            array(true, 'feature_foo', 'foo'),
            array(false, 'feature_foo', 'bar'),

            array(false, 'feature_not_foo_or_bar', 'foo'),
            array(false, 'feature_not_foo_or_bar', 'bar'),
            array(false, 'feature_not_foo_or_bar', 'foobar'),
            array(true, 'feature_not_foo_or_bar', 'lorem'),
        );
    }
}