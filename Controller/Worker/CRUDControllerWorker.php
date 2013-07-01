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

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Qimnet\PaginatorBundle\Paginator\PaginatorInterface;
use Qimnet\PaginatorBundle\Paginator\PaginatorFactoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;
use Qimnet\CRUDBundle\Configuration\CRUDAction;
use Symfony\Component\Form\FormRegistryInterface;
use Qimnet\TableBundle\Table\TableBuilderFactoryInterface;
use Qimnet\CRUDBundle\Configuration\CRUDConfigurationInterface;
use Qimnet\CRUDBundle\HTTP\CRUDRequestInterface;

/**
 * Worker class for CRUD requests
 *
 * This class contains all the controller logic for CRUD requests.
 * Custom batch actions can be created by extending this class and overriding
 * the getBatchActions method.
 */
class CRUDControllerWorker implements CRUDControllerWorkerInterface
{
    /**
     *
     * @var CRUDConfigurationInterface
     */
    protected $tableBuilderFactory;

    /**
     * @var CRUDRequestInterface
     */
    protected $CRUDRequest;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var FormRegistryInterface
     */
    protected $formRegistry;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var CsrfProviderInterface
     */
    protected $csrfProvider;

    /**
     * @var PaginatorFactoryInterface
     */
    protected $paginatorFactory;

    /**
     * Constructor
     *
     * @param FormFactoryInterface               $formFactory
     * @param FormRegistryInterface              $formRegistry
     * @param EngineInterface                    $templating
     * @param TableBuilderFactoryInterface       $tableBuilderFactory
     * @param PaginatorFactoryInterface          $paginatorFactory
     * @param CsrfProvider\CsrfProviderInterface $csrfProvider
     */
    public function __construct(
            FormFactoryInterface $formFactory,
            FormRegistryInterface $formRegistry,
            EngineInterface $templating,
            TableBuilderFactoryInterface $tableBuilderFactory,
            PaginatorFactoryInterface $paginatorFactory,
            CsrfProviderInterface $csrfProvider)
    {
        $this->formFactory = $formFactory;
        $this->formRegistry = $formRegistry;
        $this->templating = $templating;
        $this->csrfProvider = $csrfProvider;
        $this->tableBuilderFactory = $tableBuilderFactory;
        $this->paginatorFactory = $paginatorFactory;
    }

    /**
     * Sets the current CRUD request
     *
     * @param CRUDRequestInterface $CRUDRequest
     */
    public function setCRUDRequest(CRUDRequestInterface $CRUDRequest=null)
    {
        $this->CRUDRequest = $CRUDRequest;
    }

    /**
     * @inheritdoc
     */
    public function indexAction($page = 1, $sortField = 'id', $sortDirection = 'desc')
    {
        $configuration = $this->getConfiguration();
        if (!$configuration->getSecurityContext()->isActionAllowed(CRUDAction::INDEX)) {
            throw new AccessDeniedException;
        }
        $table = $this->tableBuilderFactory
                ->createFromType($configuration->getTableType())
                ->getTable();
        if (!$table->has($sortField)) {
            throw new NotFoundHttpException(sprintf('Column "%s" does not exist', $sortField));
        }
        $sortOptions = $table->getOptions($sortField);
        if (isset($sortOptions['sort'])&& !$sortOptions['sort']) {
            throw new NotFoundHttpException(sprintf('Column "%s" is not sortable', $sortField));
        }
        $sortColumn = isset($sortOptions['sort']) ? $sortOptions['sort'] : $sortField;

        $objectManager = $configuration->getObjectManager();
        $data = $objectManager->getIndexData($sortColumn, $sortDirection);

        if ($configuration->getFilterType()) {
            $filtersForm = $this->getFiltersForm();
            foreach ($filtersForm as $column=>$field) {
                $options = $field->getConfig()->getOptions();
                $objectManager->filterIndexData($data, $column, $field->getData(), $options['filter_options']);
            }
        }

        $pagination = $this->paginatorFactory->create(
                $configuration->getPaginatorType(),
                $data,
                $page,
                $configuration->getPaginatorOptions());

        return $this->render(
                        $configuration->getIndexTemplate(), array(
                    'title' => $configuration->getIndexTitle(),
                    'filters_form' => isset($filtersForm) ? $filtersForm->createView() : null,
                    'pagination' => $pagination->createView(),
                    'route' => 'qimnet_crud_index',
                    'route_parameters' => array(
                        'configName' => $configuration->getName(),
                        'sortField' => $sortField,
                        'sortDirection' => $sortDirection
                    ),
                    'table' => $table->createView($configuration->getSortLinkRendererOptions()),
                    'batch_actions' => $this->getAllowedBatchActions($pagination)
                ) + $this->getDefaultViewVars());
    }

