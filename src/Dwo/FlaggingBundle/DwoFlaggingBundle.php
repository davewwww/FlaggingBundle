<?php

namespace Dwo\FlaggingBundle;

use Dwo\TaggedServices\DependencyInjection\Compiler\TaggedServicesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author David Wolter <david@lovoo.com>
 */
class DwoFlaggingBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TaggedServicesPass());
    }
}
