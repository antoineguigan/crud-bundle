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

use Symfony\Component\Form\Form;

interface FilterBuilderInterface
{
    /**
     * @param  string                 $name
     * @param  string                 $type
     * @param  array                  $options
     * @param  callback               $callback
     * @return FilterBuilderInterface
     */
    public function add($name, $type=null, $options=array(),$filterType="foreignKey");

    /**
     * @return Form
     */
    public function getForm();

    public function getValues();

    public function setValues($values);

    public function setFilters($index_data);
}
