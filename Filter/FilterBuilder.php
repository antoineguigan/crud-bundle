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

use Symfony\Component\Form\FormFactory;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Session\Session;

class FilterBuilder implements FilterBuilderInterface
{
    protected $form;
    protected $defaults;
    protected $filterTypes=array();
    protected $session;
    protected $name;

    public function __construct(FormFactory $factory,
            Session $session,
            $name,
            $defaults=array())
    {
        $this->session = $session;
        $this->name = $name;
        $this->defaults = $defaults;
        $this->form = $factory->createNamedBuilder('crud_filter', 'form', $this->getValues());
    }
    /**
     * @inheritdoc
     */
    public function add($name, $type=null, $options=array(),$filterType="foreignKey")
    {
        $options['required'] = false;
        $this->form->add($name, $type, $options);
        $this->filterTypes[$name] = $filterType;

        return $this;
    }
    /**
     * @inheritdoc
     */
    public function getForm()
    {
        return $this->form->getForm();
    }

    public function getValues()
    {
        return array_replace(
                $this->defaults,
                $this->session->get("qimnet.crud.filter.$this->name", array()));

    }
    public function setValues($values)
    {
        $this->session->set("qimnet.crud.filter.$this->name", $values);
    }

    protected function foreignKeyFilter(QueryBuilder $query, $name, $value)
    {
        $alias = $query->getRootAlias();
        $query->andWhere("$alias.$name=:$name")
            ->setParameter("$name", $value);
    }

    public function setFilters($index_data)
    {
        $values = $this->getValues();
        foreach ($this->filterTypes as $name=>$type) {
            if (isset($values[$name]) && ($values[$name] !== "")) {
                if (is_string($type)) {
                    call_user_func(array($this,$type.'Filter'),$index_data,$name,$values[$name]);
                } else {
                    call_user_func($type, $index_data, $values[$name]);
                }
            }
        }
    }
}
