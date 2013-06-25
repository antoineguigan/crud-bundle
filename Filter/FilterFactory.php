<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\Filter;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FilterFactory implements FilterFactoryInterface
{
    protected $typeRegistry;
    protected $formFactory;

    public function __construct(
            FilterTypeRegistryInterface $typeRegistry,
            FormFactoryInterface $formFactory)
    {
        $this->typeRegistry = $typeRegistry;
        $this->formFactory = $formFactory;
    }
    public function create(SessionInterface $session, $name, $defaults=array())
    {
        return new FilterBuilder(
                $this->formFactory,
                $session,
                $name,
                $defaults);
    }

    public function createFromType(SessionInterface $session, $type)
    {
        if (is_string($type)) {
            if ($this->typeRegistry->has($type)) {
                $type = $this->typeRegistry->get($type);
            } else {
                $type = new $type;
            }
        }
        $builder = $this->create($session, $type->getName(), $type->getDefaults());
        $type->buildFilter($builder);

        return $builder;
    }
}
