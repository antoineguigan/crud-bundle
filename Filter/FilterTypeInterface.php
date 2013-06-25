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

interface FilterTypeInterface
{
    public function buildFilter(FilterBuilderInterface $builder);
    public function getName();
    public function getDefaults();
}
