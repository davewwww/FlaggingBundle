<?php

namespace Dwo\FlaggingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author David Wolter <david@lovoo.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dwo_flagging');

        $rootNode
            ->children()

                ->arrayNode('manager')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('feature')->defaultValue('dwo_flagging.manager.feature.config')->end()
                        ->scalarNode('voter')->defaultValue('dwo_flagging.manager.voter.config')->end()
                    ->end()
                ->end()

                /**
                 *  Features
                 */
                ->arrayNode('features')
                    ->performNoDeepMerging()
                    ->prototype('array')
                        ->children()

                            /**
                             *  Breaker for Feature
                             */
                            ->arrayNode('breaker')
                                ->prototype('array')
                                    ->prototype('array')
                                        ->prototype('variable')->end()
                                    ->end()
                                ->end()
                            ->end()

                            /**
                             *  Filter for Feature
                             */
                            ->arrayNode('filters')
                                ->prototype('array')
                                    ->prototype('array')
                                        ->prototype('variable')->end()
                                    ->end()
                                ->end()
                            ->end()



                            /**
                             *  Values for Feature
                             */
                            ->arrayNode('values')
                                ->prototype('array')
                                    ->children()

                                        /**
                                         * Value
                                         */
                                        ->variableNode('value')->end()

                                        /**
                                         *  Filter for Value
                                         */
                                        ->arrayNode('filters')
                                            ->prototype('array')
                                                ->prototype('array')
                                                    ->prototype('variable')->end()
                                                ->end()
                                            ->end()
                                        ->end()

                                    ->end()
                                ->end()
                            ->end()

                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}