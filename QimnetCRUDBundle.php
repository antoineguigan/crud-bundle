<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Qimnet\CRUDBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class QimnetCRUDBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DependencyInjection\Compiler\CRUDPersistenceCompilerPass);
        $container->addCompilerPass(new DependencyInjection\Compiler\CRUDConfigurationCompilerPass());
        $container->addCompilerPass(new DependencyInjection\Compiler\FilterTypeCompilerPass());
    }
}
