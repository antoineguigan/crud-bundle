<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\Controller\Worker;

interface CRUDControllerWorkerInterface
{
    /**
     * Displays a list of object
     *
     * @param int $page the page number, starting with 1
     * @param string $sortField the name of the column used to sort
     * @param string $sortDirection the sort direction (asc|desc)
     * @return Response
     */
    public function indexAction($page=1, $sortField='id', $sortDirection='desc');

    /**
     * Displays and manages a form for object creation
     *
     * @return Response
     */
    public function newAction();

    /**
     * Displays and manages a form for object edition
     *
     * @param string $id the id of the object
     * @return Response
     */
    public function editAction($id);

    /**
     * Displays an embeddable form for a given entity
     *
     * @param type $entity
     * @return Response
     */
    public function formAction($entity=null);

    /**
     * Shows an object
     *
     * @param string $id the id of the object
     * @return type
     */
    public function showAction($id);

    /**
     * Deletes multiple objects
     *
     * @return Response
     */
    public function batchDeleteAction();

    /**
     * Deletes a single object
     *
     * @param string $id
     * @return Response
     */
    public function deleteAction($id);


    /**
     * Stores the filter form data in the session
     *
     * @return Response
     */
    public function filterAction();
    
}
