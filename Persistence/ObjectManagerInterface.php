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

/**
 * Interface for CRUD Object managers
 */
interface ObjectManagerInterface
{
    /**
     * Creates an object of the managed class
     *
     * @param array $parameters object creation parameters
     */
    public function create(array $parameters=array());

    /**
     * Finds the objects corresponding to the supplied ids
     *
     * @param  mixed $ids a single id or an array of ids
     * @return array An array of objects
     */
    public function find($ids);

    /**
     * Remove the supplied object
     *
     * @param object $object
     */
    public function remove($object);

    /**
     * Returns the index data
     *
     * @param  string $sortColumn
     * @param  string $sortDirection
     * @return mixed
     */
    public function getIndexData($sortColumn, $sortDirection);

    /**
     * Filters the index data
     *
     * @param mixed  $data
     * @param string $column
     * @param mixed  $value
     * @param array  $options
     */
    public function filterIndexData($data, $column, $value, array $options=array());

    /**
     * Persists the object
     *
     * @param type $entity
     */
    public function persist($object);

    /**
     * Called after objects are persisted or deleted
     */
    public function flush();

    /**
     * Returns true if the supplied object is new
     *
     * @param mixed $object
     */
    public function isNew($object);
}
