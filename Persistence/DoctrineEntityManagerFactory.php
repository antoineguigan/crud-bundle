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
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class DoctrineEntityManagerFactory implements ObjectManagerFactoryInterface
{
    protected $doctrine;
    protected $defaultClass;
    protected $propertyAccessor;
    public function __construct(
            RegistryInterface $doctrine,
            PropertyAccessorInterface $propertyAccessor,
            $defaultClass)
    {
        $this->doctrine = $doctrine;
        $this->defaultClass = $defaultClass;
        $this->propertyAccessor = $propertyAccessor;
    }
    public function create(array $options=array(), $class='')
    {
        if (!$class) {
            $class = $this->defaultClass;
        }

        return new $class($this->doctrine, $this->propertyAccessor, $options);
    }
}
