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
use Qimnet\TableBundle\Table\Action;

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
        );
    }
    public function crudPathFunction($entity, $action=Action::INDEX, array $parameters=array())
    {
        $repository = $this->container->get('qimnet.crud.configuration.repository');
        $configuration = is_string($entity)
                ? $repository->get($entity)
                : $repository->getForEntity($entity);

        return $configuration->getPathGenerator()->generate($action, $parameters, $entity);
    }
}
