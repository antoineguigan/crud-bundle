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

class CRUDPersistenceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('qimnet.crud.object_manager.registry')) {
            return;
        }
        $definition = $container->getDefinition('qimnet.crud.object_manager.registry');
        $taggedServices = $container->findTaggedServiceIds('qimnet.crud.object_manager.factory');
        foreach ($taggedServices as $id=>$attributes) {
            $definition->addMethodCall('addFactory', array($attributes[0]['alias'], $id));
        }
    }
}
