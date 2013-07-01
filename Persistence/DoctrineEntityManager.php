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

/**
 * ObjectManagerInterface implementation for doctrine entities
 */
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

    /**
     * Constructor
     *
     * @param RegistryInterface         $doctrine
     * @param PropertyAccessorInterface $propertyAccessor
     * @param array                     $options
     */
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

    /**
     * @inheritdoc
     */
    public function create(array $parameters=array())
    {
        $class = $this->options['class'];
        $object = new $class;
        foreach ($parameters as $key=>$value) {
            $this->propertyAccessor->setValue($object, $key, $value);
        }

        return $object;
    }

    /**
     * @inheritdoc
     */
    public function find($ids)
    {
        return $this->getRepository()->findBy(array($this->options['id_column']=>$ids));
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @inheritdoc
     */
    public function getIndexData($sortColumn, $sortDirection)
    {
        $queryBuilder = call_user_func(array($this->getRepository(),  $this->options['query_builder_method']), $this->options['entity_alias']);
        if (strpos('.',$sortColumn)===false) {
            $sortColumn = $queryBuilder->getRootAlias() . '.' . $sortColumn;
        }
        $queryBuilder->orderBy($sortColumn, $sortDirection);

        return $queryBuilder;
    }

    /**
     * @inheritdoc
     */
    public function persist($entity)
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * @inheritdoc
     */
    public function remove($entity)
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * @inheritdoc
     */
    public function isNew($entity)
    {
        return is_null($this->propertyAccessor->getValue($entity, $this->options['id_column']));
    }

    /**
     * @inheritdoc
     */
    public function filterIndexData($data, $column, $value, array $options = array())
    {
        if (isset($options['column_name'])) {
            $column = $options['column_name'];
        }
        if (isset($options['callback'])) {
            call_user_func($options['callback'], $data, $column, $value, $options);
        } elseif ($value !== '') {
            if (strpos($column,'.')===false) {
                $column = sprintf('%s.%s', $data->getRootAlias(), $column);
            }
            $data->andWhere("$column = :value")
                    ->setParameter('value', $value);
        }
    }

    /**
     * Sets the default options for the manager
     *
     * @param OptionsResolverInterface $resolver
     * @param array                    $options
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver, array $options)
    {
        $resolver->setRequired(array('class'));
        $resolver->setDefaults(array(
            'query_builder_method'=>'createQueryBuilder',
            'entity_alias'=>'t',
            'id_column'=>'id'
        ));
    }

    private function getRepository()
    {
        return $this->doctrine->getManagerForClass($this->options['class'])->getRepository($this->options['class']);
    }

    private function getEntityManager()
    {
        return $this->doctrine->getManagerForClass($this->options['class']);
    }
}
