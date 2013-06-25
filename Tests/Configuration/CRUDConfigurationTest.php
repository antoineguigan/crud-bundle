<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\Tests\Configuration;

use Qimnet\TableBundle\Table\Action;
use Qimnet\CRUDBundle\Configuration\CRUDConfiguration;

class CRUDConfigurationTest extends \PHPUnit_Framework_TestCase
{
    protected $objectManagerFactory;
    protected $securityContextFactory;
    protected $pathGeneratorFactory;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->objectManagerFactory = $this->getMock('Qimnet\CRUDBundle\Persistence\ObjectManagerFactoryInterface');
        $this->securityContextFactory = $this->getMock('Qimnet\CRUDBundle\Security\CRUDSecurityContextFactoryInterface');
        $this->templating = $this->getMock('Symfony\Component\Templating\EngineInterface');
        $this->pathGeneratorFactory = $this->getMock('Qimnet\CRUDBundle\Routing\CRUDPathGeneratorFactoryInterface');
    }

    protected function createConfiguration($options=array(), $withShow=true,
            $filterEnabled=false, $class='\Company\TestBundle\Entity\CRUDTest')
    {
        if (!isset($options['show_template'])) {
            $options['show_template'] = ($withShow) ? 'show_template' : false;
        }
        $options['object_class'] = $class;
        if (!isset($options['filter_type'])) {
            $options['filter_type'] = $filterEnabled ? 'filter_type' : false;
        }
        if (!isset($options['base_template'])) {
            $options['base_template'] = 'base_template';
        }
        if (!isset($options['id_column'])) {
            $options['id_column'] = 'id_column';
        }
        if (!isset($options['route_prefix'])) {
            $options['route_prefix'] = 'route_prefix';
        }
        $parts = explode('\\', $class);
        $options['name'] = $this->getMockClass('\stdClass', array(), array(), array_pop($parts));
        $configuration = new CRUDConfiguration(
                    $this->objectManagerFactory,
                    $this->securityContextFactory,
                    $this->pathGeneratorFactory,
                    $options);

        return $configuration;
    }

    protected function getMockSecurityContext(array $options=array(), $class='')
    {
        $securityContext = $this->getMock('Qimnet\TableBundle\Security\SecurityContextInterface');
        $this->securityContextFactory
                ->expects($this->once())
                ->method('create')
                ->with($this->equalTo($options), $this->equalTo($class))
                ->will($this->returnValue($securityContext));

        return $securityContext;
    }
    protected function assertOptionSet($optionName, $methodName, array $parameters=array())
    {
        $configuration = $this->createConfiguration();
        $this->assertNotNull(call_user_func_array(array($configuration,$methodName),$parameters));
        $configuration2 = $this->createConfiguration(array($optionName=>'option_value'));
        $this->assertEquals('option_value', call_user_func_array(array($configuration2,$methodName),$parameters));
    }

    public function testGetBaseTemplate()
    {
        $this->assertOptionSet('base_template', 'getBaseTemplate');
    }

    public function testGetCSRFIntention()
    {
        $this->assertOptionSet('csrf_intention', 'getCSRFIntention');
    }

    public function testGetFormTemplate()
    {
        $this->assertOptionSet('form_template', 'getFormTemplate');
    }

    public function testGetEditTemplate()
    {
        $this->assertOptionSet('edit_template', 'getEditTemplate');
    }
    public function testGetShowTemplate()
    {
        $this->assertOptionSet('show_template', 'getShowTemplate');
    }

    public function testGetEditTitle()
    {
        $this->assertOptionSet('edit_title', 'getEditTitle');
    }

    public function testGetIndexTemplate()
    {
        $this->assertOptionSet('index_template', 'getIndexTemplate');
    }

    public function testGetIndexTitle()
    {
        $this->assertOptionSet('index_title', 'getIndexTitle');
    }

    public function testGetLimitPerPage()
    {
        $this->assertOptionSet('limit_per_page', 'getLimitPerPage');
    }

    public function testGetNewTemplate()
    {
        $this->assertOptionSet('new_template', 'getNewTemplate');
    }

    public function testGetNewTitle()
    {
        $this->assertOptionSet('new_title', 'getNewTitle');
    }

    public function testGetFormType()
    {
        $this->assertOptionSet('form_type', 'getFormType', array(new \stdClass()));
    }

    public function testGetFilterType()
    {
        $this->assertOptionSet('filter_type', 'getFilterType');
    }

    public function testGetQueryAlias()
    {
        $this->assertOptionSet('query_alias', 'getQueryAlias');
    }

    public function testGetPaginatorOptions()
    {
        $this->assertOptionSet('paginator_options', 'getPaginatorOptions');
    }
    public function testGetPaginatorType()
    {
        $this->assertOptionSet('paginator_type', 'getPaginatorType');
    }
    public function testGetTableType()
    {
        $this->assertOptionSet('table_type', 'getTableType');
    }

    public function testGetName()
    {
        $configuration = $this->createConfiguration();
        $this->assertEquals('CRUDTest', $configuration->getName());
    }

    public function getGetDefaultViewVarsData()
    {
        return array(
            array(array()),
            array(array('_inline'=>true),true),
            array(array('_ajax_link'=>true),false, true),
            array(array('_ajax_iframe'=>true),false, true),
            array(array('_ajax_iframe'=>true, '_inline'=>false),false, true),
            array(array('_ajax_iframe'=>true, '_inline'=>true),true, true,false)
        );
    }
    /**
     * @dataProvider getGetDefaultViewVarsData
     */
    public function testGetDefaultViewVars($parameters, $isInlineRequest=false, $isAjaxRequest=false, $indexAllowed=true)
    {
        $configuration = $this->createConfiguration(array(
            'route_prefix'=>'route_prefix',
            'form_template'=>'form_template',
        ));
        $securityContext = $this->getMockSecurityContext();
        $securityContext
                ->expects($this->once())
                ->method('isActionAllowed')
                ->with($this->equalTo(Action::INDEX))
                ->will($this->returnValue($indexAllowed));

        $request = $this->getMockRequest($parameters);

        $pathGenerator = $this->getMockPathGenerator();

        $pathGenerator
                ->expects($this->any())
                ->method('generate')
                ->will($this->returnValueMap(array(
                    array(Action::INDEX, array(), null, array(), 'index_path'),
                    array(Action::CREATE, array(), null, array(), 'new_path')
                )));

        $this->assertEquals(array(
                    'type_name'=>'CRUDTest',
                    'base_template'=>'base_template',
                    'index_allowed'=>$indexAllowed,
                    'route_prefix'=>'route_prefix',
                    'route_parameters'=>array(
                        'configName'=>'CRUDTest'
                    ),
                    'index_url'=>'index_path',
                    'new_url'=>'new_path',
                    'form_template'=>'form_template',
                ), $configuration->getDefaultViewVars($request));
    }

    public function testGetObjectManager()
    {
        $configuration = $this->createConfiguration(array(
            'object_manager_class'=>'object_manager_class',
            'id_column'=>'id_column',
            'object_manager_options'=>array(
                'option1'=>'value1',
                'option2'=>'value2'
            )));
        $this->objectManagerFactory
                ->expects($this->once())
                ->method('create')
                ->with($this->equalTo(array(
                    'option1'=>'value1',
                    'option2'=>'value2',
                    'id_column'=>'id_column',
                    'class'=>'\Company\TestBundle\Entity\CRUDTest',
                )), $this->equalTo('object_manager_class'))
                ->will($this->returnValue('success'));
        $this->assertEquals('success', $configuration->getObjectManager());
    }
    protected function getMockRequest($parameters)
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
                ->disableOriginalConstructor()
                ->getMock();
        $request
                ->expects($this->any())
                ->method('get')
                ->will($this->returnCallback(function($name, $default=null) use ($parameters) {
                    return isset($parameters[$name]) ? $parameters[$name] : $default;
                }));

        return $request;
    }
    protected function getMockPathGenerator()
    {
        $pathGenerator = $this->getMock('Qimnet\TableBundle\Routing\PathGeneratorInterface');
        $this->pathGeneratorFactory
                ->expects($this->once())
                ->method('create')
                ->will($this->returnValue($pathGenerator));

        return $pathGenerator;
    }

}
