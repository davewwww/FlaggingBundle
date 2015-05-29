<?php

namespace Dwo\FlaggingBundle\DependencyInjection\Definition;

use Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\Definition\CacheDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class DbalDefinition
 *
 * @author Dave Www <davewwwo@gmail.com>
 */
class DbalDefinition extends CacheDefinition
{
    /**
     * {@inheritDoc}
     */
    public function configure($name, array $config, Definition $service, ContainerBuilder $container)
    {
        $config = $config['custom_provider'];
        if(isset($config['custom_provider']['type']) && 'dbal' === $config['custom_provider']['type']) {
            $config = $config['custom_provider']['options'];
            $service->setArguments(array($config['connection'], $config['table']));
        }
    }
}
