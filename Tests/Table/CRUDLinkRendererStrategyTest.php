<?php
/*
 *  This file is part of XXX
 *  (c) Antoine Guigan <aguigan@qimnet.com>
 *  This source file is subject to the MIT license that is bundled
 *  with this source code in the file LICENSE.
 */

namespace Qimnet\CRUDBundle\Tests\Table;
use Qimnet\CRUDBundle\Table\CRUDLinkRendererStrategy;
use Qimnet\CRUDBundle\Configuration\CRUDAction;

/**
 * Description of CRUDLinkRendererStrategyTest
 *
 * @author Antoine Guigan <aguigan@qimnet.com>
 */
class CRUDLinkRendererStrategyTest extends \PHPUnit_Framework_TestCase
{
    protected $CRUDRequest;
    protected $renderer;

    protected function setUp()
    {
        $this->CRUDRequest = $this->getMock('Qimnet\CRUDBundle\HTTP\CRUDRequestInterface');
        $this->renderer = new CRUDLinkRendererStrategy($this->CRUDRequest);
        $this->renderer->setCRUDRequest($CRUDRequest);
    }

    public function testCanRender()
    {
        $this->assertTrue($this->renderer>canRender('any'));
    }

    public function testGetName()
    {
        $this->assertEquals('crud_link', $this->renderer->getName());
    }

    public function testGetPriority()
    {
        $this->assertFalse($this->renderer->getPriority());
    }

    public function getTestRenderData() {
        return array(
            array(array(),array(),false,false),
            array(array(),array(),true,false),
        );
    }

    /**
     * @dataProvider getTestRenderData
     */
    public function testRender($options, $allowedActions, $withShowTemplate, $action)
    {
        $options['object'] = new \stdClass;
        $options['object_vars'] = array(
            'key1'=>'value1',
            'key2'=>'value2'
        );
        $configuration = $this->getMock('Qimnet\CRUDExtensionsBundle\Configuration\CRUDConfigurationInterface');
        $this->CRUDRequest
                ->expects($this->any())
                ->method('getConfiguration')
                ->will($this->returnValue($configuration));
        $securityContext = $this->getMock('Qimnet\CRUDBundle\Security\CRUDSecurityContextInterface');
        $configuration
                ->expects($this->any())
                ->method('getSecurityContext')
                ->will($this->returnValue($securityContext));
        $securityContext->expects($this->any())
                ->method('isActionAllowed')
                ->will($this->returnCallback(function($action) use($allowedActions){
                    return in_array($action, $allowedActions);
                }));
        $configuration->expects($this->any())
                ->method('getShowTemplate')
                ->will($this->returnValue($withShowTemplate ? 'show_template' : null));
        $pathGenerator = $this->getMock(\Qimnet\CRUDBundle\Routing\CRUDPathGeneratorInterface);
        $configuration->expects($this->any())
                ->method('getPathGenerator')
                ->will($this->returnValue($pathGenerator));
        $pathGenerator
                ->expects($this->any())
                ->method('generate')
                ->with($this->equalTo($action), $this->sameAs($options['object']), $this->equalTo($options['object_vars']))
                ->will($this->returnValue('url'));
        $this->assertEquals($action ? '<a href="url">text</a>' : 'text', $this->renderer->render('value', $options));
    }
}
