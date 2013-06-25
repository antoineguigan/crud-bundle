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
    public function indexAction($page=1, $sortField='id', $sortDirection='desc');
    public function newAction();
    public function batchDeleteAction();
    public function deleteAction($id);
    public function filterAction();
    public function editAction($id);
    public function showAction($id);
    public function formAction($entity=null);
}
