<?php
namespace Qimnet\CRUDBundle\HTTP;
use Qimnet\CRUDBundle\Configuration\CRUDConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;
use Qimnet\CRUDBundle\Controller\Worker\CRUDControllerWorkerInterface;
use Qimnet\CRUDBundle\Controller\RedirectionManager\CRUDRedirectionManagerInterface;

/*
 *  This file is part of QIMNET CRUD Bundle
 *  (c) Antoine Guigan <aguigan@qimnet.com>
 *  This source file is subject to the MIT license that is bundled
 *  with this source code in the file LICENSE.
 */

/**
 * Interface used to get the CRUD configuration corresponding to the current
 * request.
 *
 * @author Antoine Guigan <aguigan@qimnet.com>
 */
interface CRUDRequestInterface
{
    /**
     * Returns the CRUD configuration for the current request
     *
     * @return CRUDConfigurationInterface
     */
    public function getConfiguration();
    /**
     * Returns the HTTP Request
     *
     * @return Request
     */
    public function getRequest();
    /**
     * Returns the CRUD worker for the current request
     *
     * @return CRUDControllerWorkerInterface
     */
    public function getWorker();

    /**
     * Returns the CRUD redirection manager for the current request
     *
     * @return CRUDRedirectionManagerInterface
     */
    public function getRedirectionManager();
}
