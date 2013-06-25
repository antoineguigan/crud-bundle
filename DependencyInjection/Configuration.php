<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Qimnet\CRUDBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('qimnet_crud');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('path_prefix')->defaultValue('/backend')->end()
                ->arrayNode('services')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('class')
                            ->end()
                            ->arrayNode('options')
                                ->prototype('variable')
                                ->end()
                            ->end()
                            ->scalarNode('worker')
                            ->end()
                            ->scalarNode('redirection_manager')
                            ->end()
                            ->scalarNode('object_manager_factory')
                            ->end()
                            ->scalarNode('security_context_factory')
                            ->end()
                            ->scalarNode('path_generator_factory')
                            ->end()
                            ->arrayNode('options')
                                ->prototype('variable')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')
                            ->defaultNull()
                        ->end()
                        ->arrayNode('options')
                            ->prototype('variable')
                            ->end()
                        ->end()
                        ->scalarNode('worker')
                            ->defaultValue('qimnet.crud.worker')
                        ->end()
                        ->scalarNode('redirection_manager')
                            ->defaultValue('qimnet.crud.redirection_manager')
                        ->end()
                        ->scalarNode('object_manager_factory')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('security_context_factory')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('path_generator_factory')
                            ->defaultNull()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
