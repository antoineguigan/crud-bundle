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
use Qimnet\CRUDBundle\Configuration\CRUDConfigurationRepository;

class CRUDConfigurationRepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    protected $repository;

    protected function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->repository = new CRUDConfigurationRepository($this->container);
    }

    public function testAdd()
    {
        $object = $this->getMock('stdClass');
        $class = get_class($object);
        $this->repository->add('key1', $class, 'service1', 'worker1', 'redirection_manager1');
        $this->repository->add('key2', 'class2', 'service2','worker2', 'redirection_manager2');
        $this->assertTrue($this->repository->has('key1'));
        $this->assertTrue($this->repository->hasClass($class));
        $this->assertTrue($this->repository->hasForEntity($object));
        $this->assertFalse($this->repository->hasForEntity(new \stdClass()));
        $this->assertFalse($this->repository->has('key3'));
        $this->assertFalse($this->repository->hasClass('class3'));
        $this->repository->add('key3', 'class3','service3','worker3', 'redirection_manager3');
        $this->container->expects($this->any())
                ->method('get')
                ->will($this->returnCallback(function($key){
                    return '_' . $key;
                }));
        $this->assertEquals('_service3', $this->repository->get('key3'));
        $this->assertEquals('_service2', $this->repository->getForClass('class2'));
        $this->assertEquals('_service1', $this->repository->getForClass($class));
        $this->assertEquals('_service1', $this->repository->getForEntity($object));
        $this->assertEquals('_redirection_manager1', $this->repository->getRedirectionManager('key1'));
        $this->assertEquals('_worker1', $this->repository->getWorker('key1'));
    }

}
