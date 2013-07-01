<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\Tests\Controller\Worker;
use Qimnet\CRUDBundle\Controller\Worker\CRUDControllerWorker;
use Qimnet\CRUDBundle\Configuration\CRUDAction;

class CRUDControllerWorkerTest extends \PHPUnit_Framework_TestCase
{
    protected $crudRequest;
    protected $configuration;
    protected $tableBuilderFactory;
    protected $request;
    protected $formFactory;
    protected $formRegistry;
    protected $templating;
    protected $csrfProvider;
    protected $paginatorFactory;
    protected $session;

    protected $worker;
    protected $pathGenerator;
    protected $objectManager;
    protected $securityContext;
    protected $redirectionManager;

    protected function setUp()
    {
        $this->crudRequest = $this->getMock('Qimnet\CRUDBundle\HTTP\CRUDRequestInterface');

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
                ->disableOriginalConstructor()
                ->getMock();
        $this->crudRequest
                ->expects($this->any())
                ->method('getRequest')
                ->will($this->returnValue($this->request));
        $this->session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')
                ->disableOriginalConstructor()
                ->getMock();
        $this->request
                ->expects($this->any())
                ->method('getSession')
                ->will($this->returnValue($this->session));

        $this->configuration = $this->getMock('Qimnet\CRUDBundle\Configuration\CRUDConfigurationInterface');
        $this->crudRequest
                ->expects($this->any())
                ->method('getConfiguration')
                ->will($this->returnValue($this->configuration));

        $this->redirectionManager = $this->getMock('Qimnet\CRUDBundle\Controller\RedirectionManager\CRUDRedirectionManagerInterface');
        $this->crudRequest
                ->expects($this->any())
                ->method('getRedirectionManager')
                ->will($this->returnValue($this->redirectionManager));

        $this->formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $this->formRegistry = $this->getMock('Symfony\Component\Form\FormRegistryInterface');
        $this->templating = $this->getMock('Symfony\Component\Templating\EngineInterface');
        $this->csrfProvider = $this->getMock('Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface');
        $this->csrfProvider
                ->expects($this->any())
                ->method('generateCsrfToken')
                ->with($this->equalTo('csrf_intention'))
                ->will($this->returnValue('csrf_token'));

        $this->configuration
                ->expects($this->any())
                ->method('getCSRFIntention')
                ->will($this->returnValue('csrf_intention'));

        $this->pathGenerator = $this->getMock('Qimnet\CRUDBundle\Routing\CRUDPathGeneratorInterface');
        $this->configuration
                ->expects($this->any())
                ->method('getPathGenerator')
                ->will($this->returnValue($this->pathGenerator));

        $this->objectManager = $this->getMock('Qimnet\CRUDBundle\Persistence\ObjectManagerInterface');
        $this->configuration
                ->expects($this->any())
                ->method('getObjectManager')
                ->will($this->returnValue($this->objectManager));

        $this->configuration
                ->expects($this->any())
                ->method('getSortLinkRendererOptions')
                ->will($this->returnValue(array()));

        $this->securityContext = $this->getMock('Qimnet\CRUDBundle\Security\CRUDSecurityContextInterface');
        $this->configuration
                ->expects($this->any())
                ->method('getSecurityContext')
                ->will($this->returnValue($this->securityContext));

        $this->tableBuilderFactory = $this->getMock('Qimnet\TableBundle\Table\TableBuilderFactoryInterface');

        $this->paginatorFactory = $this->getMock('Qimnet\PaginatorBundle\Paginator\PaginatorFactoryInterface');

        $this->worker = new CRUDControllerWorker(
                $this->formFactory,
                $this->formRegistry,
                $this->templating,
                $this->tableBuilderFactory,
                $this->paginatorFactory,
                $this->csrfProvider);
        $this->worker->setCRUDRequest($this->crudRequest);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testIndexActionNotAllowed()
    {
        $this->securityContext
                ->expects($this->once())
                ->method('isActionAllowed')
                ->with($this->equalTo(CRUDAction::INDEX))
                ->will($this->returnValue(false));
        $this->worker->indexAction();
    }

    public function getIndexActionData()
    {
        return array(
            array(1,1, true, false,true),
            array(7,20,true, false,false),
            array(44,50,true, true,false),
            array(49,50, false, false,false),
        );
    }

    protected function setConfigurationProperties($properties)
    {
        foreach ($properties as $method=>$value) {
            $this->configuration->expects($this->any())
                    ->method($method)
                    ->will($this->returnValue($value));
        }
    }
    /**
     * @dataProvider getIndexActionData
     */
    public function testIndexAction($page, $lastPage, $allowDelete, $hasShow, $useObjectVars)
    {
        $testCase = $this;

        $this->setupViewVars();

        $this->securityContext
                ->expects($this->any())
                ->method('isActionAllowed')
                ->will($this->returnCallback(function($permission) use ($allowDelete) {
                    return (($permission == CRUDAction::DELETE) && $allowDelete) ||
                        ($permission == CRUDAction::INDEX);
                }));
        $paginatorOptions = array(
            'option1'=>'value1',
            'option2'=>'value2'
        );
        $data = array(
            array('id'=>1),
            array('id'=>2),
        );
        $this->setConfigurationProperties(array(
            'getTableType'=>'table_type',
            'getSortField'=>'sort_field',
            'getShowTemplate'=>$hasShow ? 'show_template' : '',
            'getPaginatorType'=>'paginator_type',
            'getPaginatorOptions'=>$paginatorOptions
        ));
        $table = $this->getMock('Qimnet\TableBundle\Table\TableInterface');
        $tableBuilder = $this->getMock('Qimnet\TableBundle\Table\TableBuilderInterface');

        $this->tableBuilderFactory
                ->expects($this->once())
                ->method("createFromType")
                ->with( $this->equalTo('table_type'))
                ->will($this->returnValue($tableBuilder));

        $tableBuilder
                ->expects($this->once())
                ->method('getTable')
                ->will($this->returnValue($table));

        $table
                ->expects($this->once())
                ->method("has")
                ->with($this->equalTo('sort_field'))
                ->will($this->returnValue(true));

        $table
                ->expects($this->once())
                ->method("getOptions")
                ->with($this->equalTo('sort_field'))
                ->will($this->returnValue(array('sort'=>'sort_column')));

        $table
                ->expects($this->once())
                ->method("createView")
                ->with($this->equalTo(array()))
                ->will($this->returnValue('table'));

        $this->objectManager
                ->expects($this->once())
                ->method('getIndexData')
                ->with($this->equalTo('sort_column'), $this->equalTo('sort_direction'))
                ->will($this->returnValue($data));

        $paginator = $this->getMock('Qimnet\PaginatorBundle\Paginator\PaginatorInterface');

        $this->paginatorFactory
                ->expects($this->once())
                ->method('create')
                ->with(
                        $this->equalTo('paginator_type'),
                        $this->equalTo($data),
                        $this->equalTo($page),
                        $this->equalTo($paginatorOptions))
                ->will($this->returnValue($paginator));

        $paginatorView = $this->getMock('Qimnet\PaginatorBundle\Paginator\PaginatorViewInterface');

        $paginator
                ->expects($this->once())
                ->method('createView')
                ->will($this->returnValue($paginatorView));

        $paginatorAdapter = $this->getMock('Qimnet\PaginatorBundle\Adapter\PaginatorAdapterInterface');

        $paginatorView
                ->expects($this->any())
                ->method('getLastPage')
                ->will($this->returnValue($lastPage));

        $paginator
                ->expects($this->any())
                ->method('getAdapter')
                ->will($this->returnValue($paginatorAdapter));

        $pageData = $useObjectVars
                ? array(
                    array(new \stdClass,array('obj_key'=>'value')),
                    array(new \stdClass,array('obj_key'=>'value'))
                )
                : array(
                    new \stdClass,
                    new \stdClass
                );
        $paginatorAdapter
                ->expects($this->any())
                ->method('getIterator')
                ->will($this->returnValue($pageData));
        $filters = $this->setupFilters();
        $filters
            ->expects($this->once())
            ->method('createView')
            ->will($this->returnValue('form_view'));
        $field = $this->getMockBuilder('Symfony\Component\Form\Form')
                ->disableOriginalConstructor()
                ->getMock();
        $fieldConfig = $this->getMock('Symfony\Component\Form\FormConfigInterface');
        $field
                ->expects($this->once())
                ->method('getConfig')
                ->will($this->returnValue($fieldConfig));
        $field
                ->expects($this->once())
                ->method('getData')
                ->will($this->returnValue('filter_value'));

        $filterOptions = array('filterkey1'=>'value1');
        $fieldConfig
                ->expects($this->once())
                ->method('getOptions')
                ->will($this->returnValue(array(
                    'filter_options'=>$filterOptions
                )));
        $filters
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayObject(array(
                'field1'=>$field
            ))));
        $this->objectManager
                ->expects($this->once())
                ->method('filterIndexData')
                ->with($this->equalTo($data),
                        $this->equalTo('field1'),
                        $this->equalTo('filter_value'),
                        $this->equalTo($filterOptions));

