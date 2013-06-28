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

interface ObjectManagerInterface
{
    public function create(array $parameters=array());
    public function find($ids);
    public function remove($entity);
    /**
     * @param int    $page
     * @param string $sortColumn
     * @param string $sortDirection
     */
    public function getIndexData($sortColumn, $sortDirection);
    public function filterIndexData($data, $column, $value, array $options=array());
    public function persist($entity);
    public function flush();
    public function isNew($entity);
}
