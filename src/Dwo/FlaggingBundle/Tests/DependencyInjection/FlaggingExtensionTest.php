<?php

namespace Dwo\FlaggingBundle\Tests\DependencyInjection;

use Dwo\FlaggingBundle\Tests\Fixtures\Container;

class FlaggingExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $container = Container::createContainerFromFixtures();

        $this->assertTrue($container->hasAlias('dwo_flagging.manager.feature'));
        $this->assertTrue($container->hasAlias('dwo_flagging.manager.voter'));

        $this->assertInternalType('array', $features = $container->getParameter('dwo_flagging.features'));
        $this->assertArrayHasKey('foo', $features);
        $this->assertArrayHasKey('foo_filter', $features);
    }

    public function testFeatureMerged()
    {
        $container = Container::createContainerFromFixtures(
            array(
                'config_merge1.yml',
                'config_merge2.yml'
            )
        );

        $features = $container->getParameter('dwo_flagging.features');

        $this->assertArrayHasKey('merged_filters', $features);
        $this->assertEquals([['emails' => ['foo@domain.com']]], $features['merged_filters']['filters']);

        $this->assertArrayHasKey('merged_breaker', $features);
        $this->assertEquals([['emails' => ['foo@domain.com']]], $features['merged_breaker']['breaker']);

        $this->assertArrayHasKey('merged_value', $features);
        $this->assertEquals([['value' => ['foo'], 'filters' => []]], $features['merged_value']['values']);
    }
}
