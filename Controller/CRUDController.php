<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\Controller;

use Qimnet\CRUDBundle\HTTP\CRUDRequestInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CRUDController
{
    /**
     *
     * @var CRUDRequestInterface
     */
    protected $CRUDRequest;

    public function setCRUDRequest(CRUDRequestInterface $request=null)
    {
        $this->CRUDRequest = $request;
    }

    protected function getWorker()
    {
        $worker = $this->CRUDRequest->getWorker();
        if (!$worker) {
            throw new NotFoundHttpException;
        }

        return $worker;
    }

    public function indexAction($page=1, $sortField='id', $sortDirection='desc')
    {
        return $this->getWorker()->indexAction($page, $sortField, $sortDirection);
    }
    public function newAction()
    {
        return $this->getWorker()->newAction();
    }

    public function batchDeleteAction()
    {
        return $this->getWorker()->batchDeleteAction();
    }

    public function deleteAction($id)
    {
        return $this->getWorker()->deleteAction($id);
    }

    public function filterAction()
    {
        return $this->getWorker()->filterAction();
    }
    public function editAction($id)
    {
        return $this->getWorker()->editAction($id);
    }
    public function formAction($entity=null)
    {
        return $this->getWorker()->formAction($entity);
    }
    public function showAction($id)
    {
        return $this->getWorker()->showAction($id);
    }
}
