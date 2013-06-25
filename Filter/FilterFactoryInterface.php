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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

interface FilterFactoryInterface
{
    public function create(SessionInterface $session, $name, $defaults=array());
    public function createFromType(SessionInterface $session, $type);
}
