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

use Qimnet\CRUDBundle\Persistence\DoctrineEntityManager;

class DoctrineEntityManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $doctrine;
    protected $propertyAccessor;
    protected $entityRepository;
    protected $entityManager;

    public function setUp()
    {
        $this->doctrine = $this->getMock('Symfony\Bridge\Doctrine\RegistryInterface');
        $this->propertyAccessor = $this->getMock('Symfony\Component\PropertyAccess\PropertyAccessorInterface');
        $this->entityRepository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
                ->setMethods(array('findBy', 'query_builder_method'))
                ->disableOriginalConstructor()
                ->getMock();
        $this->entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                ->disableOriginalConstructor()
                ->getMock();
    }
    protected function createManager($options=array())
    {
        if (!isset($options['class'])) {
            $options['class'] = 'class';
        }
        $this->doctrine
                ->expects($this->any())
                ->method('getManagerForClass')
                ->with($this->equalTo($options['class']))
                ->will($this->returnValue($this->entityManager));
        $this->entityManager
                ->expects($this->any())
                ->method('getRepository')
                ->with($this->equalTo($options['class']))
                ->will($this->returnValue($this->entityRepository));

        return new DoctrineEntityManager($this->doctrine, $this->propertyAccessor, $options);
    }

    public function testFind()
    {
        $ids = array(1,2,4);
        $manager = $this->createManager(array('id_column'=>'id_column'));
        $this->entityRepository->expects($this->once())
                ->method('findBy')
                ->with($this->equalTo(array('id_column'=>$ids)))
                ->will($this->returnValue('success'));
        $this->assertEquals('success', $manager->find($ids));
    }

    public function testGetIndexData()
    {
        $manager = $this->createManager(array('query_builder_method'=>'query_builder_method'));
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
                ->disableOriginalConstructor()
                ->getMock();
        $this->entityRepository
                ->expects($this->once())
                ->method('query_builder_method')
                ->will($this->returnValue($queryBuilder));
        $queryBuilder
                ->expects($this->any())
                ->method('getRootAlias')
                ->will($this->returnValue('alias'));
        $queryBuilder
                ->expects($this->once())
                ->method('orderBy')
                ->with($this->equalTo('alias.sort_column'), $this->equalTo('sort_direction'));
        $this->assertSame($queryBuilder,$manager->getIndexData('sort_column', 'sort_direction'));
    }

    public function testPersist()
    {
        $entity = new \stdClass();
        $manager = $this->createManager();
        $this->entityManager
                ->expects($this->once())
                ->method('persist')
                ->with($this->identicalTo($entity));
        $manager->persist($entity);
    }
    public function testRemove()
    {
        $entity = new \stdClass();
        $manager = $this->createManager();
        $this->entityManager
                ->expects($this->once())
                ->method('remove')
                ->with($this->identicalTo($entity));
        $manager->remove($entity);
    }
    public function getIsNewData()
    {
        return array(
            array(2,false),
            array(null, true)
        );
    }

    /**
     * @dataProvider getIsNewData
     */
    public function testIsNew($idValue, $isNew)
    {
        $manager = $this->createManager(array('id_column'=>'id_column'));
        $entity = new \stdClass();
        $this->propertyAccessor
                ->expects($this->once())
                ->method('getValue')
                ->with($this->identicalTo($entity), $this->equalTo('id_column'))
                ->will($this->returnValue($idValue));
        $this->assertEquals($isNew, $manager->isNew($entity));
    }

    public function testFlush()
    {
        $this->entityManager
                ->expects($this->once())
                ->method('flush');
        $manager = $this->createManager();
        $manager->flush();
    }

    public function testCreate()
    {
        $mockClass = $this->getMockClass('\StdClass');
        $manager = $this->createManager(array(
                'class'=>$mockClass));
        $this->propertyAccessor
                ->expects($this->once())
                ->method('setValue')
                ->with($this->isInstanceOf($mockClass), $this->equalTo('key1'), $this->equalTo('value1'));
        $this->assertInstanceOf($mockClass, $manager->create(array(
            'key1'=>'value1'
        )));
    }
    public function getTestFilterIndexData()
    {
        return array(
            array('column', 'alias.column'),
            array('t.column', 't.column'),
            array('column', 't.column', array('column_name'=>'t.column')),
            array('column', 't.column', array('callback'=>function($queryBuilder){
                $queryBuilder->andWhere('t.column')
                        ->setParameter('name', 'value');
            }))
        );
    }
    /**
     * @dataProvider getTestFilterIndexData
     */
    public function testFilterIndexData($fieldName, $columnName, $options=array())
    {
        $data = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
                ->disableOriginalConstructor()
                ->getMock();
        $data->expects($this->once())
                ->method('andWhere')
                ->with($this->matchesRegularExpression("/$columnName/"))
                ->will($this->returnValue($data));
        $data->expects($this->any())
                ->method('getRootAlias')
                ->with()
                ->will($this->returnValue('alias'));
        $data->expects($this->once())
                ->method('setParameter')
                ->with($this->anything(),'value')
                ->will($this->returnValue($data));
        $manager = $this->createManager();
        $manager->filterIndexData($data, $fieldName, 'value', $options);
    }
}
