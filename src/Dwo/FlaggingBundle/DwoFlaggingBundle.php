<?php

namespace Dwo\FlaggingBundle;

use Dwo\TaggedServices\DependencyInjection\Compiler\TaggedServicesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Dave Www <davewwwo@gmail.com>
 */
class DwoFlaggingBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TaggedServicesPass());
    }
}
