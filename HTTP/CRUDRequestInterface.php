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
 * @author Antoine Guigan <aguigan@qimnet.com>
 */
interface CRUDRequestInterface
{
    /**
     * @return CRUDConfigurationInterface
     */
    public function getConfiguration();
    /**
     * @return Request
     */
    public function getRequest();
    /**
     * @return CRUDControllerWorkerInterface
     */
    public function getWorker();

    /**
     * @return CRUDRedirectionManagerInterface
     */
    public function getRedirectionManager();
}
