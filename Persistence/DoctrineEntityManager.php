<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\Persistence;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class DoctrineEntityManager implements ObjectManagerInterface
{
    /**
     * @var RegistryInterface
     */
    protected $doctrine;
    /**
     * @var array
     */
    protected $options;
    /**
     * @var PropertyAccessorInterface
     */
    protected $propertyAccessor;

    public function __construct(
            RegistryInterface $doctrine,
            PropertyAccessorInterface $propertyAccessor,
            array $options)
    {
        $this->doctrine = $doctrine;
        $resolver = new OptionsResolver;
        $this->setDefaultOptions($resolver, $options);
        $this->options = $resolver->resolve($options);
        $this->propertyAccessor = $propertyAccessor;
    }
    protected function setDefaultOptions(OptionsResolverInterface $resolver, array $options)
    {
        $resolver->setRequired(array('class'));
        $resolver->setDefaults(array(
            'query_builder_method'=>'createQueryBuilder',
            'entity_alias'=>'t',
            'id_column'=>'id'
        ));
    }

    public function create(array $parameters=array())
    {
        $class = $this->options['class'];
        $object = new $class;
        foreach ($parameters as $key=>$value) {
            $this->propertyAccessor->setValue($object, $key, $value);
        }

        return $object;
    }

    protected function getRepository()
    {
        return $this->doctrine->getManagerForClass($this->options['class'])->getRepository($this->options['class']);
    }

    protected function getEntityManager()
    {
        return $this->doctrine->getManagerForClass($this->options['class']);
    }
    public function find($ids)
    {
        return $this->getRepository()->findBy(array($this->options['id_column']=>$ids));
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    public function getIndexData($sortColumn, $sortDirection)
    {
        $queryBuilder = call_user_func(array($this->getRepository(),  $this->options['query_builder_method']), $this->options['entity_alias']);
        $queryBuilder->orderBy($sortColumn, $sortDirection);

        return $queryBuilder;
    }

    public function persist($entity)
    {
        $this->getEntityManager()->persist($entity);
    }

    public function remove($entity)
    {
        $this->getEntityManager()->remove($entity);
    }

    public function isNew($entity)
    {
        return is_null($this->propertyAccessor->getValue($entity, $this->options['id_column']));
    }
}
