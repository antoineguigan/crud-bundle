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
use Qimnet\CRUDBundle\Routing\CRUDPathGeneratorFactory;

class CRUDPathGeneratorFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $router;
    protected $propertyAccessor;
    protected $factory;

    protected function setUp()
    {
        $this->router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $this->propertyAccessor = $this->getMock('Symfony\Component\PropertyAccess\PropertyAccessorInterface');
        $this->factory = new CRUDPathGeneratorFactory($this->router, $this->propertyAccessor, 'crud_path_generator_default_test_class');
    }
    protected function setMockPathGeneratorClass($class)
    {
       $this->getMockForAbstractClass('Qimnet\CRUDBundle\Routing\CRUDPathGenerator', array(
                $this->router,
                $this->propertyAccessor,
                'config_name',
                ''
            ), $class);
    }

    public function testCreate()
    {
        $this->setMockPathGeneratorClass('crud_path_generator_test_class');
        $this->assertInstanceOf('crud_path_generator_test_class', $this->factory->create(
                'route_prefix',
                'config_name',
                'id_field',
                'crud_path_generator_test_class'));
    }
    public function testCreateWithDefaultClass()
    {
        $this->setMockPathGeneratorClass('crud_path_generator_default_test_class');
        $this->assertInstanceOf('crud_path_generator_default_test_class', $this->factory->create(
                'route_prefix',
                'config_name'));
    }

}
