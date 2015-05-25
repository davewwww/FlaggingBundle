<?php

namespace Dwo\FlaggingBundle\Tests\Fixtures;

use Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\DoctrineCacheExtension;
use Dwo\FlaggingBundle\DependencyInjection\DwoFlaggingExtension;
use Dwo\TaggedServices\DependencyInjection\Compiler\TaggedServicesPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class Container
{
    public static function createContainer(array $data = array())
    {
        $containerBuilder = new ContainerBuilder(
            new ParameterBag(
                array_merge(
                    array(
                        'kernel.bundles'     => array(
                            'DwoFlaggingBundle' => 'Dwo\\FlaggingBundle\\DwoFlaggingBundle',
                        ),
                        'kernel.cache_dir'   => __DIR__,
                        'kernel.debug'       => false,
                        'kernel.environment' => 'test',
                        'kernel.name'        => 'kernel',
                        'kernel.root_dir'    => __DIR__,
                    ),
                    $data
                )
            )
        );

        return $containerBuilder;
    }

    /**
     * @param array $configs
     * @param array $data
     *
     * @return ContainerBuilder
     */
    public static function createContainerFromFixtures($configs = array('config.yml'), $data = array())
    {
        $container = self::createContainer($data);
        $container->registerExtension(new DwoFlaggingExtension());
        $container->registerExtension(new DoctrineCacheExtension());

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        foreach ($configs as $config) {
            $loader->load($config);
        }

        $container->addCompilerPass(new MergeExtensionConfigurationPass());
        $container->addCompilerPass(new TaggedServicesPass());
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}
