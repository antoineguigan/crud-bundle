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
    public function add($serviceId, $class, $name, $workerServiceId, $redirectionManagerServiceId);
    public function has($name);
    public function hasClass($class);
    public function hasForEntity($entity);
    /**
     *
     * @param  string                     $name
     * @return CRUDConfigurationInterface
     */
    public function get($name);

    /**
     * @param  string                        $name
     * @return CRUDControllerWorkerInterface
     */
    public function getWorker($name);

    /**
     * @param  string                          $name
     * @return CRUDRedirectionManagerInterface
     */
    public function getRedirectionManager($name);

    /**
     *
     * @param  string                     $class
     * @return CRUDConfigurationInterface
     */
    public function getForClass($class);
    /**
     *
     * @param  object                     $entity
     * @return CRUDConfigurationInterface
     */
    public function getForEntity($entity);
}
