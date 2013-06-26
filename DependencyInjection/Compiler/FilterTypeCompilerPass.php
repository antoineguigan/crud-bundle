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

/**
 * Adds filter types to the registry
 */
class FilterTypeCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('qimnet.filter.type.registry')) {
            return;
        }
        $definition = $container->getDefinition('qimnet.filter.type.registry');
        $taggedServices = $container->findTaggedServiceIds('qimnet.filter.type');
        foreach ($taggedServices as $id=>$attributes) {
            $definition->addMethodCall('add', array($attributes[0]['alias'], $id));
        }
    }
}
