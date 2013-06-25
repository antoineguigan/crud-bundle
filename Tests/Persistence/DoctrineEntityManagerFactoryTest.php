<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\Tests\Persistence;
use Qimnet\CRUDBundle\Persistence\DoctrineEntityManagerFactory;

class DoctrineEntityManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $doctrine;
    protected $factory;
    protected $options=array('class'=>'class');
    protected $propertyAccessor;

    public function setUp()
    {
        $this->doctrine = $this->getMock('Symfony\Bridge\Doctrine\RegistryInterface');
        $this->propertyAccessor = $this->getMock('Symfony\Component\PropertyAccess\PropertyAccessorInterface');
        $this->factory = new DoctrineEntityManagerFactory($this->doctrine,  $this->propertyAccessor, 'crud_doctrine_entity_manager_default_test_class');
    }
    protected function setMockManagerClass($class)
    {
        $this->getMockForAbstractClass('Qimnet\CRUDBundle\Persistence\DoctrineEntityManager', array(
                $this->doctrine,
                $this->propertyAccessor,
                $this->options
            ), $class);
    }
    public function testCreate()
    {
        $this->setMockManagerClass('crud_doctrine_entity_manager_test_class');
        $this->assertInstanceOf('crud_doctrine_entity_manager_test_class', $this->factory->create($this->options, 'crud_doctrine_entity_manager_test_class'));
    }
    public function testCreateWithDefaultClass()
    {
        $this->setMockManagerClass('crud_doctrine_entity_manager_default_test_class');
        $this->assertInstanceOf('crud_doctrine_entity_manager_default_test_class', $this->factory->create($this->options));
    }
}
