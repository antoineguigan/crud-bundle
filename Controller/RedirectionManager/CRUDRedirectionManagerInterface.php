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

/**
 * Interface for CRUD redirection managers
 *
 * Provides success responses for CRUD actions.
 */
interface CRUDRedirectionManagerInterface
{
    /**
     * Redirects the user after the filter form data has been stored
     *
     * @return Response
     */
    public function getFilterResponse();

    /**
     * Redirects the user after an object creation
     *
     * @var object $entity the created entity
     * @return Response
     */
    public function getCreateResponse($entity);

    /**
     * Redirects the user after an object update
     *
     * @var object $entity the updated entity
     * @return Response
     */
    public function getUpdateResponse($entity);

    /**
     * Redirects the user after an object deletion
     *
     * @var object $entity the deleted entity
     * @return Response
     */
    public function getDeleteResponse($entity);

    /**
     * Reditects the user after a multiple obhect deletion
     *
     * @var string $error An error string if the deletion was not completely successful
     * @return Response
     */
    public function getDeletesResponse($error='');
}
