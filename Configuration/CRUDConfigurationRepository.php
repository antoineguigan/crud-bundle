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

/**
 * Repository containing all CRUD configurations
 */
class CRUDConfigurationRepository implements CRUDConfigurationRepositoryInterface
{
    private $services=array();
    private $workers=array();
    private $redirectionManagers=array();
    private $classes=array();
    private $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function add($name, $class, $serviceId , $workerServiceId, $redirectionManagerServiceId)
    {
        $this->services[$name] = $serviceId;
        $this->classes[$class] = $serviceId;
        $this->workers[$name] = $workerServiceId;
        $this->redirectionManagers[$name] = $redirectionManagerServiceId;
    }

    /**
     * @inheritdoc
     */
    public function has($name)
    {
        return isset($this->services[$name]);
    }

    /**
     * @inheritdoc
     */
    public function hasClass($class)
    {
        return isset($this->classes[$class]);
    }
    /**
     * @inheritdoc
     */
    public function hasForEntity($entity)
    {
        return $this->hasClass(get_class($entity));
    }

    /**
     * @inheritdoc
     */
    public function get($name)
    {
        return $this->container->get($this->services[$name]);
    }

    /**
     * @inheritdoc
     */
    public function getForClass($class)
    {
        return $this->container->get($this->classes[$class]);
    }

    /**
     * @inheritdoc
     */
    public function getForEntity($entity)
    {
        return $this->getForClass(get_class($entity));
    }

    /**
     * @inheritdoc
     */
    public function getRedirectionManager($name)
    {
        return $this->container->get($this->redirectionManagers[$name]);
    }

    /**
     * @inherit-doc
     */
    public function getWorker($name)
    {
        return $this->container->get($this->workers[$name]);
    }
}
