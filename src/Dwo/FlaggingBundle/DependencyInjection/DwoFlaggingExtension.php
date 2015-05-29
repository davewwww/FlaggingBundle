<?php

namespace Dwo\FlaggingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Dave Www <davewwwo@gmail.com>
 */
class DwoFlaggingExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('decider.yml');
        $loader->load('manager.yml');
        $loader->load('voter.yml');
        $loader->load('validator.yml');
        $loader->load('handler.yml');
        $loader->load('flagging_voter.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setAlias('dwo_flagging.manager.feature', $config['manager']['feature']);
        $container->setAlias('dwo_flagging.manager.voter', $config['manager']['voter']);

        $container->setParameter('dwo_flagging.features', $config['features']);
    }
}
