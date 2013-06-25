<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\Configuration;

use Symfony\Component\DependencyInjection\ContainerInterface;

class CRUDConfigurationRepository implements CRUDConfigurationRepositoryInterface
{
    protected $services=array();
    protected $workers=array();
    protected $redirectionManagers=array();
    protected $classes=array();
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function add($name, $class, $serviceId , $workerServiceId, $redirectionManagerServiceId)
    {
        $this->services[$name] = $serviceId;
        $this->classes[$class] = $serviceId;
        $this->workers[$name] = $workerServiceId;
        $this->redirectionManagers[$name] = $redirectionManagerServiceId;
    }

    public function has($name)
    {
        return isset($this->services[$name]);
    }

    public function hasClass($class)
    {
        return isset($this->classes[$class]);
    }
    public function hasForEntity($entity)
    {
        return $this->hasClass(get_class($entity));
    }

    /**
     *
     * @param  string                     $name
     * @return CRUDConfigurationInterface
     */
    public function get($name)
    {
        return $this->container->get($this->services[$name]);
    }

    public function getForClass($class)
    {
        return $this->container->get($this->classes[$class]);
    }

    public function getForEntity($entity)
    {
        return $this->getForClass(get_class($entity));
    }

    public function getRedirectionManager($name)
    {
        return $this->container->get($this->redirectionManagers[$name]);
    }

    public function getWorker($name)
    {
        return $this->container->get($this->workers[$name]);
    }
}
