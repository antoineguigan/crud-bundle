<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Creates the CRUD configuration services
 */
class CRUDConfigurationCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('qimnet.crud.configuration.repository')) {
            return;
        }
        $repository = $container->getDefinition('qimnet.crud.configuration.repository');
        $defaults = $container->getParameter('qimnet.crud.defaults');
        foreach ($container->getParameter('qimnet.crud.services') as $name=>$configuration) {
            $options = $configuration['options'] + array('name'=>$name) + $defaults['options'];
            $configuration = $configuration + $defaults;
            $serviceId = 'qimnet.crud.configuration.' . strtolower($name);
            $definition = new DefinitionDecorator('qimnet.crud.configuration');
            foreach (array('object_manager_factory', 'security_context_factory', 'path_generator_factory') as $index=>$key) {
                if ($configuration[$key]) {
                    $definition->replaceArgument($index, new Reference($configuration[$key]));
                }
            }
            $definition->addArgument($options);

            if ($configuration['class']) {
                $definition->setClass($configuration['class']);
            }
            $definition->addTag('qimnet.crud.configuration', array(
                'alias'=>$name,
                'object_class'=>$options['object_class'],
                'worker'=>$configuration['worker'],
                'redirection_manager'=>$configuration['redirection_manager']
            ));
            $container->setDefinition($serviceId, $definition);
        }
        $taggedServices = $container->findTaggedServiceIds('qimnet.crud.configuration');
        foreach ($taggedServices as $id=>$attributes) {
            $repository->addMethodCall('add', array(
                $attributes[0]['alias'],
                $attributes[0]['object_class'],
                $id,
                $attributes[0]['worker'],
                $attributes[0]['redirection_manager']));
        }
    }
}
