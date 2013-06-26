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
use Symfony\Component\HttpFoundation\Request;
use Qimnet\CRUDBundle\Routing\CRUDPathGeneratorInterface;
use Qimnet\CRUDBundle\Persistence\ObjectManagerInterface;
use Qimnet\CRUDBundle\Security\CRUDSecurityContextInterface;

/**
 * CRUD configuration class interface
 */
interface CRUDConfigurationInterface
{
    /**
     * Returns the name of the configuration
     *
     * @return string
     */
    public function getName();
    /**
     * Returns the default view vars for a given request
     *
     * @param  Request $request
     * @return array
     */
    public function getDefaultViewVars(Request $request);

    /**
     * Returns the path to the template for the show action
     *
     * @return string
     */
    public function getShowTemplate();

    /**
     * Returns the class of the managed objects
     *
     * @return string
     */
    public function getObjectClass();

    /**
     * Returns the path the base template
     *
     * @return string
     */
    public function getBaseTemplate();

    /**
     * Returns the path to the template for the index action
     *
     * @return string
     */
    public function getIndexTemplate();

    /**
     * Returns the path to the template for the update action
     *
     * @return string
     */
    public function getEditTemplate();

    /**
     * Returns the path to the inner form template for the update and create actions
     *
     * @return string
     */
    public function getFormTemplate();

    /**
     * Returns the path to the template for the create action
     *
     * @return string
     */
    public function getNewTemplate();

    /**
     * Returns the number of records per page for the index view
     *
     * @return int
     */
    public function getLimitPerPage();

    /**
     * Returns the CSRF intention used for all actions
     *
     * @return string
     */
    public function getCSRFIntention();

    /**
     * Returns the title for the index page
     *
     * @return string
     */
    public function getIndexTitle();

    /**
     * Returns the title for the edit page
     *
     * @return string
     */
    public function getEditTitle();

    /**
     * Returns the title for the new page
     *
     * @return string
     */
    public function getNewTitle();

    /**
     * Returns the root alias for persistence layer queries
     *
     * @return string
     */
    public function getQueryAlias();

    /**
     * Returns the path generator used by CRUD actions and templates.
     *
     * @return CRUDPathGeneratorInterface
     */
    public function getPathGenerator();

    /**
     * Returns the object manager used by CRUD actions and templates.
     *
     * @return ObjectManagerInterface
     */
    public function getObjectManager();

    /**
     * Returns the CRUD security context used by CRUD actions and templates.
     *
     * @return CRUDSecurityContextInterface
     */
    public function getSecurityContext();

    /**
     * Returns the FormType for the entity
     *
     * @param  object $entity
     * @return mixed  a FormType object or the name of a FormType
     */
    public function getFormType($entity);

    /**
     * Returns the TableType used by the index action
     *
     * @param  object $entity
     * @return mixed  a TableType object, a TableType name, or the name of a TableType class
     */
    public function getTableType();

    /**
     * Returns the FilterType used by the index action
     *
     * @param  object $entity
     * @return mixed  a FilterType object, a FilterType name, or the name of a TableType class
     */
    public function getFilterType();

    /**
     * Returns the paginator type used by the index action
     *
     * @return string
     */
    public function getPaginatorType();

    /**
     * Returns the options of the paginator used by the index action
     *
     * @return array
     */
    public function getPaginatorOptions();

    /**
     * Returns the names of the GET parameters that should be used to generate
     * the create action's URL
     *
     * @return array
     */
    public function getNewRouteParameterNames();

    /**
     * Returns the options for the rendererer of the headers of CRUD lists
     * 
     * @return array
     */
    public function getSortLinkRendererOptions();
}
