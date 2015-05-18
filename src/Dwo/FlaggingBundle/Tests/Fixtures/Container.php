<?php

namespace Dwo\FlaggingBundle\Tests\Fixtures;

use Dwo\TaggedServices\DependencyInjection\Compiler\TaggedServicesPass;
use Dwo\FlaggingBundle\DependencyInjection\DwoFlaggingExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class Container
{
    public static function createContainer(array $data = array())
    {
        return new ContainerBuilder(
            new ParameterBag(
                array_merge(
                    array(
                        'kernel.bundles'     => array('DwoFlaggingBundle' => 'Dwo\\FlaggingBundle\\DwoFlaggingBundle'),
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

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        foreach ($configs as $config) {
            $loader->load($config);
        }

        $container->addCompilerPass(new TaggedServicesPass());
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }

}
