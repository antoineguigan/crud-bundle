<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\Tests\Routing;

use Qimnet\CRUDBundle\Routing\CRUDPathGenerator;
use Qimnet\CRUDBundle\Configuration\CRUDAction;

class CRUDPathGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $router;
    protected $propertyAccessor;
    protected $generator;

    public function getGenerateData()
    {
        return array(
            array(CRUDAction::CREATE),
            array(CRUDAction::DELETE),
            array(CRUDAction::INDEX),
            array(CRUDAction::SHOW),
            array(CRUDAction::UPDATE)
        );
    }

    protected function setUp()
    {
        $this->router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $this->propertyAccessor = $this->getMock('Symfony\Component\PropertyAccess\PropertyAccessorInterface');
        $this->generator = new CRUDPathGenerator($this->router, $this->propertyAccessor, 'route_prefix', 'config_name', 'id_field');
        $this->entity = new \stdClass;
    }
    /**
     * @dataProvider getGenerateData
     */
    public function testGenerate($action)
    {
        $expectedRouteParams = array(
            'param1'=>'value1',
            'configName'=>'config_name'
        );
        switch ($action) {
            case CRUDAction::SHOW :
            case CRUDAction::UPDATE :
            case CRUDAction::DELETE :
                $this->propertyAccessor
                    ->expects($this->once())
                    ->method('getValue')
                    ->with($this->identicalTo($this->entity), $this->equalTo('id_field'))
                    ->will($this->returnValue('id_value'));
                $expectedRouteParams['id'] = 'id_value';
                break;
            default :
                $this->propertyAccessor
                    ->expects($this->never())
                    ->method('getValue');
        }
        switch ($action) {
            case CRUDAction::CREATE :
                $actionName =  'new';
                break;
            case CRUDAction::UPDATE :
                $actionName = 'edit';
                break;
            default :
                $actionName = $action;
        }
        $this->router
                ->expects($this->once())
                ->method('generate')
                ->with($this->equalTo("route_prefix_$actionName"), $this->equalTo($expectedRouteParams))
                ->will($this->returnValue('success'));

        $this->assertEquals('success', $this->generator->generate($action, array('param1'=>'value1'), $this->entity));
    }

    public function getIsNewData()
    {
        return array(
            array(null,true),
            array(1,false)
        );
    }

}
