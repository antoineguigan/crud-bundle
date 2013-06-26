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
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller used by all CRUD requests. Acts as a proxy for a
 * CRUDControllerWorkerInterface instance.
 */
class CRUDController
{
    /**
     * @var CRUDRequestInterface
     */
    protected $CRUDRequest;

    /**
     * Sets the current CRUDRequestInterface instance
     * @param \Qimnet\CRUDBundle\HTTP\CRUDRequestInterface $request
     */
    public function setCRUDRequest(CRUDRequestInterface $request=null)
    {
        $this->CRUDRequest = $request;
    }

    /**
     * Returns the CRUDControllerWorkerInterface instance for the current request
     * @return Worker\CRUDControllerWorkerInterface
     * @throws NotFoundHttpException
     */
    protected function getWorker()
    {
        $worker = $this->CRUDRequest->getWorker();
        if (!$worker) {
            throw new NotFoundHttpException;
        }

        return $worker;
    }

    /**
     * Displays a list of object
     *
     * @param  int      $page          the page number, starting with 1
     * @param  string   $sortField     the name of the column used to sort
     * @param  string   $sortDirection the sort direction (asc|desc)
     * @return Response
     */
    public function indexAction($page=1, $sortField='id', $sortDirection='desc')
    {
        return $this->getWorker()->indexAction($page, $sortField, $sortDirection);
    }

    /**
     * Displays and manages a form for object creation
     *
     * @return Response
     */
    public function newAction()
    {
        return $this->getWorker()->newAction();
    }

    /**
     * Displays and manages a form for object edition
     *
     * @param  string   $id the id of the object
     * @return Response
     */
    public function editAction($id)
    {
        return $this->getWorker()->editAction($id);
    }

    /**
     * Displays an embeddable form for a given entity
     *
     * @param  type     $entity
     * @return Response
     */
    public function formAction($entity=null)
    {
        return $this->getWorker()->formAction($entity);
    }

    /**
     * Shows an object
     *
     * @param  string $id the id of the object
     * @return type
     */
    public function showAction($id)
    {
        return $this->getWorker()->showAction($id);
    }
    /**
     * Applies an action to multiple objects
     *
     * @return Response
     * @throws \RuntimeException
     */
    public function batchAction($action)
    {
        $worker = $this->getWorker();
        $method = sprintf('batch%sAction',  ucfirst($action));
        if (!method_exists($worker, $method)) {
            throw new \RuntimeException(sprintf('Please implement method "%s" in your worker class.', $method));
        }

        return $this->getWorker()->$method();
    }

    /**
     * Deletes a single object
     *
     * @param  string   $id
     * @return Response
     */
    public function deleteAction($id)
    {
        return $this->getWorker()->deleteAction($id);
    }

    /**
     * Stores the filter form data in the session
     *
     * @return Response
     */
    public function filterAction()
    {
        return $this->getWorker()->filterAction();
    }

}
