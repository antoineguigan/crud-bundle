<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Qimnet\CRUDBundle\Routing;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

class CRUDPathGeneratorFactory implements CRUDPathGeneratorFactoryInterface
{
    protected $router;
    protected $propertyAccessor;
    protected $defaultClass;

    public function __construct(RouterInterface $router, PropertyAccessorInterface $propertyAccessor, $defaultClass)
    {
        $this->router = $router;
        $this->propertyAccessor = $propertyAccessor;
        $this->defaultClass = $defaultClass;
    }

    public function create($routePrefix, $configName, $idField='id', $class='')
    {
        if (!$class) {
            $class = $this->defaultClass;
        }

        return new $class(
            $this->router,
            $this->propertyAccessor,
            $routePrefix,
            $configName,
            $idField);
    }
}
