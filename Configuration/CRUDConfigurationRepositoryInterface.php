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

use Qimnet\CRUDBundle\Controller\Worker\CRUDControllerWorkerInterface;
use Qimnet\CRUDBundle\Controller\RedirectionManager\CRUDRedirectionManagerInterface;

interface CRUDConfigurationRepositoryInterface
{
    /**
     * Adds a configuration for a given object class
     *
     * @param string $name                        the name of the configuration
     * @param string $class                       the class of objects concerned by the configuration
     * @param string $serviceId                   the id of the configuration service
     * @param string $workerServiceId             the id of the worker service
     * @param string $redirectionManagerServiceId the id of the redirection manager service
     */
    public function add($serviceId, $class, $name, $workerServiceId, $redirectionManagerServiceId);

    /**
     * Returns true if the repository contains a configuration for the given name
     *
     * @param  string  $name
     * @return boolean
     */
    public function has($name);

    /**
     * Returns true if the repository contains a configuration for the given object class
     *
     * @param  type $class
     * @return type
     */
    public function hasClass($class);

    /**
     * Returns true if the repository contains a configuration for the class of the given object
     *
     * @param  type $entity
     * @return type
     */
    public function hasForEntity($entity);

    /**
     * Returns the configuration for a given name
     *
     * @param  string                     $name
     * @return CRUDConfigurationInterface
     */
    public function get($name);

    /**
     * Returns the worker instance for a given name
     *
     * @param  string                        $name
     * @return CRUDControllerWorkerInterface
     */
    public function getWorker($name);

    /**
     * Returns the redirection manager for a given name
     *
     * @param  string                          $name
     * @return CRUDRedirectionManagerInterface
     */
    public function getRedirectionManager($name);

    /**
     * Returns the configuration for a given class
     *
     * @param  string $class
     * @return type
     */
    public function getForClass($class);

    /**
     * Returns the configuration for the class of a given object
     *
     * @param  object                     $entity
     * @return CRUDConfigurationInterface
     */
    public function getForEntity($entity);
}
