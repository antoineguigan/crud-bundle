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
use Qimnet\CRUDBundle\Configuration\CRUDAction;

class CRUDPathGenerator implements CRUDPathGeneratorInterface
{
    protected $router;
    protected $propertyAccessor;
    protected $routePrefix;
    protected $configName;
    protected $idField;
    protected $path_suffixes = array(
        CRUDAction::CREATE=>'new',
        CRUDAction::DELETE=>'delete',
        CRUDAction::INDEX=>'index',
        CRUDAction::SHOW=>'show',
        CRUDAction::UPDATE=>'edit',
        CRUDAction::FILTER=>'filter',
        CRUDAction::BATCH=>'batch'
    );

    public function __construct(RouterInterface $router,
            PropertyAccessorInterface $propertyAccessor,
            $routePrefix,
            $configName,
            $idField='id')
    {
        $this->router = $router;
        $this->propertyAccessor = $propertyAccessor;
        $this->routePrefix = $routePrefix;
        $this->configName = $configName;
        $this->idField = $idField;
    }
    public function generate($action, array $parameters=array(), $object = null, array $objectVars = array())
    {
        $parameters['configName'] = $this->configName;
        switch ($action) {
            case CRUDAction::SHOW :
            case CRUDAction::UPDATE :
            case CRUDAction::DELETE :
                $parameters['id'] = $this->propertyAccessor->getValue($object, $this->idField);
        }

        return $this->router->generate(
                $this->routePrefix . '_' . $this->path_suffixes[$action],
                $parameters);
    }
}
