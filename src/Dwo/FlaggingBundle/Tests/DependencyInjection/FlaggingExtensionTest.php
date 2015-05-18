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
}