        $this->configuration
                ->expects($this->once())
                ->method('getIndexTemplate')
                ->will($this->returnValue('index_template'));

        $this->configuration
                ->expects($this->once())
                ->method('getIndexTitle')
                ->will($this->returnValue('index_title'));

        $this->configuration
                ->expects($this->any())
                ->method('getName')
                ->will($this->returnValue('config_name'));

        $this->templating
                ->expects($this->once())
                ->method('render')
                ->with($this->equalTo('index_template'),
                        $this->callback(function($parameters) use ($paginatorView, $page, $lastPage, $testCase, $allowDelete) {
                            $testCase->assertValidTemplatingParameters('index_title', $parameters);
                            $testCase->assertEquals('form_view', $parameters['filters_form']);
                            $testCase->assertEquals($paginatorView, $parameters['pagination']);
                            $testCase->assertEquals('qimnet_crud_index', $parameters['route']);
                            $testCase->assertEquals(array(
                                'configName'=>'config_name',
                                'sortField'=>'sort_field',
                                'sortDirection'=>'sort_direction',
                            ), $parameters['route_parameters']);
                            $testCase->assertEquals('table', $parameters['table']);
                            $testCase->assertEquals(
                                    $allowDelete ? array('delete'=>'Delete') : array(),
                                    $parameters['batch_actions']);

                            return true;
                        }))
                ->will($this->returnValue('response_content'));

