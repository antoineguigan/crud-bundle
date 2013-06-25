<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\Controller\RedirectionManager;

use Symfony\Component\HttpFoundation\Response;

interface CRUDRedirectionManagerInterface
{
    /**
     * @return Response
     */
    public function getFilterResponse();
    /**
     * @return Response
     */
    public function getCreateResponse($entity);

    /**
     * @return Response
     */
    public function getUpdateResponse($entity);

    /**
     * @return Response
     */
    public function getDeleteResponse($entity);
    /**
     * @return Response
     */
    public function getDeletesResponse($error='');
}
