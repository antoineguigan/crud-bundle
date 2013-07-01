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
                ->scalarNode('path_prefix')
                    ->info('The path prefix for all CRUD requests')
                    ->defaultValue('/backend')
                ->end()
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')
                            ->info('The default CRUDConfigurationInterface implementation.')
                            ->defaultNull()
                        ->end()
                        ->arrayNode('options')
                            ->ignoreExtraKeys()
                            ->info('The default options for all configurations')
                        ->end()
                        ->scalarNode('worker')
                            ->defaultValue('qimnet.crud.worker')
                            ->info('The default worker service id')
                        ->end()
                        ->scalarNode('redirection_manager')
                            ->defaultValue('qimnet.crud.redirection_manager')
                            ->info('The default redirection manager service id')
                        ->end()
                        ->scalarNode('object_manager_factory')
                            ->defaultNull()
                            ->info('The default object manager factory service id')
                        ->end()
                        ->scalarNode('security_context_factory')
                            ->defaultNull()
                            ->info('The default security context factory service id')
                        ->end()
                        ->scalarNode('path_generator_factory')
                            ->defaultNull()
                            ->info('The default path generator factory context factory service id')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('services')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')
                                ->info('The name of the configuration. Must contain only lowercase alphanumerics and underscores.')
                            ->end()
                            ->scalarNode('class')
                                ->info('The class name for the configuration service')
                            ->end()
                            ->arrayNode('options')
                                ->ignoreExtraKeys()
                                    ->children()
                                        ->scalarNode('object_class')
                                            ->isRequired()
                                            ->info('The class of the managed objects')
                                        ->end()
                                    ->end()
                                ->info('The options used to create the configuration service')
                            ->end()
                            ->scalarNode('worker')
                                ->info('The id of the worker service')
                            ->end()
                            ->scalarNode('redirection_manager')
                                ->info('The id of the redirection manager service')
                            ->end()
                            ->scalarNode('object_manager_factory')
                                ->info('The id of the object manager factory service')
                            ->end()
                            ->scalarNode('security_context_factory')
                                ->info('The id of the security context factory service')
                            ->end()
                            ->scalarNode('path_generator_factory')
                                ->info('The id of the path generator factory')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