        $response = $this->worker->indexAction($page, 'sort_field', 'sort_direction');
        $this->assertEquals($response->getContent(), 'response_content');
    }
    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testIndexActionWithUnexistingColumn()
    {
        $this->securityContext
                ->expects($this->once())
                ->method('isActionAllowed')
                ->will($this->returnValue(true));

        $tableBuilder = $this->getMock('Qimnet\TableBundle\Table\TableBuilderInterface');
        $this->tableBuilderFactory
                ->expects($this->once())
                ->method('createFromType')
                ->will($this->returnValue($tableBuilder));

        $table = $this->getMock('Qimnet\TableBundle\Table\TableInterface');
        $tableBuilder
                ->expects($this->once())
                ->method('getTable')
                    ->will($this->returnValue($table));
        $table
                ->expects($this->once())
                ->method('has')
                ->will($this->returnValue(false));
        $this->worker->indexAction();
    }
    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testIndexActionWithUnsortableColumn()
    {
        $this->securityContext
                ->expects($this->once())
                ->method('isActionAllowed')
                ->will($this->returnValue(true));

        $tableBuilder = $this->getMock('Qimnet\TableBundle\Table\TableBuilderInterface');
        $this->tableBuilderFactory
                ->expects($this->once())
                ->method('createFromType')
                ->will($this->returnValue($tableBuilder));

        $table = $this->getMock('Qimnet\TableBundle\Table\TableInterface');
        $tableBuilder
                ->expects($this->once())
                ->method('getTable')
                    ->will($this->returnValue($table));
        $table
                ->expects($this->once())
                ->method('has')
                ->will($this->returnValue(true));

        $table
                ->expects($this->once())
                ->method('getOptions')
                ->will($this->returnValue(array('sort'=>false)));
        $this->worker->indexAction();
    }
    protected function setupViewVars()
    {
        $this->configuration
            ->expects($this->any())
            ->method('getDefaultViewVars')
            ->with($this->equalTo($this->request))
            ->will($this->returnValue(array(
                'key1'=>'value1',
                'key2'=>'value2'
            )));
    }
    public function assertValidTemplatingParameters($title, $parameters)
    {
        $this->assertEquals($title, $parameters['title']);
        $this->assertEquals('value1', $parameters['key1']);
        $this->assertEquals('value2', $parameters['key2']);
        $this->assertEquals('csrf_token', $parameters['csrf_token']);
    }
    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testNewActionNotAllowed()
    {
        $this->securityContext
                ->expects($this->once())
                ->method('isActionAllowed')
                ->with($this->equalTo(CRUDAction::CREATE))
                ->will($this->returnValue(false));
        $this->worker->newAction();
    }

    public function getPostActionData()
    {
        return array(
            array(),
            array(true),
            array(true, true, true)
        );
    }
    /**
     * @dataProvider getPostActionData
     */
    public function testNewAction($post=false, $valid=false, $typeAsClassName=false)
    {
        $entity = new \stdClass();
        $routeParams = array('param1'=>'value1', 'param2'=>'value2');

        $this->securityContext
                ->expects($this->once())
                ->method('isActionAllowed')
                ->with($this->equalTo(CRUDAction::CREATE))
                ->will($this->returnValue(true));

        $this->objectManager
                ->expects($this->once())
                ->method('create')
                ->with($this->equalTo($routeParams))
                ->will($this->returnValue($entity));

        $formType = $this->setupFormType($entity, $typeAsClassName);

        $form = $this->setUpForm($formType, $entity, $post, $valid);

        $this->configuration
                ->expects($this->once())
                ->method('getObjectCreationParameters')
                ->will($this->returnValue(array_keys($routeParams)));

        $this->request->query = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');
        $this->request->query
                ->expects($this->any())
                ->method('has')
                ->will($this->returnValue(true));

        $this->request->query
                ->expects($this->any())
                ->method('get')
                ->will($this->returnCallback(function($key) use ($routeParams) {
                    return $routeParams[$key];
                }));
        if ($valid) {
            $this->redirectionManager
                    ->expects($this->once())
                    ->method('getCreateResponse')
                    ->with($this->identicalTo($entity))
                    ->will($this->returnValue('redirect'));
            $this->assertValidPostResponse($entity, 'newAction');
        } else {
            $this->configuration
                    ->expects($this->never())
                    ->method('getCreateRedirectResponse');
            $this->configuration
                    ->expects($this->once())
                    ->method('getNewTemplate')
                    ->will($this->returnValue('new_template'));
            $this->configuration
                    ->expects($this->once())
                    ->method('getNewTitle')
                    ->will($this->returnValue('new_title'));

            $this->pathGenerator
                    ->expects($this->once())
                    ->method('generate')
                    ->with($this->equalTo(CRUDAction::CREATE), $this->equalTo($routeParams))
                    ->will($this->returnValue('action'));
            $this->assertUnvalidPostResponse(
                    $entity, 'newAction',
                    $form, $formType, 'new_title', 'new_template');
        }
    }
    protected function setupFormType($entity, $asClassName=false, $asService=false)
    {
        $formTypeObject = $this->getMock('Symfony\Component\Form\FormTypeInterface');
        if ($asClassName) {
            $formType = get_class($formTypeObject);
            $this->formRegistry
                ->expects($this->once())
                ->method('hasType')
                ->with($this->identicalTo($formType))
                ->will($this->returnValue(false));
        } elseif ($asService) {
            $this->formRegistry
                ->expects($this->once())
                ->method('hasType')
                ->with($this->identicalTo('service'))
                ->will($this->returnValue(true));
            $this->formRegistry
                ->expects($this->once())
                ->method('getType')
                ->with($this->equalTo('service'))
                ->will($this->returnValue($formTypeObject));
            $formType = 'service';
        } else {
            $formType = $formTypeObject;
        }
        $this->configuration
                ->expects($this->once())
                ->method('getFormType')
                ->with($this->identicalTo($entity))
                ->will($this->returnValue($formType));

        return $formTypeObject;
    }
    protected function setupForm($formType, $entity, $post, $valid)
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
                ->disableOriginalConstructor()
                ->getMock();
        $this->formFactory
                ->expects($this->once())
                ->method('create')
                ->with($this->isInstanceOf(get_class($formType)),
                        $this->identicalTo($entity),
                        $this->equalTo(array()))
                ->will($this->returnValue($form));
        if (is_object($formType));
        $this->request
                ->expects($this->any())
                ->method('isMethod')
                ->with($this->equalTo('POST'))
                ->will($this->returnValue($post));

        if ($post) {
            $form
                    ->expects($this->once())
                    ->method('bind')
                    ->with($this->equalTo($this->request));
            $form
                    ->expects($this->once())
                    ->method('isValid')
                    ->will($this->returnValue($valid));
        } else {
            $form
                    ->expects($this->never())
                    ->method('bind');
        }

        return $form;
    }

    protected function assertValidPostResponse($entity, $action, $attributes=array())
    {
        $this->objectManager
                ->expects($this->once())
                ->method('persist')
                ->with($this->identicalTo($entity));
        $this->objectManager
                ->expects($this->once())
                ->method('flush');
        $this->templating
                ->expects($this->never())
                ->method('render');
        $response = call_user_func_array(array($this->worker, $action), $attributes);
        $this->assertEquals('redirect', $response);
    }
    protected function assertUnvalidPostResponse($entity, $action, $form, $formType, $title, $template, $attributes=array())
    {
        $this->setupViewVars();
        $testCase = $this;
        $this->objectManager
                ->expects($this->never())
                ->method('persist');
        $this->objectManager
                ->expects($this->never())
                ->method('flush');
        $form
                ->expects($this->once())
                ->method('createView')
                ->will($this->returnValue('form_view'));
        $formType
                ->expects($this->any())
                ->method('getName')
                ->will($this->returnValue('form_type'));
        $this->templating
                ->expects($this->once())
                ->method('render')
                ->with(
                        $this->equalTo($template),
                        $this->callback(function($parameters) use ($testCase, $title, $entity) {
                            $testCase->assertValidTemplatingParameters($title, $parameters);
                            $testCase->assertSame($entity, $parameters['entity']);
                            $testCase->assertEquals('form_view', $parameters['form']);
                            $testCase->assertEquals('action', $parameters['action']);

                            return true;
                }))
                ->will($this->returnValue('response_content'));
        $response = call_user_func_array(array($this->worker, $action), $attributes);
        $this->assertEquals('response_content', $response->getContent());
    }
    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testEditActionNotAllowed()
    {
        $entity = new \stdClass();
        $this->securityContext
                ->expects($this->once())
                ->method('isActionAllowed')
                ->with($this->equalTo(CRUDAction::UPDATE))
                ->will($this->returnValue(false));
        $this->objectManager
                ->expects($this->once())
                ->method('find')
                ->with($this->equalTo('id'))
                ->will($this->returnValue(array($entity)));
        $this->worker->editAction('id');
    }
    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testEditActionNotFound()
    {
        $this->objectManager
                ->expects($this->once())
                ->method('find')
                ->with($this->equalTo('id'))
                ->will($this->returnValue(array()));
        $this->worker->editAction('id');
    }
    /**
     * @dataProvider getPostActionData
     */
    public function testEditAction($post=false, $valid=false, $typeAsClassName=false)
    {
        $entity = new \stdClass();
        $this->securityContext
                ->expects($this->once())
                ->method('isActionAllowed')
                ->with($this->equalTo(CRUDAction::UPDATE))
                ->will($this->returnValue(true));

        $this->objectManager
                ->expects($this->once())
                ->method('find')
                ->with($this->equalTo('id'))
                ->will($this->returnValue(array($entity)));

        $formType = $this->setupFormType($entity, $typeAsClassName);

        $form = $this->setUpForm($formType, $entity, $post, $valid);

        if ($valid) {
            $this->redirectionManager
                    ->expects($this->once())
                    ->method('getUpdateResponse')
                    ->with($this->identicalTo($entity))
                    ->will($this->returnValue('redirect'));
            $this->assertValidPostResponse($entity, 'editAction', array('id'=>'id'));
        } else {
            $this->redirectionManager
                    ->expects($this->never())
                    ->method('getUpdateRedirectResponse');
            $this->configuration
                    ->expects($this->once())
                    ->method('getEditTemplate')
                    ->will($this->returnValue('edit_template'));
            $this->configuration
                    ->expects($this->once())
                    ->method('getEditTitle')
                    ->will($this->returnValue('edit_title'));
            $this->pathGenerator
                    ->expects($this->once())
                    ->method('generate')
                    ->with($this->equalTo(CRUDAction::UPDATE), $this->equalTo(array()), $this->identicalTo($entity))
                    ->will($this->returnValue('action'));
            $this->assertUnvalidPostResponse(
                    $entity, 'editAction',
                    $form, $formType, 'edit_title', 'edit_template', array('id'=>'id'));
        }
    }

    public function getFormActionData()
    {
        return array(
            array(false,true, false),
            array(true,true, false),
            array(true, false, false),
            array(true, false, true)
        );
    }

    /**
     * @dataProvider getFormActionData
     */
    public function testFormAction($new, $withObject, $typeAsService)
    {
        $entity = new \stdClass();
        $routeParameters = array('key1'=>'value1');
        $this->securityContext
                ->expects($this->once())
                ->method('isActionAllowed')
                ->with($new ? CRUDAction::CREATE : CRUDAction::UPDATE)
                ->will($this->returnValue(true));
        $this->configuration
                ->expects($this->once())
                ->method('getDefaultViewVars')
                ->will($this->returnValue(array(
                        'route_parameters'=>$routeParameters,
                        'var1'=>'value1'
                )));
        if ($withObject) {
            $this->objectManager
                    ->expects($this->never())
                    ->method('create');
        } else {
            $this->objectManager
                    ->expects($this->once())
                    ->method('create')
                    ->will($this->returnValue($entity));
        }
        $this->objectManager
                ->expects($this->any())
                ->method('isNew')
                ->with($this->identicalTo($entity))
                ->will($this->returnValue($new ? true : false));
        $this->pathGenerator
                ->expects($this->once())
                ->method('generate')
                ->with(
                        $this->equalTo($new ? CRUDAction::CREATE : CRUDAction::UPDATE),
                        $this->equalTo($new ? $routeParameters : array()),
                        $this->identicalTo($new ? null : $entity))
                ->will($this->returnValue('action'));

        $formType = $this->setupFormType($entity, false, $typeAsService);
        $form = $this->setupForm($formType, $entity, false, false);
        $form
                ->expects($this->once())
                ->method('createView')
                ->will($this->returnValue('form_view'));

        $this->configuration
                ->expects($this->once())
                ->method('getFormTemplate')
                ->will($this->returnValue('form_template'));

        $this->templating
                ->expects($this->once())
                ->method('render')
                ->with(
                        $this->equalTo('form_template'),
                        $this->equalTo(array(
                            'form'=>'form_view',
                            'entity'=>$entity,
                            'standalone'=>false,
                            'var1'=>'value1',
                            'route_parameters'=>array('key1'=>'value1'),
                            'csrf_token'=>'csrf_token',
                            'action'=>'action'
                        )))
                ->will($this->returnValue('success'));

        $this->assertEquals('success', $this->worker->formAction($withObject ? $entity : null)->getContent());

    }
    /**
     * @dataProvider getFormActionData
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFormActionNotAllowed($new=false, $withObject=true)
    {
        $this->worker->formAction($withObject ? null : new \stdClass);
    }

    public function testDeleteAction()
    {
        $entity = new \stdClass();

        $this->securityContext
                ->expects($this->once())
                ->method('isActionAllowed')
                ->with($this->equalTo(CRUDAction::DELETE))
                ->will($this->returnValue(true));

        $this->csrfProvider
                ->expects($this->once())
                ->method('isCsrfTokenValid')
                ->will($this->returnValue(true));

        $this->objectManager
                ->expects($this->once())
                ->method('find')
                ->with($this->equalTo('id'))
                ->will($this->returnValue(array($entity)));

        $this->objectManager
                ->expects($this->once())
                ->method('remove')
                ->with($this->identicalTo($entity));

        $this->objectManager
                ->expects($this->once())
                ->method('flush');

        $this->redirectionManager
                ->expects($this->once())
                ->method('getDeleteResponse')
                ->with($this->identicalTo($entity))
                ->will($this->returnValue('success'));

        $this->assertEquals('success', $this->worker->deleteAction('id'));
    }
    /**
     * @expectedException \Exception
     */
    public function testDeleteActionBadCSRF()
    {
        $this->csrfProvider
                ->expects($this->once())
                ->method('isCsrfTokenValid')
                ->will($this->returnValue(false));

        $this->worker->deleteAction('id');
    }
    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testDeleteActionNotAllowed()
    {
        $entity = new \stdClass();

        $this->securityContext
                ->expects($this->once())
                ->method('isActionAllowed')
                ->with($this->equalTo(CRUDAction::DELETE))
                ->will($this->returnValue(false));

        $this->csrfProvider
                ->expects($this->once())
                ->method('isCsrfTokenValid')
                ->will($this->returnValue(true));

        $this->objectManager
                ->expects($this->once())
                ->method('find')
                ->with($this->equalTo('id'))
                ->will($this->returnValue(array($entity)));

        $this->worker->deleteAction('id');
    }
    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testDeleteActionNotFound()
    {
        $this->csrfProvider
                ->expects($this->once())
                ->method('isCsrfTokenValid')
                ->will($this->returnValue(true));

        $this->objectManager
                ->expects($this->once())
                ->method('find')
                ->with($this->equalTo('id'))
                ->will($this->returnValue(array()));

        $this->worker->deleteAction('id');
    }
    public function getBatchDeleteActionData()
    {
        return array(
            array(array(new \stdClass, new \stdClass)),
            array(array()),
        );
    }
    /**
     * @dataProvider getBatchDeleteActionData
     */
    public function testBatchDeleteAction($entities)
    {
        $testCase = $this;
        $this->request
                ->expects($this->any())
                ->method('get')
                ->will($this->returnCallback(function($name, $default=null) use ($testCase) {
                    if ($name=='ids') {
                        $testCase->assertEquals(array(), $default);

                        return array();
                    }
                }));

        $this->securityContext
                ->expects($this->any())
                ->method('isActionAllowed')
                ->with($this->equalTo(CRUDAction::DELETE))
                ->will($this->returnValue(true));

        $this->csrfProvider
                ->expects($this->once())
                ->method('isCsrfTokenValid')
                ->will($this->returnValue(true));

        $this->objectManager
                ->expects($this->once())
                ->method('find')
                ->will($this->returnValue($entities));

        $this->objectManager
                ->expects($this->exactly(count($entities)))
                ->method('remove');

        $this->objectManager
                ->expects($this->exactly(count($entities) ? 1 : 0))
                ->method('flush');

        $this->redirectionManager
                ->expects($this->once())
                ->method('getDeletesResponse')
                ->with(
                        count($entities)
                        ? $this->equalTo('')
                        : $this->logicalNot($this->equalTo('')))
                ->will($this->returnValue('success'));

        $this->assertEquals('success', $this->worker->batchDeleteAction());
    }
    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testBatchDeleteActionNotAllowed()
    {
        $testCase = $this;
        $this->csrfProvider
                ->expects($this->once())
                ->method('isCsrfTokenValid')
                ->will($this->returnValue(true));
        $this->request
                ->expects($this->any())
                ->method('get')
                ->will($this->returnCallback(function($name, $default=null) use ($testCase) {
                    if ($name=='ids') {
                        $testCase->assertEquals(array(), $default);

                        return array();
                    }
                }));
        $this->securityContext
                ->expects($this->any())
                ->method('isActionAllowed')
                ->with($this->equalTo(CRUDAction::DELETE))
                ->will($this->returnValue(false));

        $entities = array(new \stdClass, new \stdClass);
        $this->objectManager
                ->expects($this->once())
                ->method('find')
                ->will($this->returnValue($entities));

        $this->worker->batchDeleteAction();

    }
    /**
     * @expectedException \Exception
     */
    public function testBatchDeleteActionBadCSRF()
    {
        $this->csrfProvider
                ->expects($this->once())
                ->method('isCsrfTokenValid')
                ->will($this->returnValue(false));
        $this->worker->batchDeleteAction();

    }
    protected function setupFilters()
    {
        $filters =  $this->getMockBuilder('Symfony\Component\Form\Form')
                ->disableOriginalConstructor()
                ->getMock();

        $this->configuration
                ->expects($this->any())
                ->method('getFilterType')
                ->will($this->returnValue('filter_type'));
        $this->formRegistry
            ->expects($this->once())
            ->method('hasType')
            ->with($this->identicalTo('filter_type'))
            ->will($this->returnValue(true));
        $this->formFactory
                ->expects($this->once())
                ->method('create')
                ->will($this->returnValue($filters));

        return $filters;
    }

    public function testFilterAction()
    {
        $form = $this->setupFilters();
        $form
                ->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($this->request));
        $form
                ->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue(true));
        $form
                ->expects($this->once())
                ->method('getData')
                ->will($this->returnValue('form_data'));
        $this->redirectionManager
                ->expects($this->once())
                ->method('getFilterResponse')
                ->will($this->returnValue('success'));

        $this->assertEquals('success', $this->worker->filterAction());
    }
    /**
     * @expectedException \Exception
     */
    public function testFiltersUnvalid()
    {
        $form = $this->setupFilters();
        $form
                ->expects($this->once())
                ->method('bind')
                ->with($this->equalTo($this->request));
        $form
                ->expects($this->once())
                ->method('isValid')
                ->will($this->returnValue(false));
        $this->worker->filterAction();
    }
    public function testShowAction()
    {
        $entity = new \stdClass();
        $this->securityContext
                ->expects($this->once())
                ->method('isActionAllowed')
                ->with($this->equalTo(CRUDAction::SHOW), $this->identicalTo($entity))
                ->will($this->returnValue(true));
        $this->objectManager
                ->expects($this->once())
                ->method('find')
                ->with($this->equalTo('id'))
                ->will($this->returnValue(array($entity)));
        $this->templating
                ->expects($this->once())
                ->method('render')
                ->with($this->equalTo('show_template'), $this->equalTo(array(
                    'entity'=>$entity,
                    'csrf_token'=>'csrf_token'
                )))
                ->will($this->returnValue('success'));
        $this->setConfigurationProperties(array(
            'getShowTemplate'=>'show_template'
        ));
        $this->assertEquals('success', $this->worker->showAction('id')->getContent());
    }
    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testShowActionNotAllowed()
    {
        $entity = new \stdClass();
        $this->securityContext
                ->expects($this->once())
                ->method('isActionAllowed')
                ->with($this->equalTo(CRUDAction::SHOW), $this->identicalTo($entity))
                ->will($this->returnValue(false));
        $this->objectManager
                ->expects($this->once())
                ->method('find')
                ->with($this->equalTo('id'))
                ->will($this->returnValue(array($entity)));
        $this->worker->showAction('id');
    }
}
