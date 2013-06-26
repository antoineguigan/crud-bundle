<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\Routing;

interface CRUDPathGeneratorInterface
{
    public function generate($action, array $parameters=array(), $object = null, array $objectVars = array());
}
