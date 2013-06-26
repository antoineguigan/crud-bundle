<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\Twig;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Qimnet\CRUDBundle\Configuration\CRUDAction;
use Qimnet\CRUDBundle\Configuration\CRUDConfigurationInterface;

class Extensions extends \Twig_Extension
{
    protected $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function getName()
    {
        return 'qimnet_crud_extension';
    }
    public function getFunctions()
    {
        return array(
            'crud_path'=>new \Twig_Function_Method($this, 'crudPathFunction'),
            'editable'=>new \Twig_Function_Method($this, 'editable'),
            'deletable'=>new \Twig_Function_Method($this, 'deletable'),
            'creatable'=>new \Twig_Function_Method($this, 'creatable'),
            'listable'=>new \Twig_Function_Method($this, 'listable'),
        );
    }
    public function getTests()
    {
        return array(
            'editable'=>new \Twig_Test_Method($this, 'editable'),
            'deletable'=>new \Twig_Test_Method($this, 'deletable'),
            'creatable'=>new \Twig_Test_Method($this, 'creatable'),
            'listable'=>new \Twig_Test_Method($this, 'listable'),
        );
    }
    /**
     * @param mixed $entity
     * @return CRUDConfigurationInterface
     */
    private function getConfiguration($entity)
    {
        $repository = $this->container->get('qimnet.crud.configuration.repository');
        return is_string($entity)
                ? $repository->get($entity)
                : $repository->getForEntity($entity);
    }
    public function crudPathFunction($entity, $action=CRUDAction::INDEX, array $parameters=array(), array $objectVars=array())
    {
        return $this->getConfiguration($entity)->getPathGenerator()->generate($action, $parameters, $entity);
    }
    public function editable($object, $objectVars=array()) {
        return $this->getConfiguration($object)->getSecurityContext()->isActionAllowed(CRUDAction::UPDATE, $object, $objectVars);
    }
    public function deletable($object, $objectVars=array()) {
        return $this->getConfiguration($object)->getSecurityContext()->isActionAllowed(CRUDAction::DELETE, $object, $objectVars);
    }
    public function creatable($object) {
        return $this->getConfiguration($object)->getSecurityContext()->isActionAllowed(CRUDAction::CREATE);
    }
    public function listable($object) {
        return $this->getConfiguration($object)->getSecurityContext()->isActionAllowed(CRUDAction::INDEX);
    }
}
