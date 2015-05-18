<?php

namespace Dwo\FlaggingBundle\Tests;

use Dwo\FlaggingBundle\DwoFlaggingBundle;

class DwoFlaggingBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildBundle()
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('addCompilerPass')
            ->with(self::isInstanceOf('Dwo\TaggedServices\DependencyInjection\Compiler\TaggedServicesPass'));

        $bundle = new DwoFlaggingBundle();
        $bundle->build($container);
    }
}