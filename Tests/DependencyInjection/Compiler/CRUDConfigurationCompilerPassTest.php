<?php

/*
 *  This file is part of CRUD bundle
 *  (c) Antoine Guigan <aguigan@qimnet.com>
 *  This source file is subject to the MIT license that is bundled
 *  with this source code in the file LICENSE.
 */

namespace Qimnet\CRUDBundle\Tests\DependencyInjection\Compiler;
use Qimnet\CRUDBundle\DependencyInjection\Compiler\CRUDConfigurationCompilerPass;

/**
 * Description of CRUDConfigurationCompilerPassTest
 *
 * @author Antoine Guigan <aguigan@qimnet.com>
 */
class CRUDConfigurationCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    protected $compiler;

    protected function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
                ->disableOriginalConstructor()
                ->getMock();
        $this->compiler = new CRUDConfigurationCompilerPass;
    }
    public function testNoDefinition()
    {
        $this->container
                ->expects($this->once())
                ->method('hasDefinition')
                ->with($this->equalTo('qimnet.crud.configuration.repository'))
                ->will($this->returnValue(false));
        $this->container
                ->expects($this->never())
                ->method('setDefinition');
        $this->compiler->process($this->container);
    }
    public function testProcess()
    {
        $defaults = array(
            'class'=>null,
            'object_manager_factory'=>null,
            'security_context_factory'=>null,
            'path_generator_factory'=>null,
            'worker'=>'default_worker',
            'redirection_manager'=>'default_redirection_manager',
            'options'=>array()
        );
        $configurations = array(
            'service1'=>array(
                'options'=>array(
                    'object_class'=>'object_class1'
                )
            ),
            'service2'=>array(
                'class'=>'service2',
                'object_manager_factory'=>'object_manager_factory',
                'options'=>array(
                    'object_class'=>'object_class2'
                )
            )
        );
        $this->container
                ->expects($this->once())
                ->method('hasDefinition')
                ->with($this->equalTo('qimnet.crud.configuration.repository'))
                ->will($this->returnValue(true));

        $this->container
                ->expects($this->any())
                ->method('getParameter')
                ->will($this->returnValueMap(array(
                    array('qimnet.crud.defaults', $defaults),
                    array('qimnet.crud.services', $configurations)
                )));

        $this->container
                ->expects($this->exactly(count($configurations)))
                ->method('setDefinition');

        $taggedServices = array(
            'service'=>array(array(
                'alias'=>'alias',
                'object_class'=>'object_class',
                'worker'=>'worker',
                'redirection_manager'=>'redirection_manager'
            ))
        );
        $this->container
                ->expects($this->once())
                ->method('findTaggedServiceIds')
                ->will($this->returnValue($taggedServices));

        $repositoryDefinition =$this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
        $this->container
                ->expects($this->once())
                ->method('getDefinition')
                ->with($this->equalTo('qimnet.crud.configuration.repository'))
                ->will($this->returnValue($repositoryDefinition));
        
        $repositoryDefinition
                ->expects($this->once())
                ->method('addMethodCall')
                ->with($this->equalTo('add'), $this->equalTo(array(
                    'alias',
                    'object_class',
                    'service',
                    'worker',
                    'redirection_manager'
                )));
        $this->compiler->process($this->container);
    }
}