    /**
     * @inheritdoc
     */
    public function newAction()
    {
        if (!$this->getConfiguration()->getSecurityContext()->isActionAllowed(CRUDAction::CREATE)) {
            throw new AccessDeniedException;
        }
        $parameters = array();
        foreach ($this->getConfiguration()->getObjectCreationParameters() as $name) {
            if ($this->getRequest()->query->has($name)) {
                $parameters[$name] = $this->getRequest()->query->get($name);
            }
        }
        $entity = $this->createEntity($parameters);
        $form = $this->getEntityForm($entity);
        $response = new Response;
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $this->persistEntity($entity, true);

                return $this->getRedirectionManager()->getCreateResponse($entity);
            }
            $response->headers->set('X-Form-errors', 'True');
        }

        return $this->render(
                        $this->getConfiguration()->getNewTemplate(), array(
                    'action'=>  $this->getConfiguration()->getPathGenerator()->generate(CRUDAction::CREATE, $parameters),
                    'entity' => $entity,
                    'title' => $this->getConfiguration()->getNewTitle(),
                    'form' => $form->createView(),
                ) + $this->getDefaultViewVars(), $response);
    }

    /**
     * @inheritdoc
     */
    public function batchDeleteAction()
    {
        $this->checkCSRFToken();
        $entities = $this->findEntities($this->getRequest()->get('ids', array()));
        if (!count($entities)) {
            return $this->getRedirectionManager()->getDeletesResponse('Please select at least one element.');
        }
        foreach ($entities as $entity) {
            $this->doDelete($entity);
        }
        $this->flushEntities();

        return $this->getRedirectionManager()->getDeletesResponse();
    }

    /**
     * @inheritdoc
     */
    public function deleteAction($id)
    {
        $this->checkCSRFToken();
        $entity = $this->findEntity($id);
        $this->doDelete($entity);
        $this->flushEntities();

        return $this->getRedirectionManager()->getDeleteResponse($entity);
    }

    /**
     * @inheritdoc
     */
    public function filterAction()
    {
        $form = $this->getFiltersForm();
        $form->bind($this->getRequest());
        if (!$form->isValid()) {
            throw new \Exception('Form is not valid!');
        }
        $this->setFiltersData($form->getData());

        return $this->getRedirectionManager()->getFilterResponse();
    }

    /**
     * @inheritdoc
     */
    public function editAction($id)
    {
        $entity = $this->findEntity($id);
        if (!$this->getConfiguration()->getSecurityContext()->isActionAllowed(CRUDAction::UPDATE, $entity)) {
            throw new AccessDeniedException;
        }
        $response = new Response;
        $form = $this->getEntityForm($entity);
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $this->persistEntity($entity);

                return $this->getRedirectionManager()->getUpdateResponse($entity);
            }
            $response->headers->set('X-Form-errors', 'True');
        }

        return $this->render(
                        $this->getConfiguration()->getEditTemplate(), array(
                    'entity' => $entity,
                    'title' => $this->getConfiguration()->getEditTitle(),
                    'form' => $form->createView(),
                    'action'=>  $this->getConfiguration()->getPathGenerator()->generate(CRUDAction::UPDATE,array(),$entity),
                        ) + $this->getDefaultViewVars(), $response);
    }

    /**
     * @inheritdoc
     */
    public function showAction($id)
    {
        $entity = $this->findEntity($id);
        if (!$this->getConfiguration()->getSecurityContext()->isActionAllowed(CRUDAction::SHOW, $entity)) {
            throw new AccessDeniedException;
        }

        return $this->render(
            $this->getConfiguration()->getShowTemplate(), array(
            'entity' => $entity,
                ) + $this->getDefaultViewVars());
    }

    /**
     * @inheritdoc
     */
    public function formAction($entity = null)
    {
        if (!$this->getConfiguration()->getSecurityContext()->isActionAllowed(
                        (is_null($entity) || $this->getConfiguration()->getObjectManager()->isNew($entity)) ? CRUDAction::CREATE : CRUDAction::UPDATE, $entity)) {
            throw new AccessDeniedException;
        }
        if (is_null($entity)) {
            $entity = $this->createEntity();
        }
        $params = array(
            'form' => $this->getEntityForm($entity)->createView(),
            'entity' => $entity,
            'standalone' => false) + $this->getDefaultViewVars();

        if ($this->getConfiguration()->getObjectManager()->isNew($entity)) {
            $params['action'] = $this->getConfiguration()->getPathGenerator()->generate(CRUDAction::CREATE, $params['route_parameters']);
        } else {
            $params['action'] = $this->getConfiguration()->getPathGenerator()->generate(CRUDAction::UPDATE, array(), $entity);
        }

        return $this->render($this->getConfiguration()->getFormTemplate(), $params);
    }

    /**
     * Returns an associative array representing the available batch actions
     *
     * Batch actions can be added to a configuration by overriding this method.
     * The returned array's keys contain the name of the batch actions, and the
     * values contain their labels.
     *
     * Each batch action must hava a corresponding batch{XXX}Action method in the
     * worker, where {XXX} is the capitalized name of the batch action.
     *
     * @return string
     */
    protected function getBatchActions()
    {
        return array(CRUDAction::DELETE=>'Delete');
    }


    private function getAllowedBatchActions(PaginatorInterface $pagination)
    {
        $availableBatchActions = $this->getBatchActions();
        $allowedBatchActions = array();
        $batchActions = array();
        $security = $this->getConfiguration()->getSecurityContext();
        $batchActionKeys = array_keys($availableBatchActions);
        $iterator = $pagination->getAdapter()->getIterator();
        foreach ($iterator as $entity) {
            if (is_array($entity) && isset($entity[1])) {
                $objectVars = $entity[1];
                $entity = $entity[0];
            } else {
                $objectVars = array();
            }
            foreach ($availableBatchActions as $actionName=>$actionLabel) {
                if ($security->isActionAllowed($actionName, $entity, $objectVars)) {
                    $allowedBatchActions[$actionName] = $actionLabel;
                    unset($availableBatchActions[$actionName]);
                    break;
                }
            }
            if (!count($availableBatchActions)) {
                break;
            }
        }
        foreach ($batchActionKeys as $actionName) {
            if (isset($allowedBatchActions[$actionName])) {
                $batchActions[$actionName] = $allowedBatchActions[$actionName];
            }
        }

        return $batchActions;
    }

    private function getDefaultViewVars()
    {
        $vars = $this->getConfiguration()->getDefaultViewVars($this->getRequest());
        $vars['csrf_token'] = $this->csrfProvider->generateCsrfToken($this->getConfiguration()->getCSRFIntention());

        return $vars;
    }

    private function doDelete($entity)
    {
        if (!$this->getConfiguration()->getSecurityContext()->isActionAllowed(CRUDAction::DELETE, $entity)) {
            throw new AccessDeniedException;
        }
        $this->getConfiguration()->getObjectManager()->remove($entity);
    }

    private function checkCSRFToken()
    {
        if (!$this->csrfProvider->isCsrfTokenValid($this->getConfiguration()->getCSRFIntention(), $this->getRequest()->get('_token'))) {
            throw new \Exception('Bad CSRF Token');
        }
    }

    private function getEntityForm($entity)
    {
        return $this->formFactory->create($this->getFormType($this->getConfiguration()->getFormType($entity)), $entity);
    }
    private function getFiltersForm()
    {
        return $this->formFactory->create($this->getFormType($this->getConfiguration()->getFilterType()), $this->getFiltersData());
    }
    private function getFormType($formType)
    {
        if (is_string($formType)) {
            if ($this->formRegistry->hasType($formType)) {
                $formType = $this->formRegistry->getType($formType);
            } else {
                $formType = new $formType;
            }
        }

        return $formType;
    }

    private function render($template, $parameters, $response = null)
    {
        if (is_null($response)) {
            $response = new Response;
        }
        $response->setContent($this->templating->render($template, $parameters));

        return $response;
    }

    private function findEntities($ids)
    {
        return $this->getConfiguration()->getObjectManager()->find($ids);
    }

    private function findEntity($id)
    {
        $entities = $this->findEntities($id);
        if (!count($entities)) {
            throw new NotFoundHttpException;
        }

        return $entities[0];
    }

    private function flushEntities()
    {
        $this->getConfiguration()->getObjectManager()->flush();
    }

    private function persistEntity($entity, $isNew = false)
    {
        $this->getConfiguration()->getObjectManager()->persist($entity);
        $this->flushEntities();
    }

    private function createEntity(array $parameters=array())
    {
        return $this->getConfiguration()->getObjectManager()->create($parameters);
    }
    private function getConfiguration()
    {
        return $this->CRUDRequest->getConfiguration();
    }

    private function getRequest()
    {
        return $this->CRUDRequest->getRequest();
    }

    private function getRedirectionManager()
    {
        return $this->CRUDRequest->getRedirectionManager();
    }
    private function getFiltersData()
    {
        return $this->getRequest()->getSession()->get($this->getFiltersSessionKey());
    }
    private function setFiltersData($data)
    {
        $this->getRequest()->getSession()->set($this->getFiltersSessionKey(), $data);
    }
    private function getFiltersSessionKey()
    {
        return sprintf('qimnet.crud.filter.%s', $this->getConfiguration()->getName());
    }
}
